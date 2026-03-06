<?php

namespace SkyBlock\tasks;

use pocketmine\scheduler\Task;
use SkyBlock\Main;

class DropPartyTask extends Task {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onRun() : void {
        $this->getPlugin()->getServer()->broadcastMessage("§f§l[DropParty]§b> §r§bDropParty has started! PVP enabled! Do /warp dropparty to join!");
        $this->getPlugin()->status = "enabled";
    }

    public function getPlugin() : Main {
        return $this->plugin;
    }
}
