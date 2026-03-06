<?php


namespace SkyBlock\enchants\armor;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;

class Cursed extends BaseArmorEnchant {

    static int $id = 128;

    public function getLevel(int $level = 1) : int {
        return (($int = ceil($level / 2)) > 7) ? 8 : $int;
    }

    public function isApplicableTo(Player $holder) : bool {
        return mt_rand(1, 35) === 1;
    }

    /**
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void {
        $attacker->getEffects()->remove(VanillaEffects::REGENERATION());
        $attacker->getEffects()->remove(VanillaEffects::HEALTH_BOOST());
        $this->sendActivation($victim, "§bCursed §aActivated!");
        $this->sendActivation($attacker, "§cStruck by §bCursed §cEnchant! Health Effects removed");
        $effect = new EffectInstance(VanillaEffects::WITHER(), $this->getDuration($enchantmentLevel), ceil($this->getLevel($enchantmentLevel) / 2), false);
        $attacker->getEffects()->add($effect);
    }

}