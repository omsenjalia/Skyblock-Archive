<?php

namespace SkyBlock\spawner;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use SkyBlock\util\Util;

class Cow extends Animal implements SpawnerEntity {

    use StackableTrait;

    public float $length = 0.9;

    public static function getNetworkTypeId() : string {
        return EntityIds::COW;
    }

    public function getName() : string {
        return "Cow";
    }

    public function getXpDropAmount() : int {
        return mt_rand(1, 3);
    }

    public function getDrops() : array {
        $rawBeef = VanillaItems::RAW_BEEF();
        $cookedBeef = VanillaItems::STEAK();
        $leather = VanillaItems::LEATHER();

        $drops = [];
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $c = $cause->getDamager();
            if ($c instanceof Player) {
                $looting = Util::getLooting($c->getInventory()->getItemInHand());
                $drops[] = $leather->setCount(mt_rand(0, 2 + $looting));
                if ($this->isOnFire()) {
                    $drops[] = $cookedBeef->setCount(mt_rand(1, 3 + $looting));
                } else {
                    $drops[] = $rawBeef->setCount(mt_rand(1, 3 + $looting));
                }
            }
        } else {
            $drops[] = $leather->setCount(mt_rand(0, 2));
            if ($this->isOnFire()) {
                $drops[] = $cookedBeef->setCount(mt_rand(1, 3));
            } else {
                $drops[] = $rawBeef->setCount(mt_rand(1, 3));
            }
        }
        return $drops;
    }

    protected function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(1.3, 0.9);
    }

    public function getMobcoins() : int {
        return 2;
    }
}