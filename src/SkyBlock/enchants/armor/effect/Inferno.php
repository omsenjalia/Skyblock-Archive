<?php


namespace SkyBlock\enchants\armor\effect;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;

class Inferno extends BaseArmorEffectEnchant {

    static int $id = 155;

    public function isApplicableTo(Player $holder) : bool {
        return !$holder->getEffects()->has(VanillaEffects::FIRE_RESISTANCE());
    }

    public function onEquip(Player $holder, int $enchantmentLevel) : void {
        $effect = new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 15 * 20, $this->getLevel($enchantmentLevel), false);
        $holder->getEffects()->add($effect);
    }

}