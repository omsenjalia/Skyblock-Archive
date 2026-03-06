<?php


namespace SkyBlock\enchants\armor;


use pocketmine\item\Sword;
use pocketmine\player\Player;

class Protector extends BaseArmorEnchant {

    static int $id = 146;

    /**
     * @param Player $holder
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder) : bool {
        return mt_rand(1, 10) === 1;
    }

    public function getLevel(int $level = 1) : int {
        return (($int = ceil($level / 2)) > 7) ? 8 : $int;
    }

    /**
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void {
        if ($attacker->getInventory()->getItemInHand() instanceof Sword) {
            $this->sendActivation($victim, "§bProtector §aActivated!");
            $this->sendActivation($attacker, "§cStruck by §bProtector §cEnchant!");
            $victim->setHealth($victim->getHealth() + 0.25 * $this->getLevel($enchantmentLevel));
        }
    }

}