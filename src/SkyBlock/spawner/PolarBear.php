<?php

namespace SkyBlock\spawner;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use SkyBlock\util\Util;

class PolarBear extends Animal implements SpawnerEntity {
    use StackableTrait;

    public static function getNetworkTypeId() : string {
        return EntityIds::POLAR_BEAR;
    }

    public function getXpDropAmount() : int {
        return mt_rand(1, 3);
    }

    public function getName() : string {
        return "Polar Bear";
    }

    public function getDrops() : array {
        $rawCod = VanillaItems::RAW_FISH();
        $cookedCod = VanillaItems::COOKED_FISH();
        $rawSalmon = VanillaItems::RAW_SALMON();
        $cookedSalmon = VanillaItems::COOKED_SALMON();

        $drops = [];
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $c = $cause->getDamager();
            if ($c instanceof Player) {
                $looting = Util::getLooting($c->getInventory()->getItemInHand());
                if ($this->isOnFire()) {
                    $drops[] = $cookedCod->setCount(mt_rand(0, 2 + $looting));
                    $drops[] = $cookedSalmon->setCount(mt_rand(0, 2 + $looting)); // java only but whatever
                } else {
                    $drops[] = $rawCod->setCount(mt_rand(0, 2 + $looting));
                    $drops[] = $rawSalmon->setCount(mt_rand(0, 2 + $looting));
                }
            }
        } else {
            if ($this->isOnFire()) {
                $drops[] = $cookedCod->setCount(mt_rand(0, 2));
                $drops[] = $cookedSalmon->setCount(mt_rand(0, 2)); // java only but whatever
            } else {
                $drops[] = $rawCod->setCount(mt_rand(0, 2));
                $drops[] = $rawSalmon->setCount(mt_rand(0, 2));
            }
        }
        return $drops;
    }

    protected function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(1.4, 1.4);
    }

    public function getMobcoins() : int {
        return 10;
    }

}