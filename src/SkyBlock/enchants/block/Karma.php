<?php


namespace SkyBlock\enchants\block;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\player\Player;
use SkyBlock\EvFunctions;
use SkyBlock\util\Constants;

class Karma extends BaseBlockBreakEnchant {

    static int $id = 187;

    /**
     * @param Player $holder
     * @param int    $level
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder, int $level = 0) : bool {
        return true;
    }

    /**
     * @param Player          $player
     * @param BlockBreakEvent $ev
     * @param int             $enchantmentlevel
     */
    public function onActivation(Player $player, BlockBreakEvent $ev, int $enchantmentlevel) : void {
        if ((in_array($ev->getBlock()->getTypeId(), Constants::ORE_BLOCKS, true)) or (in_array($ev->getBlock()->getTypeId(), Constants::FARM_BLOCKS, true) and EvFunctions::isFarmRipe($ev->getBlock()))) {
            $times = [0, 1, mt_rand(1, 2), 2, mt_rand(2, 3), 3, mt_rand(3, 4), 4, mt_rand(4, 5), 5, mt_rand(5, 6), 6, mt_rand(6, 7), 7, mt_rand(7, 8), 8];
            $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
            if (isset($times[$enchantmentlevel]))
                $user->addMana($times[$enchantmentlevel]);
        }
    }

}