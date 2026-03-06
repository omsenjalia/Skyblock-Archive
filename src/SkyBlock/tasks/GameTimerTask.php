<?php

namespace SkyBlock\tasks;

use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use SkyBlock\Arena;

class GameTimerTask extends Task {
    private Arena $arena;

    private int $time;

    /**
     * @param Arena $arena
     */
    public function __construct(Arena $arena) {
        $this->arena = $arena;
        $this->time = 10 * 60;
    }

    public function onRun() : void {
        $time = $this->time--;
        if ($time < 1) {
            $this->arena->timeOver();
            $this->getHandler()?->cancel();
            return;
        }
        if (!empty($this->arena->players)) {
            $max = min($this->arena->players);
            $this->arena->sendTip(TextFormat::BOLD . TextFormat::RED . "(!) " . TextFormat::GREEN . "KOTH time left: " . gmdate("i:s", $time) . "\nCurrent highest: " . array_search($max, $this->arena->players, true));
            $this->arena->checkPlayers();
        }
    }


}