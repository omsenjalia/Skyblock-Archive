<?php


namespace SkyBlock\enchants\block;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\player\Player;

class Barter extends BaseBlockBreakEnchant {

    static int $id = 183;

    /**
     * @param Player $holder
     * @param int    $level
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder, int $level = 0) : bool {
        if ($level >= 10)
            return true;
        return mt_rand(1, (11 - $level)) === 1;
    }

    /**
     * @param Player          $player
     * @param BlockBreakEvent $ev
     * @param int             $enchantmentlevel
     */
    public function onActivation(Player $player, BlockBreakEvent $ev, int $enchantmentlevel) : void { }

}