<?php


namespace SkyBlock\enchants\sword;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;

class Blind extends BaseMeleeEnchant {

    static int $id = 101;

    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $this->sendActivation($attacker, "§cStruck by §bBlind §cEnchant!");
        $this->sendActivation($player, "§bBlind §aActivated!");
        $effect = new EffectInstance(VanillaEffects::BLINDNESS(), $this->getDuration($enchantmentlevel), 5, false);
        $attacker->getEffects()->add($effect);
    }
}