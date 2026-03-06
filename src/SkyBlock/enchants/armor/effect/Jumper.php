<?php


namespace SkyBlock\enchants\armor\effect;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;

class Jumper extends BaseArmorEffectEnchant {

    static int $id = 160;

    public function isApplicableTo(Player $holder) : bool {
        return !$holder->getEffects()->has(VanillaEffects::JUMP_BOOST());
        //        else {
        //            return !in_array($holder->getWorld()->getDisplayName(), Values::SERVER_WORLDS, true);
        //        }
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

    public function onEquip(Player $holder, int $enchantmentLevel) : void {
        $effect = new EffectInstance(VanillaEffects::JUMP_BOOST(), 15 * 20, $this->getLevel($enchantmentLevel, 1), false);
        $holder->getEffects()->add($effect);
    }

}