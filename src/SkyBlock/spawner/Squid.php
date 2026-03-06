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
use function mt_rand;

class Squid extends WaterAnimal implements SpawnerEntity {

    use StackableTrait;

    public static function getNetworkTypeId() : string {
        return EntityIds::SQUID;
    }

    public function getXpDropAmount() : int {
        return mt_rand(1, 3);
    }

    public function initEntity(CompoundTag $nbt) : void {
        $this->setMaxHealth(10);
        parent::initEntity($nbt);
    }

    public function getName() : string {
        return "Squid";
    }

    public function getDrops() : array {
        $string = VanillaItems::STRING();
        $spiderEye = VanillaItems::SPIDER_EYE();

        $drops = [];
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $c = $cause->getDamager();
            if ($c instanceof Player) {
                $looting = Util::getLooting($c->getInventory()->getItemInHand());
                $drops[] = $string->setCount(mt_rand(0, 2 + $looting));

                if (mt_rand(1, 10000) <= 3333 + ($looting * 1667)) {
                    $drops[] = $spiderEye->setCount(mt_rand(1, 1 + $looting));
                }
            }
        } else {
            $drops[] = $string->setCount(mt_rand(0, 2));
            $drops[] = $spiderEye->setCount(1);
        }

        return $drops;
    }

    protected function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo(0.475, 0.475);
    }

    public function getMobcoins() : int {
        return 3;
    }
}
