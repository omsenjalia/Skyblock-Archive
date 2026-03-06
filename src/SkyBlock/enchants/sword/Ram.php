<?php

namespace SkyBlock\enchants\sword;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;

class Ram extends BaseMeleeEnchant {

    static int $id = 103;

    public function isApplicableTo(Player $holder) : bool {
        return mt_rand(1, 5) === 1;
    }

    /**
     * @param Player            $player
     * @param Player            $attacker
     * @param EntityDamageEvent $ev
     * @param int               $enchantmentlevel
     */
    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $this->sendActivation($attacker, "§cStruck by §bRam §cEnchant!");
        $this->sendActivation($player, "§bRam §aActivated!");
        if ($ev instanceof EntityDamageByEntityEvent)
            $ev->setKnockBack(0.2 * ($enchantmentlevel * 0.75));
    }

}