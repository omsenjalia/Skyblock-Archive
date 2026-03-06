<?php

namespace SkyBlock\spawner;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Silverfish extends Monster implements SpawnerEntity {

    use StackableTrait;

    public float $length = 0.9;

    public static function getNetworkTypeId() : string {
        return EntityIds::SILVERFISH;
    }

    public function getName() : string {
        return "Silverfish";
    }

    public function getXpDropAmount() : int {
        return 5;
    }

    public function getDrops() : array {
        return []; // todo what do they drop
    }

    protected function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(0.3, 0.4);
    }

    public function getMobcoins() : int {
        return 5;
    }
}