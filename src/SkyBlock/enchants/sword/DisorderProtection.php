<?php


namespace SkyBlock\enchants\sword;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;

class DisorderProtection extends BaseMeleeEnchant {

    static int $id = 135;

    /**
     * @param Player $holder
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder) : bool {
        return false;
    }

    /**
     * @param Player            $player
     * @param Player            $attacker
     * @param EntityDamageEvent $ev
     * @param int               $enchantmentlevel
     */
    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void { }

}