<?php

namespace SkyBlock;

use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\math\Vector3;
use SkyBlock\spawner\SpawnerEntity;

class StackFactory {

    const TAG_STACK_AMOUNT = "Amount";

    public static function removeFromStack(Living $entity) : bool {
        if (!$entity instanceof Human) {
            if (self::decreaseStackSize($entity)) {
                $size = self::getStackSize($entity);
                if ($size <= 0) return false;
                $level = $entity->getWorld();
                $pos = new Vector3($entity->getPosition()->x, $entity->getPosition()->y, $entity->getPosition()->z);
                $ev = new EntityDeathEvent($entity, $entity->getDrops());
                $ev->call();
                foreach ($ev->getDrops() as $drops) {
                    $level->dropItem($pos, $drops);
                }
                return true;
            }
        }
        return false;
    }

    public static function decreaseStackSize(Living $entity, int $amount = 1) : bool {
        if (!$entity instanceof Human) {
            $size = self::getStackSize($entity);
            if (self::isStack($entity) && $entity instanceof SpawnerEntity) {
                if ($size - $amount > 0) {
                    $entity->setStack($size - $amount);
                } else {
                    return false;
                }
                return true;
            }
        }
        return false;
    }

    public static function getStackSize(Living $entity) : int {
        if (!$entity instanceof Human) {
            if (self::isStack($entity) && $entity instanceof SpawnerEntity) {
                return $entity->getStackAmount();
            }
            return 1;
        }
        return 0;
    }

    public static function isStack($entity) : bool {
        if ($entity instanceof SpawnerEntity) {
            return $entity->getStackAmount() >= 1;
        }
        return false;
    }

    public static function addToClosestStack(Living $entity) : bool {
        if (!$entity instanceof Human) {
            $stack = self::findNearbyStack($entity);
            if (self::isStack($stack) && $stack instanceof SpawnerEntity && $stack instanceof Living) {
                if (self::addToStack($stack, $entity)) {
                    self::recalculateStackName($stack);
                    return true;
                }
            }
        }
        return false;
    }

    public static function findNearbyStack(Living $entity) {
        if ($entity instanceof SpawnerEntity) {
            foreach ($entity->getWorld()->getEntities() as $e) {
                if (is_a($e, get_class($entity)) and $e !== $entity) {
                    return $e;
                }
            }
        }
        return null;
    }

    public static function addToStack(Living $stack, Living $entity) : bool {
        if ($entity instanceof SpawnerEntity) {
            if (is_a($entity, get_class($stack)) && $stack !== $entity) {
                if (self::increaseStackSize($stack, self::getStackSize($entity))) {
                    $entity->flagForDespawn();
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    public static function increaseStackSize(Living $entity, int $amount = 1) : bool {
        if (self::isStack($entity) and $entity instanceof SpawnerEntity) {
            $entity->setStack(self::getStackSize($entity) + $amount);
            return true;
        }
        return false;
    }

    public static function recalculateStackName(Living $entity) : void {
        if (!$entity instanceof Human) {
            if (self::isStack($entity)) {
                $count = self::getStackSize($entity);
                $entity->setNameTagVisible(true);
                $entity->setNameTagAlwaysVisible(true);
                $entity->setNameTag(str_replace(["{name}", "{amount}"], [$entity->getName(), $count], "§l§e{name} §7§lx§c{amount}§r"));
            }
        }
    }

}