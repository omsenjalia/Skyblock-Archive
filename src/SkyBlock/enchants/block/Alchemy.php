<?php

namespace SkyBlock\enchants\block;

use pocketmine\block\BlockTypeIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\player\Player;

class Alchemy extends BaseBlockBreakEnchant {

    static int $id = 199;

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
    public function onActivation(Player $player, BlockBreakEvent $ev, int $enchantmentlevel) : void {
        $block = $ev->getBlock();
        $blocks = [
            BlockTypeIds::COAL_ORE          => 1,
            BlockTypeIds::COPPER_ORE        => 2,
            BlockTypeIds::IRON_ORE          => 3,
            BlockTypeIds::LAPIS_LAZULI_ORE  => 4,
            BlockTypeIds::GOLD_ORE          => 5,
            BlockTypeIds::DIAMOND_ORE       => 6,
            BlockTypeIds::EMERALD_ORE       => 7,
            BlockTypeIds::NETHER_QUARTZ_ORE => 8,
            BlockTypeIds::ANCIENT_DEBRIS    => 9,

            BlockTypeIds::DEEPSLATE_COAL_ORE         => 10,
            BlockTypeIds::DEEPSLATE_COPPER_ORE       => 11,
            BlockTypeIds::DEEPSLATE_IRON_ORE         => 12,
            BlockTypeIds::DEEPSLATE_LAPIS_LAZULI_ORE => 13,
            BlockTypeIds::DEEPSLATE_GOLD_ORE         => 14,
            BlockTypeIds::DEEPSLATE_DIAMOND_ORE      => 15,
            BlockTypeIds::DEEPSLATE_EMERALD_ORE      => 16,
            BlockTypeIds::QUARTZ                     => 17,
            BlockTypeIds::NETHERITE                  => 18,
        ];
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        if (isset($blocks[$block->getTypeId()])) {
            $user->addMana($blocks[$block->getTypeId()]);
        } else {
            $user->addmana($blocks[$block->getTypeId()]);
        }
    }

}