<?php


namespace SkyBlock\enchants\armor\effect;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;

class Flashlight extends BaseArmorEffectEnchant {

    static int $id = 151;

    public function isApplicableTo(Player $holder) : bool {
        return true;
    }

    public function onEquip(Player $holder, int $enchantmentLevel) : void {
        $effect = new EffectInstance(VanillaEffects::NIGHT_VISION(), 15 * 20, 1, false);
        $holder->getEffects()->add($effect);
    }

}