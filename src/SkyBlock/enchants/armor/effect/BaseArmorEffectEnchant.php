<?php


namespace SkyBlock\enchants\armor\effect;


use pocketmine\player\Player;
use SkyBlock\enchants\BaseEnchantment;

abstract class BaseArmorEffectEnchant extends BaseEnchantment {

    public function getLevel(int $level = 1, int $diff = 0) : int {
        $level = floor($level / 2);
        if ($diff > 0) $level = ($level >= $diff) ? $level - $diff : $level;
        return (int) $level;
    }

    /**
     * @param Player $holder
     *
     * @return bool
     */
    abstract public function isApplicableTo(Player $holder) : bool;

    /**
     * Give effect to armor wearer
     *
     * @param Player $holder
     * @param int    $enchantmentLevel
     */
    abstract public function onEquip(Player $holder, int $enchantmentLevel) : void;
}