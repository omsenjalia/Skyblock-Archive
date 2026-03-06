<?php

namespace SkyBlock\spawner;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use SkyBlock\util\Util;

class Pig extends Animal implements SpawnerEntity {

    use StackableTrait;

    public float $length = 0.9;

    public static function getNetworkTypeId() : string {
        return EntityIds::PIG;
    }

    public function getXpDropAmount() : int {
        return mt_rand(1, 3);
    }

    public function getName() : string {
        return "Pig";
    }

    public function getDrops() : array {
        $rawPorkchop = VanillaItems::RAW_PORKCHOP();
        $cookedPorkchop = VanillaItems::COOKED_PORKCHOP();

        $drops = [];
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $c = $cause->getDamager();
            if ($c instanceof Player) {
                $looting = Util::getLooting($c->getInventory()->getItemInHand());
                if ($this->isOnFire()) {
                    $drops[] = $cookedPorkchop->setCount(mt_rand(1, 3 + $looting));
                } else {
                    $drops[] = $rawPorkchop->setCount(mt_rand(1, 3 + $looting));
                }
            }
        } else {
            if ($this->isOnFire()) {
                $drops[] = $cookedPorkchop->setCount(mt_rand(1, 3));
            } else {
                $drops[] = $rawPorkchop->setCount(mt_rand(1, 3));
            }
        }
        return $drops;
    }

    protected function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(0.9, 0.9);
    }

    public function getMobcoins() : int {
        return 1;
    }
}