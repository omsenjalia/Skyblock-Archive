<?php

namespace SkyBlock\tasks;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use SkyBlock\Main;

class BroadcastTask extends Task {
    private int $length;

    public function __construct() {
        $this->length = -1;
    }

    public function onRun() : void {
        $this->length = $this->length + 1;
        $bc = Main::getInstance()->bc->getAll();
        $messages = $bc["messages"];
        shuffle($messages);
        $messagekey = $this->length;
        $message = $messages[$messagekey];
        if ($this->length == count($messages) - 1) $this->length = -1;
        Server::getInstance()->broadcastMessage("§a§l»>\n" . Main::getInstance()->translateColors("&", $message) . "\n§a§l»>");
    }
}
