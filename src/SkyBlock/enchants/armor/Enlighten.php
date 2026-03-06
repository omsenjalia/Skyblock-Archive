<?php


namespace SkyBlock\enchants\armor;


use pocketmine\player\Player;

class Enlighten extends BaseArmorEnchant {

    static int $id = 124;

    /**
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void {
        $this->sendActivation($victim, "§bEnlighten §aActivated!");
        $victim->setHealth($victim->getHealth() + ($enchantmentLevel / 40));
    }

}