<?php

namespace SkyBlock\spawner;

use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use SkyBlock\enchants\BaseEnchantment;

class Spider extends Monster implements SpawnerEntity {

    use StackableTrait;

    public float $length = 0.9;

    public static function getNetworkTypeId() : string {
        return EntityIds::SPIDER;
    }

    public function getName() : string {
        return "Spider";
    }

    public function getXpDropAmount() : int {
        return 5;
    }

    protected function initEntity(CompoundTag $nbt) : void {
        $this->setMaxHealth(16);
        parent::initEntity($nbt);
    }

    public function getDrops() : array {
        $drops = [];
        $cause = $this->lastDamageCause;
        $item = VanillaItems::STRING();
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($damager instanceof Player) {
                $lootingL = BaseEnchantment::getEnchantmentLevel($damager->getInventory()->getItemInHand(), EnchantmentIds::LOOTING);
                $levels = [0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 5, 6, 6, 7, 7];
                $count = mt_rand(3, 3 + $levels[$lootingL]);
                $item->setCount($count);
            }
        } else    $item->setCount(mt_rand(1, 2));
        $drops[] = $item;
        return $drops;
    }

    protected function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(0.9, 1.4);
    }

    public function getMobcoins() : int {
        return 7;
    }
}