<?php


namespace princepines\ChristmasPMnS;

use pocketmine\scheduler\Task;
use princepines\ChristmasPMnS\FireworkTask;

class LaunchTask extends Task
{

    public function __construct(main $main, $playerName)
    {
        $this->main = $main;
        $this->playerName = $playerName;
    }

    public $timer = 889;
    public $tasks = [];

    /**
     * @inheritDoc
     */
    public function onRun(int $currentTick)
    {
        $player = $this->main->getServer()->getPlayerExact($this->playerName);
        $this->timer--;
        if ($this->timer == 878) {
            $player->sendMessage("Fireworks Starting.");
            $task = new FireworkTask();
            $this->tasks[$player->getId()] = $task;
            $this->main->getScheduler()->scheduleDelayedRepeatingTask($task, 40, 20);
        } elseif ($this->timer == 777) {
            $task = $this->tasks[$player->getId()];
            unset($this->tasks[$player->getId()]);
            $task->getHandler()->cancel();
            $player->sendMessage("second song instance.");
            $task = new FireworkTask();
            $this->tasks[$player->getId()] = $task;
            $this->main->getScheduler()->scheduleDelayedRepeatingTask($task, 80, 20);
        } elseif ($this->timer == 625) {
            $task = $this->tasks[$player->getId()];
            unset($this->tasks[$player->getId()]);
            $task->getHandler()->cancel();
            $player->sendMessage("third song instance.");
            $task = new FireworkTask();
            $this->tasks[$player->getId()] = $task;
            $this->main->getScheduler()->scheduleDelayedRepeatingTask($task, 50, 20);
        } elseif ($this->timer == 532) {
            $task = $this->tasks[$player->getId()];
            unset($this->tasks[$player->getId()]);
            $task->getHandler()->cancel();
            $player->sendMessage("slow song instance.");
            $task = new FireworkTask();
            $this->tasks[$player->getId()] = $task;
            $this->main->getScheduler()->scheduleDelayedRepeatingTask($task, 120, 20);
        } elseif ($this->timer <= 1) {
            $player->sendMessage("Fireworks Stopping.");
            $task = $this->tasks[$player->getId()];
            unset($this->tasks[$player->getId()]);
            $task->getHandler()->cancel();
        }
    }
}
