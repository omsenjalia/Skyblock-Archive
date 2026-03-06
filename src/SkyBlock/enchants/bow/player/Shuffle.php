<?php


namespace SkyBlock\enchants\bow\player;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;

class Shuffle extends BaseBowHitPlayerEnchant {

    static int $id = 121;

    /**
     * @param Player                    $attacker
     * @param Player                    $victim
     * @param int                       $enchantmentLevel
     * @param EntityDamageByEntityEvent $event
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel, EntityDamageByEntityEvent $event) : void {
        $pos1 = clone $attacker->getLocation();
        $pos2 = clone $victim->getLocation();
        if ($pos1->getWorld()->getDisplayName() === 'PvP' && $pos2->getWorld()->getDisplayName() === 'PvP') {
            if ($pos1->distance($pos2) <= 50) {
                $attacker->teleport($pos2->add(0, 1, 0));
                $victim->teleport($pos1->add(0, 1, 0));
                $this->sendActivation($attacker, "§bShuffle §aActivated!");
                $this->sendActivation($victim, "§cStruck by §bShuffle §cEnchant!");
                return;
            }
        }
        $this->sendActivation($attacker, "§bShuffle §cDeactivated, player too far!");
    }

}