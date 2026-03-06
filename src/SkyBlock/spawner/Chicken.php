<?php

namespace SkyBlock\spawner;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use SkyBlock\util\Util;

class Chicken extends Animal implements SpawnerEntity {

    use StackableTrait;

    public float $length = 0.6;

    public static function getNetworkTypeId() : string {
        return EntityIds::CHICKEN;
    }

    public function getXpDropAmount() : int {
        return mt_rand(1, 3);
    }

    public function getName() : string {
        return "Chicken";
    }

    public function getDrops() : array {
        $feather = VanillaItems::FEATHER();
        $rawChicken = VanillaItems::RAW_CHICKEN();
        $cookedChicken = VanillaItems::COOKED_CHICKEN();

        $drops = [];
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $c = $cause->getDamager();
            if ($c instanceof Player) {
                $looting = Util::getLooting($c->getInventory()->getItemInHand());
                $drops[] = $feather->setCount(mt_rand(0, 2 + $looting));
                if ($this->isOnFire()) {
                    $drops[] = $cookedChicken->setCount(mt_rand(1, 1 + $looting));
                } else {
                    $drops[] = $rawChicken->setCount(mt_rand(1, 1 + $looting));
                }
            }
        } else {
            $drops[] = $feather->setCount(mt_rand(0, 2));
            if ($this->isOnFire()) {
                $drops[] = $cookedChicken;
            } else {
                $drops[] = $rawChicken;
            }
        }
        return $drops;
    }

    protected function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(0.8, 0.6);
    }

    public function getMobcoins() : int {
        return 1;
    }
}