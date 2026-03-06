<?php

namespace SkyBlock\spawner;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\WaterAnimal;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use SkyBlock\util\Util;

class GlowSquid extends WaterAnimal implements SpawnerEntity {

    use StackableTrait;

    public static function getNetworkTypeId() : string {
        return EntityIds::GLOW_SQUID;
    }

    public function getXpDropAmount() : int {
        return mt_rand(1, 3);
    }

    protected function initEntity(CompoundTag $nbt) : void {
        $this->setMaxHealth(10);
        parent::initEntity($nbt);
    }

    public function getName() : string {
        return "Glow Squid";
    }

    public function getDrops() : array {
        $glowInkSac = VanillaItems::GLOW_INK_SAC();

        $drops = [];
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $c = $cause->getDamager();
            if ($c instanceof Player) {
                $looting = Util::getLooting($c->getInventory()->getItemInHand());
                $drops[] = $glowInkSac->setCount(mt_rand(1, 3 + $looting));
            }
        } else {
            $drops[] = $glowInkSac->setCount(mt_rand(1, 3));
        }
        return $drops;
    }

    protected function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(0.8, 0.8);
    }

    public function getMobcoins() : int {
        return 4;
    }
}
