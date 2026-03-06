<?php


namespace SkyBlock\enchants\armor\effect;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;

class Snorkel extends BaseArmorEffectEnchant {

    static int $id = 150;

    public function isApplicableTo(Player $holder) : bool {
        return !$holder->getEffects()->has(VanillaEffects::WATER_BREATHING());
    }

    public function onEquip(Player $holder, int $enchantmentLevel) : void {
        $effect = new EffectInstance(VanillaEffects::WATER_BREATHING(), 15 * 20, $this->getLevel($enchantmentLevel), false);
        $holder->getEffects()->add($effect);
    }

}