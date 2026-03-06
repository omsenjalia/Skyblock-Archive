<?php

namespace SkyBlock\enchants\sword;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Durable;
use pocketmine\player\Player;

class Smasher extends BaseMeleeEnchant {

    static int $id = 196;

    public function isApplicableTo(Player $holder) : bool {
        return mt_rand(1, 35) === 1;
    }

    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $this->sendActivation($attacker, "§cStruck by §bSmasher §cEnchant! Your armor has been damaged.");
        $this->sendActivation($player, "§bSmasher §aActivated!");
        $damage = mt_rand(1, 3) * ceil($enchantmentlevel / 3);
        foreach ($attacker->getArmorInventory()->getContents() as $armor) {
            if ($armor instanceof Durable && mt_rand(1, 4) === 1) {
                $armor->applyDamage($damage);
            }
        }
    }


}