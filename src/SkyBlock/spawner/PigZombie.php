<?php

namespace SkyBlock\spawner;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use SkyBlock\util\Util;

class PigZombie extends Monster implements SpawnerEntity {

    use StackableTrait;

    public static function getNetworkTypeId() : string {
        return EntityIds::ZOMBIE_PIGMAN;
    }

    public function getXpDropAmount() : int {
        return 5 + mt_rand(1, 3);
    }

    public function getName() : string {
        return "PigZombie";
    }

    public function getDrops() : array {
        $rottenFlesh = VanillaItems::ROTTEN_FLESH();
        $goldNugget = VanillaItems::GOLD_NUGGET();
        $goldIngot = VanillaItems::GOLD_INGOT();

        $drops = [];
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $c = $cause->getDamager();
            if ($c instanceof Player) {
                $looting = Util::getLooting($c->getInventory()->getItemInHand());
                $drops[] = $rottenFlesh->setCount(mt_rand(0, 1 + $looting));
                $drops[] = $goldNugget->setCount(mt_rand(0, 1 + $looting));

                if (mt_rand(1, 10000) <= 250 + ($looting * 100)) {
                    $drops[] = $goldIngot;
                }
            }
        } else {
            $drops[] = $rottenFlesh->setCount(mt_rand(0, 1));
            $drops[] = $goldNugget->setCount(mt_rand(0, 1));

            if (mt_rand(1, 10000) <= 250) {
                $drops[] = $goldIngot;
            }
        }
        return $drops;
    }

    protected function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(1.9, 0.6);
    }

    public function getMobcoins() : int {
        return 8;
    }
}
