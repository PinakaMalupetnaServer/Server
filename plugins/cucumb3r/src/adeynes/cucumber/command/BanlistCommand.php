<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\cucumber\mod\Ban;
use adeynes\cucumber\utils\Queries;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use pocketmine\command\CommandSender;

class BanlistCommand extends PunishmentListCommand
{

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'banlist',
            'cucumber.command.banlist',
            'See the list of bans',
            '/banlist [page] [-all|-a]'
        );
    }

    protected function isAllable(): bool
    {
        return true;
    }

    protected function getSelectQuery(): string
    {
        return Queries::CUCUMBER_GET_PUNISHMENTS_BANS_LIMITED;
    }

    protected function getCountQuery(): string
    {
        return Queries::CUCUMBER_GET_PUNISHMENTS_BANS_COUNT;
    }

    /**
     * @param array $ban_row The database representation of a ban
     * @return string
     */
    protected function makeBanInfoLine(array $ban_row): string {
        return $this->getPlugin()->formatMessageFromConfig(
            'success.banlist.list',
            Ban::from($ban_row)->getFormatData()
        );
    }

    protected function sendList(CommandSender $sender, array $bans, int $count, int $page_number): void {
        $page = trim(
            $this->getPlugin()->formatMessageFromConfig(
                'success.banlist.intro',
                ['page' => strval($page_number), 'count' => strval($count)]
            ) . PHP_EOL .
            implode(PHP_EOL, array_map([$this, 'makeBanInfoLine'], $bans))
        );
        $sender->sendMessage($page);
    }

}