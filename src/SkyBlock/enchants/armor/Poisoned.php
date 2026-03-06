<?php


namespace SkyBlock\enchants\armor;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;

class Poisoned extends BaseArmorEnchant {

    static int $id = 125;

    /**
     * @param int $level
     *
     * @return int
     */
    public function getLevel(int $level = 1) : int {
        return (($int = ceil($level / 3)) > 5) ? 5 : $int;
    }

    /**
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void {
        $attacker->getEffects()->remove(VanillaEffects::REGENERATION());
        $attacker->getEffects()->remove(VanillaEffects::HEALTH_BOOST());
        $this->sendActivation($victim, "§bPoisoned §aActivated!");
        $this->sendActivation($attacker, "§cStruck by §bPoisoned §cEnchant! Health Effects removed");
        $effect = new EffectInstance(VanillaEffects::POISON(), $enchantmentLevel * 2 * 20, $this->getLevel($enchantmentLevel), false);
        $attacker->getEffects()->add($effect);
    }

}