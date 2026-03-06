<?php

namespace SkyBlock\tasks;

use pocketmine\scheduler\Task;
use pocketmine\world\Position;
use SkyBlock\Main;

class EnvoyTask extends Task {

    public Main $pl;

    public function __construct(Main $pl) {
        $this->pl = $pl;
    }

    public function onRun() : void {
        $this->pl->getServer()->broadcastMessage("§b§l»>\n§b§l[Envoys]§r§e> Envoys are spawning now...\n§b§l»>");
        $i = 0;
        while ($i <= 3) {
            $pos1 = $this->pl->warzone[mt_rand(0, 19)];
            $this->pl->spawnEnvoy($i, new Position($pos1['x'], $pos1['y'] + 1, $pos1['z'], $this->pl->getServer()->getWorldManager()->getWorldByName("PvP")));
            $i++;
        }
        $this->pl->getServer()->broadcastMessage("§b§l»>\n§b§l[Envoys]§r§e> Envoys have spawned over warzone, do /warp warzone to join and pvp! Envoys have amazing loot!\n§b§l»>");
        $this->pl->getScheduler()->scheduleDelayedTask(new EnvoyCompleteTask($this->pl), 5 * 1200);
    }

}
