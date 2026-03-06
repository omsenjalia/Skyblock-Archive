<?php


namespace SkyBlock\enchants\sword;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;

class CripplingStrike extends BaseMeleeEnchant {

    static int $id = 108;

    public function getLevel(int $level = 1) : int {
        return (($int = ceil($level / 2)) > 7) ? 8 : $int;
    }

    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $this->sendActivation($attacker, "§cStruck by §bCrippling Strike §cEnchant!");
        $this->sendActivation($player, "§bCrippling Strike §aActivated!");
        $effect = new EffectInstance(VanillaEffects::HUNGER(), $enchantmentlevel * 2 * 20, $this->getLevel($enchantmentlevel), false);
        $attacker->getEffects()->add($effect);
        $effect = new EffectInstance(VanillaEffects::WEAKNESS(), $enchantmentlevel * 2 * 20, $this->getLevel($enchantmentlevel), false);
        $attacker->getEffects()->add($effect);
        $effect = new EffectInstance(VanillaEffects::NAUSEA(), $enchantmentlevel * 2 * 20, $this->getLevel($enchantmentlevel), false);
        $attacker->getEffects()->add($effect);
    }
}