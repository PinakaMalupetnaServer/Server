<?php
declare(strict_types=1);

namespace adeynes\cucumber\command;

use adeynes\cucumber\Cucumber;
use adeynes\parsecmd\command\blueprint\CommandBlueprint;
use adeynes\parsecmd\command\ParsedCommand;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\Player;

class VanishCommand extends CucumberCommand
{

    protected const STATUSES = [true => 'on', false => 'off'];

    /**
     * @var bool[]
     */
    protected static $vanished = [];

    public function __construct(Cucumber $plugin, CommandBlueprint $blueprint)
    {
        parent::__construct(
            $plugin,
            $blueprint,
            'vanish',
            'cucumber.command.vanish',
            'Vanish from other player\'s sight',
            '/vanish'
        );
    }

    public function _execute(CommandSender $sender, ParsedCommand $command): bool
    {
        if (!$sender instanceof Player) {
            $this->getPlugin()->formatAndSend($sender, 'error.not-ingame');
            return false;
        }

        $old_vanished = self::isVanished($sender);
        $new_vanished = !$old_vanished;
        self::setVanished($sender, $new_vanished);
        self::$vanished[$sender->getLowerCaseName()] = $new_vanished;

        $this->getPlugin()->formatAndSend($sender, 'success.vanish', ['status' => self::STATUSES[$new_vanished]]);
        return true;
    }

    public static function setVanished(Player $player, bool $vanish = true): void
    {
        $player->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, $vanish);
        $player->setNameTagVisible(!$vanish);
    }

    public static function isVanished(Player $player): bool
    {
        $name = $player->getLowerCaseName();
        return isset(self::$vanished[$name]) && self::$vanished[$name];
    }

}