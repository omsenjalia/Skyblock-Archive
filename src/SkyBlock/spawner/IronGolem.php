<?php

namespace SkyBlock\spawner;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use SkyBlock\util\Util;

class IronGolem extends Animal implements SpawnerEntity {

    use StackableTrait;

    const NETWORK_ID = 20;

    public float $length = 0.9;

    public int $knockbackTicks = 0;
    public $target = null;
    public int $range = 10;
    public int $speed = 1;
    public int $attackDamage = 7;
    public int $attackRate = 10;
    public int $attackDelay = 0;

    public static function getNetworkTypeId() : string {
        return EntityIds::IRON_GOLEM;
    }

    public function getXpDropAmount() : int {
        return 0;
    }

    public function initEntity(CompoundTag $nbt) : void {
        $this->setMaxHealth(100);
        parent::initEntity($nbt);
    }

    public function getName() : string {
        return "Iron Golem";
    }

    public function getDrops() : array {
        $poppy = VanillaBlocks::POPPY()->asItem();
        $ironIngot = VanillaItems::IRON_INGOT();

        $drops = [];
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $c = $cause->getDamager();
            if ($c instanceof Player) {
                $looting = Util::getLooting($c->getInventory()->getItemInHand());
                $drops[] = $ironIngot->setCount(mt_rand(3, 5 + $looting));
                $drops[] = $poppy->setCount(mt_rand(0, 2 + $looting));
            }
        } else {
            $drops[] = $ironIngot->setCount(mt_rand(3, 5));
            $drops[] = $poppy->setCount(mt_rand(0, 2));
        }
        return $drops;
    }

    protected function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(2.9, 1.4);
    }

    public function getMobcoins() : int {
        return 15;
    }
}