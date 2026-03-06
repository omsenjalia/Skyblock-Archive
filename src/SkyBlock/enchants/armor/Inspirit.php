<?php


namespace SkyBlock\enchants\armor;


use pocketmine\player\Player;

class Inspirit extends BaseArmorEnchant {

    static int $id = 191;

    public function isApplicableTo(Player $holder) : bool {
        return true;
    }

    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void {
    }

}