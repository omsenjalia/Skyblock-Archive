<?php


namespace SkyBlock\enchants\armor;


use pocketmine\player\Player;

class Virtuous extends BaseArmorEnchant {

    static int $id = 133;

    /**
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void {
        $i = 0;
        $this->sendActivation($victim, "§bVirtuous §aActivated!");
        $this->sendActivation($attacker, "§cStruck by §bVirtuous §cEnchant!");
        $limit = ceil($this->getLevel($enchantmentLevel) / 2);
        foreach ($victim->getEffects()->all() as $effect) {
            if ($i < $limit) {
                if ($effect->getType()->isBad()) {
                    $victim->getEffects()->remove($effect->getType());
                    $i++;
                }
            }
        }
    }

}