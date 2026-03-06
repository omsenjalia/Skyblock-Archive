<?php


namespace SkyBlock\enchants\armor;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;

class Glowing extends BaseArmorEnchant {

    static int $id = 134;

    /**
     * @param Player $holder
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder) : bool {
        return true;
    }

    /**
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void {
        if (!$victim->getEffects()->has(VanillaEffects::NIGHT_VISION())) {
            $effect = new EffectInstance(VanillaEffects::NIGHT_VISION(), $enchantmentLevel * 2 * 20, $this->getLevel($enchantmentLevel), false);
            $victim->getEffects()->add($effect);
        }
    }

}