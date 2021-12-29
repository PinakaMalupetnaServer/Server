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

class EventsTask extends Task
{
    public $timer = 4;

    public function __construct(teleport $main, $playerName)
    {
        $this->main = $main;
        $this->playerName = $playerName;
    }

    public function onRun(int $currentTick)
    {
        $player = $this->main->getServer()->getPlayerExact($this->playerName);
        $level = $this->main->getServer()->getLevelByName("Event");
        if ($player instanceof Player) {
            if (in_array($player->getName(), teleport::$cancel)) $this->main->getScheduler()->cancelTask($this->getTaskId());
            $this->main->getServer()->loadLevel("Event");
            if (!$this->main->getServer()->isLevelLoaded("Event")) {
                $player->sendMessage("Something went wrong, please report it to princepines");
                $this->main->getLogger()->alert("World 'Event' not Loaded properly, load it using /mw load Events");
                $this->main->getScheduler()->cancelTask($this->getTaskId());
                return false;
            }
            $this->timer--;
            $player->sendMessage(C::WHITE . "Going to Events..");
            if ($this->timer <= 0) {
                // $loc = new Position(-1736, 86, -202, $level);
                $player->teleport($level->getSafeSpawn());
                $player->setGamemode(0);
                $player->setGamemode(0);
                $this->main->getScheduler()->cancelTask($this->getTaskId());
            }
        } else {
            $this->main->getScheduler()->cancelTask($this->getTaskId());
            return false;
        }
    }
}
