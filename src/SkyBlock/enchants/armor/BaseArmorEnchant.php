<?php


namespace SkyBlock\enchants\armor;


use pocketmine\player\Player;
use SkyBlock\enchants\BaseEnchantment;

abstract class BaseArmorEnchant extends BaseEnchantment {
    /**
     * @param Player $holder
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder) : bool {
        return mt_rand(1, 25) === 1;
    }

    /**
     * Called after damaging the entity to apply any post damage effects to the target.
     *
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    abstract public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void;
}