<?php


namespace SkyBlock\enchants\sword;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use SkyBlock\enchants\BaseEnchantment;

abstract class BaseMeleeEnchant extends BaseEnchantment {

    /**
     * @param Player $holder
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder) : bool {
        return mt_rand(1, 25) === 1;
    }

    public function getLevel(int $level = 1) : int {
        return (int) (ceil($level / 2));
    }

    /**
     * @param Player            $player
     * @param Player            $attacker
     * @param EntityDamageEvent $ev
     * @param int               $enchantmentlevel
     */
    abstract public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void;
}