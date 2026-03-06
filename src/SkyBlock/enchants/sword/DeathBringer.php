<?php


namespace SkyBlock\enchants\sword;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use SkyBlock\enchants\BaseEnchantment;

class DeathBringer extends BaseMeleeEnchant {

    static int $id = 102;

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

    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $ev->setModifier($enchantmentlevel > 10 ? $ev->getBaseDamage() : $ev->getBaseDamage() * ($enchantmentlevel / 10), 102);
        $this->sendActivation($attacker, "§cStruck by §bDeathBringer §cEnchant!");
        $this->sendActivation($player, "§bDeathBringer §aActivated!");
    }

}