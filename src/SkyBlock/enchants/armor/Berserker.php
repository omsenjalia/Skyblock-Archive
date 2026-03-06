<?php


namespace SkyBlock\enchants\armor;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;

class Berserker extends BaseArmorEnchant {

    static int $id = 130;

    public function getLevel(int $level = 1) : int {
        return (($int = ceil($level / 2)) > 7) ? 8 : $int;
    }

    /**
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void {
        if ($victim->getHealth() < ($victim->getMaxHealth() / 2.5)) {
            $this->sendActivation($victim, "§bBerserker §aActivated!");
            $this->sendActivation($attacker, "§cStruck by §bBerserker §cEnchant!");
            $effect = new EffectInstance(VanillaEffects::STRENGTH(), $enchantmentLevel * 10, $this->getLevel($enchantmentLevel), false);
            $victim->getEffects()->add($effect);
        }
    }

}