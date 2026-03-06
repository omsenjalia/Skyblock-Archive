<?php

namespace SkyBlock\enchants\sword;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Potshot extends BaseMeleeEnchant {

    static int $id = 198;

    public function isApplicableTo(Player $holder) : bool {
        return mt_rand(1, 50) === 1;
    }

    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $this->sendActivation($attacker, "§cStruck by §bPotshot §cEnchant!");
        $this->sendActivation($player, "§bPotshot §aActivated!");

        foreach ($attacker->getWorld()->getPlayers() as $p) {
            if ($p !== $player && $p->getPosition()->distance($attacker->getPosition()) <= 10) {
                $p->setMotion(new Vector3(0, 1.5, 0));
                if ($p !== $attacker) {
                    $this->sendActivation($p, "§cStruck by §bPotshot §cEnchant!");
                }
            }
        }
    }

}