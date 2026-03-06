<?php

namespace SkyBlock\spawner;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use SkyBlock\util\Util;

class Blaze extends Monster implements SpawnerEntity {

    use StackableTrait;

    public float $length = 0.9;

    public static function getNetworkTypeId() : string {
        return EntityIds::BLAZE;
    }

    public function getName() : string {
        return "Blaze";
    }

    public function getXpDropAmount() : int {
        return 10;
    }

    public function getDrops() : array {
        $blazeRod = VanillaItems::BLAZE_ROD();

        $drops = [];
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $c = $cause->getDamager();
            if ($c instanceof Player) {
                $looting = Util::getLooting($c->getInventory()->getItemInHand());
                $drops[] = $blazeRod->setCount(mt_rand(0, 1 + $looting));
            }
        } else {
            $drops[] = $blazeRod->setCount(mt_rand(0, 1));
        }
        return $drops;
    }

    protected function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(1.8, 0.5);
    }

    public function getMobcoins() : int {
        return 10;
    }
}