<?php


namespace SkyBlock\enchants\armor;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;

class Implants extends BaseArmorEnchant {

    static int $id = 133;

    /**
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void {
        if ($victim->getHungerManager()->getFood() < 18) {
            $effect = new EffectInstance(VanillaEffects::SATURATION(), $enchantmentLevel, $this->getLevel($enchantmentLevel), false);
            $victim->getEffects()->add($effect);
            $victim->getHungerManager()->setFood($victim->getHungerManager()->getFood() + ($enchantmentLevel / 60));
        }
    }

}