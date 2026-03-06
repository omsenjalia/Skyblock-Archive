<?php

namespace SkyBlock\spawner;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use SkyBlock\util\Util;

class Panda extends Animal implements SpawnerEntity {
    use StackableTrait;

    public static function getNetworkTypeId() : string {
        return EntityIds::PANDA;
    }

    public function getXpDropAmount() : int {
        return mt_rand(1, 3);
    }

    public function getName() : string {
        return "Panda";
    }

    public function getDrops() : array {
        $bamboo = VanillaItems::BAMBOO();

        $drops = [];
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $c = $cause->getDamager();
            if ($c instanceof Player) {
                $looting = Util::getLooting($c->getInventory()->getItemInHand());
                $drops[] = $bamboo->setCount(mt_rand(0, 2 + $looting));
            }
        } else {
            $drops[] = $bamboo->setCount(mt_rand(0, 2));
        }
        return $drops;
    }

    protected function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(1.25, 1.3);
    }

    public function getMobcoins() : int {
        return 5;
    }
}