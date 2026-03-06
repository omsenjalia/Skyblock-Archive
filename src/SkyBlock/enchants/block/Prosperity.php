<?php

namespace SkyBlock\enchants\block;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\player\Player;

class Prosperity extends BaseBlockBreakEnchant {

    static int $id = 183;

    /**
     * @param Player $holder
     * @param int    $level
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder, int $level = 0) : bool {
        return false;
    }

    /**
     * @param Player          $player
     * @param BlockBreakEvent $ev
     * @param int             $enchantmentlevel
     */
    public function onActivation(Player $player, BlockBreakEvent $ev, int $enchantmentlevel) : void { }


}