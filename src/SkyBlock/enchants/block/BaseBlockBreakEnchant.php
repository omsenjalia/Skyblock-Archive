<?php


namespace SkyBlock\enchants\block;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\player\Player;
use SkyBlock\enchants\BaseEnchantment;

abstract class BaseBlockBreakEnchant extends BaseEnchantment {
    /**
     * @param Player $holder
     * @param int    $level
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder, int $level = 0) : bool {
        return mt_rand(1, 20) === 1;
    }

    /**
     * @param Player          $player
     * @param BlockBreakEvent $ev
     * @param int             $enchantmentlevel
     */
    abstract public function onActivation(Player $player, BlockBreakEvent $ev, int $enchantmentlevel) : void;
}