<?php

namespace SkyBlock\spawner;

use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use SkyBlock\util\Util;

class Sheep extends Animal implements Colorable, SpawnerEntity {

    use StackableTrait;

    const NETWORK_ID = 13;
    public float $length = 1.4375;

    public static function getNetworkTypeId() : string {
        return EntityIds::SHEEP;
    }

    public function getXpDropAmount() : int {
        return mt_rand(1, 3);
    }

    public function getName() : string {
        return "Sheep";
    }

    public function getDrops() : array {
        $wool = VanillaBlocks::WOOL()->setColor(DyeColor::WHITE)->asItem();
        $rawMutton = VanillaItems::RAW_MUTTON();
        $cookedMutton = VanillaItems::COOKED_MUTTON();

        $drops = [];
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $c = $cause->getDamager();
            if ($c instanceof Player) {
                $looting = Util::getLooting($c->getInventory()->getItemInHand());
                if ($this->isOnFire()) {
                    $drops[] = $cookedMutton->setCount(mt_rand(1, 2 + $looting));
                } else {
                    $drops[] = $rawMutton->setCount(mt_rand(1, 2 + $looting));
                }
            }
        } else {
            if ($this->isOnFire()) {
                $drops[] = $cookedMutton->setCount(mt_rand(1, 2));
            } else {
                $drops[] = $rawMutton->setCount(mt_rand(1, 2));
            }
        }
        $drops[] = $wool;
        return $drops;
    }

    protected function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(1.3, 0.9);
    }

    public function getMobcoins() : int {
        return 2;
    }
}
