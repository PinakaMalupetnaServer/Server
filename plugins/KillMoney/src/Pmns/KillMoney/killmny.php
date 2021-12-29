<?php

namespace Pmns\KillMoney;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Event;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\player\PlayerDeathEvent as PD;
use pocketmine\event\entity\EntityDamageByEntityEvent as EDBE;
use pocketmine\utils\TextFormat;

class killmny extends PluginBase implements Listener
{

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onDie(PD $e)
    {
        $eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        $p = $e->getPlayer();
        if ($e->getPlayer()->getLastDamageCause() instanceof EDBE) {
            if ($e->getPlayer()->getLastDamageCause()->getDamager() instanceof Player) {
                $killer = $p->getLastDamageCause()->getDamager();
                $mny = $eco->myMoney($p);
                if ($mny < 5) {
                    //do Nothing
                    return false;
                }
                $amo = mt_rand(1, $mny);
                $eco->addMoney($killer, $amo);
                $eco->reduceMoney($p, $amo);
                $killer->sendMessage(TextFormat::GREEN . "+" . $amo . " money\n" . TextFormat::GREEN . "for killing " . TextFormat::GREEN . $p->getName());
                $p->sendMessage(TextFormat::RED . "-" . $amo . " money\n" . TextFormat::RED . "killed by " . TextFormat::RED . $killer->getName());
            }
        }
    }
}
