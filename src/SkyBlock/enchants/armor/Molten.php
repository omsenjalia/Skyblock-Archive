<?php


namespace SkyBlock\enchants\armor;


use pocketmine\player\Player;

class Molten extends BaseArmorEnchant {

    static int $id = 123;

    /**
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void {
        $this->sendActivation($victim, "§bMolten §aActivated!");
        $this->sendActivation($attacker, "§cStruck by §bMolten §cEnchant!");
        if (!$attacker->isOnFire()) {
            $attacker->setOnFire($enchantmentLevel);
        }
    }

}