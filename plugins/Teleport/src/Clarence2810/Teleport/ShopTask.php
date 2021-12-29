<?php

namespace Clarence2810\Teleport;

use Clarence2810\Teleport\teleport;
use pocketmine\{
    Player,
    scheduler\Task,
    utils\TextFormat as C,
    plugin\PluginBase,
    level\Position,
};

;

class ShopTask extends Task
{
    public $timer = 2;
    public function __construct(teleport $main, $playerName){
        $this->main = $main;
        $this->playerName = $playerName;
    }

    public function onRun(int $currentTick)
    {
        $player = $this->main->getServer()->getPlayerExact($this->playerName);
        $level = $this->main->getServer()->getLevelByName("server-spawn");
        if ($player instanceof Player) {
            if(in_array($player->getName(), teleport::$cancel)) $this->main->getScheduler()->cancelTask($this->getTaskId());
            $this->main->getServer()->loadLevel("server-spawn");
            if (!$this->main->getServer()->isLevelLoaded("server-spawn")){
                $player->sendMessage("Lol xD, Something went wrong...");
                $this->main->getLogger()->alert("World 'server-spawn' not Loaded properly, load it using /mw load POTPVP");
                $this->main->getScheduler()->cancelTask($this->getTaskId());
                return false;
            }
            $player->addTitle(C::YELLOW . "Going to Shop...");
            $this->timer--;
            $player->sendSubtitle(C::WHITE . "in " . $this->timer);
            if ($this->timer <= 0) {
                $loc = new Position(-1717, 72, -221, $level);
                $player->teleport($loc);
                $player->setGamemode(0);
                $player->setGamemode(0);
                $this->main->getScheduler()->cancelTask($this->getTaskId());
                $player->sendTip(C::BOLD . C::RED . "Welcome to Shop");
            }
        }
        else {
            $this->main->getScheduler()->cancelTask($this->getTaskId());
            return false;
        }
    }
}