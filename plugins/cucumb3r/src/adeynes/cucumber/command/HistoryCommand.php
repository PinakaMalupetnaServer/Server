<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\mod\Ban;
use adeynes\cucumber\mod\IpBan;
use adeynes\cucumber\mod\Mute;
use adeynes\cucumber\mod\Warning;
use adeynes\cucumber\utils\Formattable;
use adeynes\cucumber\utils\HasTimeOfCreation;
use adeynes\cucumber\utils\Queries;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use adeynes\parsecmd\command\ParsedCommand;
use pocketmine\command\CommandSender;

class HistoryCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'history',
            'cucumber.command.history',
            'See a player\'s punishment history',
            '/history <player>'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        $player = strtolower($command->get(['player'])[0]);

        $processAndSendHistory = function (array $player_rows) use ($sender) {
            $player = $player_rows[0];
            /** @var Formattable[]&HasTimeOfCreation[] $history */
            $history = [];

            $this->addBansToHistory(
                $player['name'],
                $history,
                function (array &$history) use ($player, $sender) {
                    $this->addIpBansToHistory(
                        $player['ip'],
                        $history,
                        function (array &$history) use ($player, $sender) {
                            $this->addMutesToHistory(
                                $player['name'],
                                $history,
                                function (array &$history) use ($player, $sender) {
                                    $this->addWarningsToHistory(
                                        $player['name'],
                                        $history,
                                        function (array $history) use ($player, $sender) {
                                            $this->showHistory($sender, $history, $player['name']);
                                        }
                                    );
                                }
                            );
                        }
                    );
                }
            );
        };

        $this->doIfTargetExists($processAndSendHistory, $sender, $player);

        return true;
    }

    /**
     * @param CommandSender $sender
     * @param Formattable[]&HasTimeOfCreation[] $history
     * @param string $player_name
     */
    protected function showHistory(CommandSender $sender, array $history, string $player_name) {
        usort(
            $history,
            function ($a, $b) {
                /** @var Formattable&HasTimeOfCreation $a */
                /** @var Formattable&HasTimeOfCreation $b */
                return ($a->getTimeOfCreation() <=> $b->getTimeOfCreation()) * -1; // desc order
            }
        );

        $this->getPlugin()->getConnector()->executeSelect(
            Queries::CUCUMBER_GET_PLAYER_BY_NAME,
            ['name' => $player_name],
            function (array $rows) use ($sender, $history, $player_name) {
                $this->getPlugin()->formatAndSend(
                    $sender,
                    'success.history.intro',
                    [
                        'player' => $player_name,
                        'count' => strval(count($history)),
                        'first_joined' => date($this->getPlugin()->getMessage('time-format'), $rows[0]['first_join']),
                        'last_joined' => date($this->getPlugin()->getMessage('time-format'), $rows[0]['last_join'])
                    ]
                );
                $lines = [];
                foreach ($history as $punishment) {
                    $lines[] = $this->getPlugin()->formatMessageFromConfig(
                        $punishment->getMessagesPath(),
                        $punishment->getFormatData()
                    );
                }
                $sender->sendMessage(implode(PHP_EOL, $lines));
            }
        );
    }

    protected function addBansToHistory(string $player, array &$history, ?callable $next): void
    {
        $this->getPlugin()->getConnector()->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_BANS_BY_PLAYER,
            ['player' => $player],
            function (array $rows) use (&$history, $next) {
                foreach ($rows as $row) {
                    $history[] = Ban::from($row);
                }

                if (!is_null($next)) $next($history);
            }
        );
    }

    protected function addIpBansToHistory(string $ip, array &$history, ?callable $next): void
    {
        $this->getPlugin()->getConnector()->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_IP_BANS_BY_IP,
            ['ip' => $ip],
            function (array $rows) use (&$history, $next) {
                foreach ($rows as $row) {
                    $history[] = IpBan::from($row);
                }

                if (!is_null($next)) $next($history);
            }
        );
    }

    protected function addMutesToHistory(string $player, array &$history, ?callable $next): void
    {
        $this->getPlugin()->getConnector()->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_MUTES_BY_PLAYER,
            ['player' => $player],
            function (array $rows) use (&$history, $next) {
                foreach ($rows as $row) {
                    $history[] = Mute::from($row);
                }

                if (!is_null($next)) $next($history);
            }
        );
    }

    protected function addWarningsToHistory(string $player, array &$history, ?callable $next): void
    {
        $this->getPlugin()->getConnector()->executeSelect(
            Queries::CUCUMBER_GET_PUNISHMENTS_WARNINGS_BY_PLAYER,
            ['player' => $player, 'all' => true],
            function (array $rows) use (&$history, $next) {
                foreach ($rows as $row) {
                    $history[] = new class($row['warning_id'], Warning::from($row)) implements Formattable, HasTimeOfCreation {
                        /** @var int */
                        private $id;

                        /** @var Warning */
                        private $warning;

                        public function __construct(int $id, Warning $warning)
                        {
                            $this->id = $id;
                            $this->warning = $warning;
                        }

                        public function getTimeOfCreation(): int
                        {
                            return $this->warning->getTimeOfCreation();
                        }

                        public function getFormatData(): array
                        {
                            return $this->warning->getFormatData() + ['id' => strval($this->id)];
                        }

                        public function getMessagesPath(): string
                        {
                            return $this->warning->getMessagesPath();
                        }
                    };
                }

                if (!is_null($next)) $next($history);
            }
        );
    }

}