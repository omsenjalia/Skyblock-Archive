<?php

namespace SkyBlock\tasks;

use pocketmine\scheduler\Task;
use SkyBlock\Main;

class MinuteTask extends Task {

    private Main $plugin;

    /**
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onRun() : void {
        --$this->plugin->envoyTimer; // envoy time
        if ($this->plugin->envoyTimer <= 0) {
            $this->plugin->envoyTimer = -1;
        }
        --$this->plugin->droppartyTimer; // drop party time
        if ($this->plugin->droppartyTimer <= 0) {
            $this->plugin->droppartyTimer = -1;
        }
    }

}