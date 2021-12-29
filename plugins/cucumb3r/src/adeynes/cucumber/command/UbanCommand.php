<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\mod\UBan;
use adeynes\cucumber\utils\CucumberException;
use adeynes\cucumber\utils\CucumberPlayer;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use adeynes\parsecmd\command\ParsedCommand;
use pocketmine\command\CommandSender;

use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use CortexPE\DiscordWebhookAPI\Embed;

class UbanCommand extends CucumberCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'uban',
            'cucumber.command.uban',
            'Ban any player that joins using an IP. Irreversible',
            '/uban <player>|<ip> [reason]'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        [$target, $reason] = $command->get(['target', 'reason']);
        if ($reason === null) {
            $reason = $this->getPlugin()->getMessage('moderation.ban.default-reason');
        }

        $uban = function(string $ip) use ($sender, $reason) {
            try {
                $uban = new UBan($ip, $reason, $sender->getName(), time());
                $uban_data = $uban->getFormatData();
                $this->getPlugin()->getPunishmentRegistry()->addUBan($uban);
                $uban->save($this->getPlugin()->getConnector());

                foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $player) {
                    if ($this->getPlugin()->getPunishmentRegistry()->getUBanChecker()->checkFor($player)) {
                        $player->kick(
                            $this->getPlugin()->formatMessageFromConfig('moderation.ban.message', $uban_data),
                            false // don't say Kicked by admin
                        );
                    }
                }

                $this->getPlugin()->formatAndSend($sender, 'success.uban', $uban_data);

                // send details on discord server
                $whook = $this->getPlugin()->getConfig()->get('webh');
                $webhook = new Webhook($whook);

                $msg = new Message();
                $msg->setUsername("cucumBAN");
                $msg->setAvatarURL("https://th.bing.com/th/id/R.3e31457af0eba4508a0f69e2aa4415f8?rik=okgaal1d19EDsg&riu=http%3a%2f%2fpngimg.com%2fuploads%2fcucumber%2fcucumber_PNG84281.png&ehk=1SM1m9pziiqKralyNFy2tsj4Hp%2fBWelIZK8Y2BVqG5s%3d&risl=&pid=ImgRaw&r=0");
                $msg->setContent("");

                $embed = new Embed();
                $embed->setTitle("UNBANNED");
                $embed->setColor(0x00FF00);
                $embed->setDescription("[Someone's IP] is now unbanned by " . $sender->getName() . " for " . $reason);
                $embed->setFooter("🥒 by princepines and adeynes");
                $msg->addEmbed($embed);

                $webhook->send($msg);
                return true;
            } catch (CucumberException $exception) {
                $sender->sendMessage($exception->getMessage());
                return false;
            }
        };

        $ip_matches = [];
        preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $target, $ip_matches);
        if ($ip_matches === []) {
            if ($player = CucumberPlayer::getOnlinePlayer($target)) {
                $uban($player->getAddress());
            } else {
                $this->doIfTargetExists(
                    function (array $rows) use ($uban) {
                        $uban($rows[0]['ip']);
                    },
                    $sender,
                    $target
                );
            }
        } else {
            $uban($ip_matches[0]);
        }

        /*if ($target_name) {
            if ($target = CucumberPlayer::getOnlinePlayer($target_name)) {
                $ip_ban($target->getAddress());
            } else {
                $this->doIfTargetExists(
                    function (array $rows) use ($ip_ban) {
                        $ip_ban($rows[0]['ip']);
                    },
                    $sender,
                    $target_name
                );
            }
            // don't return in case ip flag is set
        }

        if ($ip) {
            $ip_ban($ip);
        }

        if (!$target_name && !$ip) {
            $sender->sendMessage(
                MessageFactory::colorize("&cAt least one of flag &b-p &cand flag &b-ip&c must be set!")
            );
        }*/

        return true;
    }

}
