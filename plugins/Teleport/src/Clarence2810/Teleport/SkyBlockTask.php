<?php

namespace Clarence2810\Teleport;

use Clarence2810\Teleport\teleport;
use pocketmine\{
    Player,
    scheduler\Task,
    utils\TextFormat as C,
    plugin\PluginBase,
};

;

class SkyBlockTask extends Task
{
    public $timer = 4;
    public function __construct(teleport $main, $playerName){
        $this->main = $main;
        $this->playerName = $playerName;
    }

    public function onRun(int $currentTick)
    {
        $player = $this->main->getServer()->getPlayerExact($this->playerName);
        $level = $this->main->getServer()->getLevelByName("skai");
        if ($player instanceof Player) {
			if(in_array($player->getName(), teleport::$cancel)) $this->main->getScheduler()->cancelTask($this->getTaskId());
            $this->main->getServer()->loadLevel("skai");
            if (!$this->main->getServer()->isLevelLoaded("skai")){
                $player->sendMessage("Lol xD, Something went wrong...");
                $this->main->getLogger()->alert("World 'Top1' not Loaded properly, load it using /mw load skai");
                $this->main->getScheduler()->cancelTask($this->getTaskId());
                return false;
            }
            $player->addTitle(C::YELLOW . "Going to SkyBlock...");
            $this->timer--;
            $player->addSubtitle(C::WHITE . "in " . $this->timer);
            if ($this->timer <= 0) {
                $player->teleport($level->getSafeSpawn());
                $this->main->getScheduler()->cancelTask($this->getTaskId());
                $player->addTitle(C::BOLD . C::AQUA . "SkyBlock");
                $player->sendTip(C::WHITE . "You have been teleported to SkyBlock!");
            }
            if (!$player->hasPermission("teleport.nogm")) {
                $player->setGamemode(2);
            }
        }
        else {
            $this->main->getScheduler()->cancelTask($this->getTaskId());
            return false;
        }
    }
}
