<?php

namespace Matthww\NoHunger;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;

class NoHunger extends PluginBase implements Listener {

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("This Plugin is modified for PMnS,");
        $this->getLogger()->info("modified by princepines, Clarence1028");
    }

    public function Hunger(PlayerExhaustEvent $exhaustEvent) {
        $player = $exhaustEvent->getPlayer();
        $level = $player->getLevel();
            if ($level->getName() === "lobby") {
                $exhaustEvent->setCancelled(true);
            }
            else{
                $exhaustEvent->setCancelled(false);
        }
    }

}
