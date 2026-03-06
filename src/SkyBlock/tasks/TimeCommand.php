<?php

namespace SkyBlock\tasks;

use pocketmine\scheduler\Task;
use SkyBlock\Main;

class TimeCommand extends Task {

    private Main $plugin;
    private string $cmd;
    private bool $start;

    /**
     * @param Main   $plugin
     * @param string $cmd
     */
    public function __construct(Main $plugin, string $cmd) {
        $this->plugin = $plugin;
        $this->cmd = $cmd;
        $this->start = false;
    }

    public function onRun() : void {
        if ($this->start) {
            $this->plugin->runCommand($this->cmd);
        } else {
            $this->start = true;
        }
    }

}
