<?php

namespace SkyBlock\spawner;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use SkyBlock\util\Util;

class Goat extends Animal implements SpawnerEntity {
    use StackableTrait;

    public static function getNetworkTypeId() : string {
        return EntityIds::GOAT;
    }

    public function getXpDropAmount() : int {
        return mt_rand(1, 3);
    }

    public function getName() : string {
        return "Goat";
    }

    public function getDrops() : array {
        $drops = [];
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $c = $cause->getDamager();
            if ($c instanceof Player) {
                $looting = Util::getLooting($c->getInventory()->getItemInHand());
            }
        }
        return $drops; // todo
    }

    protected function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(1.3, 0.9);
    }

    public function getMobcoins() : int {
        return 4;
    }

}