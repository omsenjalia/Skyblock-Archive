<?php

namespace SkyBlock\spawner;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use SkyBlock\util\Util;
use function mt_rand;

class Zombie extends Monster implements SpawnerEntity {

    use StackableTrait;

    public static function getNetworkTypeId() : string {
        return EntityIds::ZOMBIE;
    }

    public function getName() : string {
        return "Zombie";
    }

    public function getDrops() : array {
        $rottenFlesh = VanillaItems::ROTTEN_FLESH();
        $ironIngot = VanillaItems::IRON_INGOT();
        $carrot = VanillaItems::CARROT();
        $potato = VanillaItems::POTATO();
        $bakedPotato = VanillaItems::BAKED_POTATO();

        $drops = [];
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $c = $cause->getDamager();
            if ($c instanceof Player) {
                $looting = Util::getLooting($c->getInventory()->getItemInHand());
                $drops[] = $rottenFlesh->setCount(mt_rand(0, (2 + $looting)));

                if (mt_rand(1, 10000) <= 83 + ($looting * 33)) {
                    $drops[] = $ironIngot;
                }
                if (mt_rand(1, 10000) <= 83 + ($looting * 33)) {
                    $drops[] = $carrot;
                }
                if (mt_rand(1, 10000) <= 83 + ($looting * 33)) {
                    if ($this->isOnFire()) {
                        $drops[] = $bakedPotato;
                    } else {
                        $drops[] = $potato;
                    }
                }
            }
        } else {
            $drops[] = $rottenFlesh->setCount(mt_rand(0, 2));

            if (mt_rand(1, 10000) <= 83) {
                $drops[] = $ironIngot;
            }
            if (mt_rand(1, 10000) <= 83) {
                $drops[] = $carrot;
            }
            if (mt_rand(1, 10000) <= 83) {
                if ($this->isOnFire()) {
                    $drops[] = $bakedPotato;
                } else {
                    $drops[] = $potato;
                }
            }
        }

        return $drops;
    }

    public function getXpDropAmount() : int {
        return mt_rand(1, 3);
    }

    protected function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(1.95, 0.6);
    }

    public function getMobcoins() : int {
        return 8;
    }

}
