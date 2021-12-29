<?php

namespace AFD;

use pocketmine\plugin\PluginBase;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
class main extends PluginBase implements Listener
{

    public function OnEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("Anti Fall Damage - ENABLE");
        $this->getLogger()->alert("Modified by princepines");
    }

    public function OnDisable()
    {
        $this->getLogger()->info("Anti Fall Damage - DISABLE");
        $this->getLogger()->alert("Modified by princepines");
    }

    public function onDamage(EntityDamageEvent $event)
    {
        $player = $this->getServer()->getPlayer();
        $level = $player->getLevel();
        if ($level->getName() === "lobby") {
            if ($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
                $event->setCancelled();
            }
        }
        elseif ($level->getName() === "Event") {
            if ($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
                $event->setCancelled();
            }
        }
    }
}
