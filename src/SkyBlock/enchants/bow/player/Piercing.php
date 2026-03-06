<?php


namespace SkyBlock\enchants\bow\player;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;

class Piercing extends BaseBowHitPlayerEnchant {

    static int $id = 120;

    public function isApplicableTo(Player $holder) : bool {
        return mt_rand(1, 15) === 1;
    }

    /**
     * @param Player                    $attacker
     * @param Player                    $victim
     * @param int                       $enchantmentLevel
     * @param EntityDamageByEntityEvent $event
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel, EntityDamageByEntityEvent $event) : void {
        $this->sendActivation($attacker, "§bPiercing §aActivated!");
        $this->sendActivation($victim, "§cStruck by §bPiercing §cEnchant!");
        $event->setModifier($event->getBaseDamage() * ($enchantmentLevel / 50), 120);
    }

}