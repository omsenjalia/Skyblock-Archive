<?php


namespace SkyBlock\enchants\armor;


use pocketmine\player\Player;

class Dispatch extends BaseArmorEnchant {

    static int $id = 144;

    /**
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void {
        if (5 >= $victim->getHealth()) {
            $this->sendActivation($victim, "§bDispatch §aActivated!");
            $this->sendActivation($attacker, "§cStruck by §bDispatch §cEnchant!");
            if (!isset($this->pl->notnt[strtolower($victim->getName())])) $this->pl->notnt[strtolower($victim->getName())] = strtolower($victim->getName());
            for ($i = $this->getLevel($enchantmentLevel); $i >= 0; $i--) {
                $this->pl->createTNT($victim->getPosition()->asVector3(), $victim->getWorld());
            }
        }
    }

}