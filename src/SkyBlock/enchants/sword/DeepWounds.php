<?php


namespace SkyBlock\enchants\sword;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;

class DeepWounds extends BaseMeleeEnchant {

    static int $id = 110;

    public function getLevel(int $level = 1) : int {
        return (($int = ceil($level / 2)) > 7) ? 8 : $int;
    }

    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $attacker->getEffects()->remove(VanillaEffects::REGENERATION());
        $attacker->getEffects()->remove(VanillaEffects::HEALTH_BOOST());
        $this->sendActivation($attacker, "§cStruck by §bDeep Wounds §cEnchant! Health Effects removed");
        $this->sendActivation($player, "§bDeep Wounds §aActivated!");
        $attacker->setOnFire((int) (($enchantmentlevel / 2) * 1.5)); // seconds not ticks
        $effect = new EffectInstance(VanillaEffects::NAUSEA(), $enchantmentlevel * 2 * 20, $this->getLevel($enchantmentlevel), false);
        $attacker->getEffects()->add($effect);
    }
}