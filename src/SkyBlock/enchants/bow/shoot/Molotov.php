<?php


namespace SkyBlock\enchants\bow\shoot;


use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\player\Player;

class Molotov extends BaseBowShootEnchant {

    static int $id = 118;

    /**
     * @param Player              $attacker
     * @param int                 $enchantmentLevel
     * @param EntityShootBowEvent $event
     */
    public function onActivation(Player $attacker, int $enchantmentLevel, EntityShootBowEvent $event) : void {
        $this->sendActivation($attacker, "§bMolotov §aActivated!");
        $event->getProjectile()->setOnFire((int) ($enchantmentLevel * 1.5));
    }

}