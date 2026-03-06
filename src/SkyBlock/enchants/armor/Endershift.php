<?php


namespace SkyBlock\enchants\armor;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;

class Endershift extends BaseArmorEnchant {

    static int $id = 129;

    public function getLevel(int $level = 1) : int {
        if ($level > 12) {
            return 5;
        } elseif ($level > 9) {
            return 4;
        } elseif ($level > 6) {
            return 3;
        } elseif ($level > 3) {
            return 2;
        } else {
            return 1;
        }
    }

    /**
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void {
        if ($victim->getHealth() < ($victim->getMaxHealth() / 2)) {
            $this->sendActivation($victim, "§bEndershift §aActivated!");
            $this->sendActivation($attacker, "§cStruck by §bEndershift §cEnchant!");
            $effect = new EffectInstance(VanillaEffects::SPEED(), $enchantmentLevel * 20, $this->getLevel($enchantmentLevel), false);
            $victim->getEffects()->add($effect);
            $victim->setHealth($victim->getHealth() + (0.20 * $enchantmentLevel));
        }
    }

}