<?php


namespace SkyBlock\enchants\armor;


use pocketmine\player\Player;

class Sharingan extends BaseArmorEnchant {

    static int $id = 188;

    /**
     * @param Player $holder
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder) : bool {
        return true;
    }

    /**
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void {
    }

}