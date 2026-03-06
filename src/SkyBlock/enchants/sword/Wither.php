<?php


namespace SkyBlock\enchants\sword;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;

class Wither extends BaseMeleeEnchant {

    static int $id = 113;

    public function getLevel(int $level = 1) : int {
        return (($int = ceil($level / 2)) > 7) ? 8 : $int;
    }

    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $this->sendActivation($attacker, "§cStruck by §bWither §cEnchant!");
        $this->sendActivation($player, "§bWither §aActivated!");
        $effect = new EffectInstance(VanillaEffects::NAUSEA(), $enchantmentlevel * 2 * 20, 1, false);
        $attacker->getEffects()->add($effect);
        $effect = new EffectInstance(VanillaEffects::WITHER(), $enchantmentlevel * 20, $this->getLevel($enchantmentlevel), false);
        $attacker->getEffects()->add($effect);
    }
}