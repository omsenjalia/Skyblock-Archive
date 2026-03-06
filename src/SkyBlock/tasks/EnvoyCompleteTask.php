<?php

namespace SkyBlock\tasks;

use pocketmine\scheduler\Task;
use pocketmine\world\Position;
use SkyBlock\Main;

class EnvoyCompleteTask extends Task {

    public Main $pl;

    public function __construct(Main $pl) {
        $this->pl = $pl;
    }

    public function onRun() : void {
        foreach ($this->pl->envoys as $id => $data) {
            $pos = new Position($data['x'], $data['y'], $data['z'], $this->pl->getServer()->getWorldManager()->getWorldByName("PvP"));
            $this->pl->despawnEnvoy($id, $pos);
        }
        $this->pl->getServer()->broadcastMessage("§b§l»>\n§b§l[Envoys]§r§c> Envoys have been despawned!\n§b§l»>");
    }
}
