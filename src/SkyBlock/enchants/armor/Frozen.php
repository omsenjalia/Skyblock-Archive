<?php


namespace SkyBlock\enchants\armor;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;

class Frozen extends BaseArmorEnchant {

    static int $id = 126;


    public function getLevel(int $level = 1) : int {
        if ($level > 12) {
            return 3;
        } else if ($level > 6) {
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
        $this->sendActivation($victim, "§bFrozen §aActivated!");
        $this->sendActivation($attacker, "§cStruck by §bFrozen §cEnchant!");
        $effect = new EffectInstance(VanillaEffects::SLOWNESS(), 20 * 3, $this->getLevel($enchantmentLevel), false);
        $attacker->getEffects()->add($effect);
        $effect = new EffectInstance(VanillaEffects::BLINDNESS(), $this->getDuration($enchantmentLevel), 1, false);
        $attacker->getEffects()->add($effect);
    }

}