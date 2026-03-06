<?php


namespace SkyBlock\enchants\armor;


use pocketmine\player\Player;

class Tank extends BaseArmorEnchant {

    static int $id = 145;

    /**
     * @param Player $holder
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder) : bool {
        return false;
    }

    /**
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void {
    }

}