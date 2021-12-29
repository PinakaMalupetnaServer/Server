<?php

namespace Clarence2810\Teleport;

use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\{command\Command, command\CommandSender, event\Listener, Player, plugin\PluginBase,};

;

class teleport extends PluginBase implements Listener
{
    public static $cancel = [];
    public $cooldown = [];

    public function onEnable()
    {
        $this->getServer()->getpluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("Plugin by Clarence2810 has been enabled");
        $this->getLogger()->info("Contributors: princepines");

    }

    public function onDisable()
    {
        $this->getLogger()->info("Plugin by Clarence2810 has been disabled");
        $this->getLogger()->info("Contributors: princepines");
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool
    {
        switch ($cmd->getName()) {
            case "hub":
                if ($sender instanceof Player) {
                    $this->getScheduler()->scheduleRepeatingTask(new HubTask($this, $sender->getName()), 20);
                }
                break;
            case "event":
                if ($sender instanceof Player) {
                    $this->getScheduler()->scheduleRepeatingTask(new EventsTask($this, $sender->getName()), 20);
                }
                break;
            case "pvp":
                if ($sender instanceof Player) {
                    $this->getScheduler()->scheduleRepeatingTask(new PvPTask($this, $sender->getName()), 20);
                }
                break;
            case "survival":
                if ($sender instanceof Player) {
                    $this->getScheduler()->scheduleRepeatingTask(new SMTask($this, $sender->getName()), 20);
                }
                break;
        }
        return true;
    }
}