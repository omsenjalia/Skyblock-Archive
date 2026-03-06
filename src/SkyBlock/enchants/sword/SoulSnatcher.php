<?php

namespace SkyBlock\enchants\sword;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;

class SoulSnatcher extends BaseMeleeEnchant {

    static int $id = 195;

    public function isApplicableTo(Player $holder) : bool {
        return true;
    }

    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        if (mt_rand(1, (100000 * ((100 - $enchantmentlevel) / 100))) === 1) {
            $this->sendActivation($attacker, "§cStruck by §bSoulSnatcher §cEnchant. You have been instantly killed!");
            $this->sendActivation($player, "§bSoulSnatcher §aActivated!");
            $this->pl->getServer()->broadcastMessage("§a§l»> " . $attacker->getDisplayName() . " has been instantly killed with §5SoulSnatcher §aby " . $player->getDisplayName() . "!");
            $attacker->kill();
        }
    }

}