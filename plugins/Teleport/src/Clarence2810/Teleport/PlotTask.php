<?php

namespace Clarence2810\Teleport;

use Clarence2810\Teleport\teleport;
use pocketmine\{
    Player,
    scheduler\Task,
    utils\TextFormat as C,
    plugin\PluginBase,
};
use pocketmine\inventory\InventoryBase;

;

class PlotTask extends Task
{
    public $timer = 4;
    public $creative = 5;
    public function __construct(teleport $main, $playerName){
        $this->main = $main;
        $this->playerName = $playerName;
    }

    public function onRun(int $currentTick)
    {
        $player = $this->main->getServer()->getPlayerExact($this->playerName);
        $level = $this->main->getServer()->getLevelByName("Disney");
        if ($player instanceof Player) {
			if(in_array($player->getName(), teleport::$cancel)) $this->main->getScheduler()->cancelTask($this->getTaskId());
            $this->main->getServer()->loadLevel("Disney");
            if (!$this->main->getServer()->isLevelLoaded("Disney")){
                $player->sendMessage("Lol xD, Something went wrong...");
                $this->main->getLogger()->alert("World 'Disney' not Loaded properly, load it using /mw load Disney");
                $this->main->getScheduler()->cancelTask($this->getTaskId());
                return false;
            }
            $player->addTitle(C::YELLOW . "Going to Plots...");
			$this->timer--;
			$this->creative--;
            $player->sendSubtitle(C::WHITE . "in " . $this->timer);
            if ($this->timer <= 0) {
                $player->teleport($level->getSafeSpawn());
				$player->getInventory()->clearAll();
                $player->addTitle(C::BOLD . C::YELLOW . "Plots");
                $player->sendTip(C::WHITE . "You have been teleported to Plots!");
                if($this->creative <= 0){
					$player->setGamemode(1);
                    $this->main->getScheduler()->cancelTask($this->getTaskId());
                }
            }
        }
       else {
            $this->main->getScheduler()->cancelTask($this->getTaskId());
            return false;
        }
    }
}