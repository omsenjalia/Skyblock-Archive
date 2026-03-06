<?php

declare(strict_types=1);

namespace SkyBlock\block;

use pocketmine\block\MonsterSpawner as SpawnerPM;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\tiles\MobSpawner as TileSpawner;

class MonsterSpawner extends SpawnerPM {

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null, array &$returnedItems = []) : bool {
        return false;
    }

    public function onScheduledUpdate() : void {
        $spawner = $this->position->getWorld()->getTile($this->position);
        if ($spawner instanceof TileSpawner && $spawner->onUpdate())
            $this->position->getWorld()->scheduleDelayedBlockUpdate($this->position, 1);
    }

    public function onBreak(Item $item, Player $player = null, array &$returnedItems = []) : bool {
        $tile = $this->getPosition()->getWorld()->getTile($this->getPosition());
        if (parent::onBreak($item, $player)) {
            if ($tile instanceof TileSpawner) {
                $main = Main::getInstance();
                $island = $main->getIslandManager()->getOnlineIslandByWorld($this->getPosition()->getWorld()->getDisplayName());
                if (!is_null($island)) {
                    if (!is_null($player)) {
                        $player->getInventory()->addItem($main->getEvFunctions()->getSpawnerBlock($tile->getEntityId(), $tile->level1));
                    }
                    $island->removeSpawner();
                }
            }
            return true;
        }
        return false;
    }

    protected function getXpDropAmount() : int {
        return 0;
    }

}