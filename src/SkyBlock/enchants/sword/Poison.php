<?php


namespace SkyBlock\enchants\sword;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;

class Poison extends BaseMeleeEnchant {

    static int $id = 104;

    public function getLevel(int $level = 1) : int {
        return (($int = ceil($level / 3)) > 5) ? 5 : $int;
    }

    /**
     * @param Player            $player
     * @param Player            $attacker
     * @param EntityDamageEvent $ev
     * @param int               $enchantmentlevel
     */
    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $this->sendActivation($attacker, "§cStruck by §bPoison §cEnchant!");
        $this->sendActivation($player, "§bPoison §aActivated!");
        $effect = new EffectInstance(VanillaEffects::POISON(), $enchantmentlevel * 2 * 20, $this->getLevel($enchantmentlevel), false);
        $attacker->getEffects()->add($effect);
    }

}