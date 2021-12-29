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

class SMTask extends Task
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
        if ($player instanceof Player) {
            if (in_array($player->getName(), teleport::$cancel)) $this->main->getScheduler()->cancelTask($this->getTaskId());
            $player->sendTip(C::WHITE . "Please wait.");
            $this->timer--;
            if ($this->timer <= 0) {
                $player->transfer("gsrv.princepines.digital", 19130, $player->getName() . ": Transferred to SM");
            }
        } else {
            $this->main->getScheduler()->cancelTask($this->getTaskId());
            return false;
        }
        return 0;
    }
}