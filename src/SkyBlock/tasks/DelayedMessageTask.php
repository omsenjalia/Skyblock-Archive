<?php

namespace SkyBlock\tasks;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use SkyBlock\Main;

class DelayedMessageTask extends Task {

    private string $message;
    private Player $player;

    public function __construct(string $message, Player $player, int $ticks = 20) {
        $this->message = $message;
        $this->player = $player;
        Main::getInstance()->getScheduler()->scheduleDelayedTask($this, $ticks);
    }

    public function onRun() : void {
        if ($this->player->isOnline()) {
            $this->player->sendMessage($this->message);
        }
    }

}