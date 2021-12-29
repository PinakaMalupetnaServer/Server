<?php

namespace Clarence2810\Teleport;

use Clarence2810\Teleport\teleport;
use pocketmine\{
    Player,
    scheduler\Task,
    utils\TextFormat as C,
    plugin\PluginBase,
};
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

;

class KPvPTask extends Task
{
    public function __construct(teleport $main, $playerName){
        $this->main = $main;
        $this->playerName = $playerName;
    }

    public function onRun(int $currentTick)
    {
        $player = $this->main->getServer()->getPlayerExact($this->playerName);
		$name = $player->getName();
        $level = $this->main->getServer()->getLevelByName("world");
        if ($player instanceof Player) {
			if(in_array($player->getName(), teleport::$cancel)) $this->main->getScheduler()->cancelTask($this->getTaskId());
            $this->main->getServer()->loadLevel("world");
            if (!$this->main->getServer()->isLevelLoaded("world")){
                $player->sendMessage("Lol xD, Something went wrong...");
                $this->main->getLogger()->alert("World 'world' not Loaded properly, load it using /mw load under");
                $this->main->getScheduler()->cancelTask($this->getTaskId());
                return false;
            }
            $player->teleport($level->getSafeSpawn());
			$player->setGamemode(0);
			$player->setGamemode(0);
            $this->main->getScheduler()->cancelTask($this->getTaskId());
			$this->main->getServer()->dispatchCommand(new ConsoleCommandSender(), "rca " . $name . " " ."wild");
            $player->sendMessage("You have been teleported elsewhere");
        }
        else {
            $this->main->getScheduler()->cancelTask($this->getTaskId());
            return false;
        }
    }
}
