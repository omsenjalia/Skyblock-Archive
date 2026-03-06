<?php

namespace SkyBlock\spawner;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use SkyBlock\util\Util;

class Camel extends Animal implements SpawnerEntity {
    use StackableTrait;

    public static function getNetworkTypeId() : string {
        return EntityIds::CAMEL;
    }

    public function getXpDropAmount() : int {
        return mt_rand(1, 3);
    }

    public function getName() : string {
        return "Camel";
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
        return new EntitySizeInfo(2.375, 1.7);
    }

    public function getMobcoins() : int {
        return 4;
    }

}