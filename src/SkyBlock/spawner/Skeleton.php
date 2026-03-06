<?php

namespace SkyBlock\spawner;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\projectile\ProjectileSource;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use SkyBlock\util\Util;

class Skeleton extends Monster implements ProjectileSource, SpawnerEntity {

    use StackableTrait;

    public static function getNetworkTypeId() : string {
        return EntityIds::SKELETON;
    }

    public function getName() : string {
        return "Skeleton";
    }

    /**
     * @return array
     */
    public function getDrops() : array {
        $bone = VanillaItems::BONE();
        $arrow = VanillaItems::ARROW();
        $drops = [];
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $c = $cause->getDamager();
            if ($c instanceof Player) {
                $looting = Util::getLooting($c->getInventory()->getItemInHand());
                $drops[] = $bone->setCount(mt_rand(0, 2 + $looting));
                $drops[] = $arrow->setCount(mt_rand(0, 2 + $looting));
            }
        } else {
            $drops[] = $bone->setCount(mt_rand(0, 2));
            $drops[] = $bone->setCount(mt_rand(0, 2));
        }
        return $drops;
    }

    public function getXpDropAmount() : int {
        return mt_rand(1, 3);
    }

    protected function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(1.99, 0.6);
    }

    public function getMobcoins() : int {
        return 9;
    }
}
