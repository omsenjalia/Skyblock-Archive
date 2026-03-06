<?php


namespace SkyBlock\enchants\armor;


use pocketmine\player\Player;

class LifeShield extends BaseArmorEnchant {

    static int $id = 186;

    /**
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void {
    }

}