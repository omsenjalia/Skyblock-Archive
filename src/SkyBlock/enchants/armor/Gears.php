<?php


namespace SkyBlock\enchants\armor;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;

class Gears extends BaseArmorEnchant {

    static int $id = 131;

    public function isApplicableTo(Player $holder) : bool {
        return true;
    }

    /**
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void {
        if (!$victim->getEffects()->has(VanillaEffects::SPEED())) {
            $effect = new EffectInstance(VanillaEffects::SPEED(), $enchantmentLevel * 2 * 20, mt_rand(1, ceil($enchantmentLevel / 4)), false);
            $victim->getEffects()->add($effect);
        }
    }

}