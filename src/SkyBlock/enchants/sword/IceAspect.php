<?php


namespace SkyBlock\enchants\sword;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;

class IceAspect extends BaseMeleeEnchant {

    static int $id = 106;

    /**
     * @param int $level
     *
     * @return int
     */
    public function getLevel(int $level = 1) : int {
        return (($int = ceil($level / 2)) > 7) ? 8 : $int;
    }

    /**
     * @param Player            $player
     * @param Player            $attacker
     * @param EntityDamageEvent $ev
     * @param int               $enchantmentlevel
     */
    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $this->sendActivation($attacker, "§cStruck by §bIce Aspect §cEnchant!");
        $this->sendActivation($player, "§bIce Aspect §aActivated!");
        $effect = new EffectInstance(VanillaEffects::WEAKNESS(), $this->getDuration($enchantmentlevel), $this->getLevel($enchantmentlevel), false);
        $attacker->getEffects()->add($effect);
        $effect = new EffectInstance(VanillaEffects::BLINDNESS(), $this->getDuration($enchantmentlevel), $this->getLevel($enchantmentlevel), false);
        $attacker->getEffects()->add($effect);
    }

}