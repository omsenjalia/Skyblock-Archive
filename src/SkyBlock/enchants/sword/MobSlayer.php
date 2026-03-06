<?php


namespace SkyBlock\enchants\sword;


use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;

class MobSlayer extends BaseMeleeEnchant {

    static int $id = 190;

    public function isApplicableTo(Player $holder) : bool {
        return false;
    }

    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void { }

}