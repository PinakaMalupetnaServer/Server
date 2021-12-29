<?php

namespace Clarence2810\Teleport;

use Clarence2810\Teleport\teleport;
use pocketmine\{
    Player,
    scheduler\Task,
    utils\TextFormat as C,
    plugin\PluginBase,
};


class HubTask extends Task
{
    public $timer = 4;
    public function __construct(teleport $main, $playerName){
        $this->main = $main;
        $this->playerName = $playerName;
    }

    public function onRun(int $currentTick)
    {
        $player = $this->main->getServer()->getPlayerExact($this->playerName);
        $level = $this->main->getServer()->getLevelByName("lobby");
        if ($player instanceof Player) {
			if(in_array($player->getName(), teleport::$cancel)) $this->main->getScheduler()->cancelTask($this->getTaskId());
             $this->main->getServer()->loadLevel("lobby");
            /* if (!$this->main->getServer()->isLevelLoaded("lobby")){
                $player->sendMessage("Lol xD, Something went wrong...");
                $this->main->getLogger()->alert("World 'lobby' is not Loaded properly, load it using /mw load lobby");
                $this->main->getScheduler()->cancelTask($this->getTaskId());
                return false;
            } */ // Ignored due to world lobby is loaded during startup!
            $this->timer--;
            $player->sendMessage(C::WHITE . "Please wait.");
            if ($this->timer <= 0) {
                $player->getInventory()->clearAll();
		        $player->getArmorInventory()->clearAll();
                $player->teleport($level->getSafeSpawn());
				$player->setGamemode(2);
				$player->setGamemode(2);
				$player->setFood($player->getMaxFood());
                $this->main->getScheduler()->cancelTask($this->getTaskId());
                $player->sendTip(C::WHITE . "Welcome back!");
			}
		}
        else {
            $this->main->getScheduler()->cancelTask($this->getTaskId());
            return false;
        }
	}
}
