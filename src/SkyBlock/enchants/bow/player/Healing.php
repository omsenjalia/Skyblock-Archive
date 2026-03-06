<?php


namespace SkyBlock\enchants\bow\player;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;

class Healing extends BaseBowHitPlayerEnchant {

    static int $id = 122;

    public function isApplicableTo(Player $holder) : bool {
        return true;
    }

    /**
     * @param Player                    $attacker
     * @param Player                    $victim
     * @param int                       $enchantmentLevel
     * @param EntityDamageByEntityEvent $event
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel, EntityDamageByEntityEvent $event) : void {
        $this->sendActivation($victim, "§aStruck by §bHealing §aEnchant!");
        $this->sendActivation($attacker, "§bHealing §aActivated!");
        $victim->setHealth($victim->getHealth() + ($enchantmentLevel * 2 / 3));
        $event->cancel();
    }

}