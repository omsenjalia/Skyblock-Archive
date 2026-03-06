<?php

namespace SkyBlock\tasks;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\world\Position;
use SkyBlock\Main;

class ArenaCountDown extends Task {

    public Main $pl;
    public Player $player;
    public string $island;

    public function __construct(Main $pl, Player $player, $island) {
        $this->pl = $pl;
        $this->player = $player;
        $this->island = $island;
    }

    public function onRun() : void {
        if ($this->player instanceof Player and isset($this->pl->warplayers[strtolower($this->player->getName())]) and isset($this->pl->countdown[strtolower($this->player->getName())])) {
            if ($this->player->getWorld() !== null && $this->player->getWorld()->getDisplayName() != "PvP") {
                if ($this->pl->war[1]["island1"] == strtolower($this->island)) {
                    $warp = new Position($this->pl->wars[0]['x'], $this->pl->wars[0]['y'], $this->pl->wars[0]['z'], $this->pl->getServer()->getWorldManager()->getWorldByName("PvP"));
                } else {
                    $warp = new Position($this->pl->wars[1]['x'], $this->pl->wars[1]['y'], $this->pl->wars[1]['z'], $this->pl->getServer()->getWorldManager()->getWorldByName("PvP"));
                }
                $this->player->teleport($warp);
                unset($this->pl->countdown[strtolower($this->player->getName())]);
            }
        }
    }

}