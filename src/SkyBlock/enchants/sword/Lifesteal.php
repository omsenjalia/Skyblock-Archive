<?php


namespace SkyBlock\enchants\sword;


use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use SkyBlock\enchants\armor\LifeShield;
use SkyBlock\enchants\BaseEnchantment;

class Lifesteal extends BaseMeleeEnchant {

    static int $id = 100;

    public function isApplicableTo(Player $holder) : bool {
        $heldItem = $holder->getInventory()->getItemInHand();
        $ench = $heldItem->getEnchantment(BaseEnchantment::getEnchantment(self::$id));
        if ($ench !== null) {
            $level = $ench->getLevel();
            if ($level === 15) {
                return mt_rand(1, 20) === 1;
            } elseif ($level === 14) {
                return mt_rand(1, 21) === 1;
            } elseif ($level === 13) {
                return mt_rand(1, 22) === 1;
            } elseif ($level === 12) {
                return mt_rand(1, 23) === 1;
            } elseif ($level === 11) {
                return mt_rand(1, 24) === 1;
            } else {
                return mt_rand(1, 25) === 1;
            }
        }
        return false;
    }

    /**
     * @param Player            $player
     * @param Player            $attacker
     * @param EntityDamageEvent $ev
     * @param int               $enchantmentlevel
     */
    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $this->sendActivation($player, "§bLifeSteal §aActivated!");
        $level = BaseEnchantment::getEnchantmentLevel($attacker->getArmorInventory()->getChestplate(), LifeShield::$id);
        if ($level > 10 ? mt_rand(1, 3) === 1 : mt_rand(1, 6) === 1) {
            $this->sendActivation($player, "§bLifeSteal §cDeactivated by LifeShield Enchant!");
            return;
        }

        $attacker->getEffects()->remove(VanillaEffects::REGENERATION());
        $attacker->getEffects()->remove(VanillaEffects::HEALTH_BOOST());
        $this->sendActivation($attacker, "§cStruck by §bLifeSteal §cEnchant! Health Effects removed");

        $health2 = $attacker->getHealth();
        $health1 = $player->getHealth();
        $healthTaken = $enchantmentlevel > 9 ? 8 : ceil($enchantmentlevel / 2);

        $attacker->setHealth($health2 - $healthTaken);
        $player->setHealth($health1 + $healthTaken);

        //        if ($health2 >= 20) {
        //            $attacker->setHealth(19);
        //        } elseif ($health2 > 15) {
        //            $attacker->setHealth(14);
        //        } elseif ($health2 < 12 && $health2 > 6) {
        //            $attacker->setHealth(5);
        //        } elseif ($health2 < 3 && $health2 > 1) {
        //            $attacker->kill();
        //        }
        //
        //        if ($health1 >= 15) {
        //            $player->setHealth($health1 + 2);
        //        } elseif ($health1 < 12 && $health1 > 7) {
        //            $player->setHealth(14);
        //        } elseif ($health1 < 4 && $health1 > 1) {
        //            $player->setHealth(7);
        //        }
    }

}