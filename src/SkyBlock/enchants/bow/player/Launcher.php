<?php


namespace SkyBlock\enchants\bow\player;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;

class Launcher extends BaseBowHitPlayerEnchant {

    static int $id = 161;

    /**
     * @param Player                    $attacker
     * @param Player                    $victim
     * @param int                       $enchantmentLevel
     * @param EntityDamageByEntityEvent $event
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel, EntityDamageByEntityEvent $event) : void {
        $this->sendActivation($attacker, "§bLauncher §aActivated!");
        $this->sendActivation($victim, "§aStruck by §bLauncher §aEnchant!");
        $victim->knockBack($victim->getPosition()->getX() - $attacker->getPosition()->getX(), $victim->getPosition()->getZ() - $attacker->getPosition()->getZ(), $enchantmentLevel * 0.05);
    }

}