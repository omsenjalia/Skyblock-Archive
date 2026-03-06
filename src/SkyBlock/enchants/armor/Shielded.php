<?php


namespace SkyBlock\enchants\armor;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;

class Shielded extends BaseArmorEnchant {

    static int $id = 127;

    public function getLevel(int $level = 1) : int {
        return (($int = ceil($level / 2)) > 7) ? 8 : $int;
    }

    /**
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void {
        $this->sendActivation($victim, "§bShielded §aActivated!");
        $this->sendActivation($attacker, "§cStruck by §bShielded §cEnchant!");
        $effect = new EffectInstance(VanillaEffects::RESISTANCE(), $enchantmentLevel * 2 * 20, $this->getLevel($enchantmentLevel), false);
        $victim->getEffects()->add($effect);
    }

}