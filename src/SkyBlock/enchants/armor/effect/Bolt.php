<?php


namespace SkyBlock\enchants\armor\effect;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;

class Bolt extends BaseArmorEffectEnchant {

    static int $id = 153;

    public function isApplicableTo(Player $holder) : bool {
        return !$holder->getEffects()->has(VanillaEffects::SPEED());
    }

    public function onEquip(Player $holder, int $enchantmentLevel) : void {
        $effect = new EffectInstance(VanillaEffects::SPEED(), 15 * 20, $this->getLevel($enchantmentLevel), false);
        $holder->getEffects()->add($effect);
    }

    public function getLevel(int $level = 1, int $diff = 0) : int {
        if ($level > 12) {
            return 4;
        } elseif ($level > 9) {
            return 4;
        } elseif ($level > 5) {
            return 3;
        } else {
            return 2;
        }
    }

}