<?php


namespace SkyBlock\enchants\sword;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;

class Charge extends BaseMeleeEnchant {

    static int $id = 111;

    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $this->sendActivation($player, "§bCharge §aActivated!");
        $effect = new EffectInstance(VanillaEffects::STRENGTH(), ($enchantmentlevel / 3) * 20, ceil($enchantmentlevel / 2), false);
        $ev->setBaseDamage($ev->getbaseDamage() + ($ev->getBaseDamage() * ((($enchantmentlevel - 1) * 4) / 100)));
        $player->getEffects()->add($effect);
    }

}