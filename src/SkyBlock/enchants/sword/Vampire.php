<?php


namespace SkyBlock\enchants\sword;


use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;

class Vampire extends BaseMeleeEnchant {

    static int $id = 109;

    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $this->sendActivation($player, "§bVampire §aActivated!");
        $player->setHealth($player->getHealth() + (($ev->getFinalDamage() / 2) * (($enchantmentlevel - 1) / 100)));
    }

}