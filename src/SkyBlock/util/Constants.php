<?php


namespace SkyBlock\util;


use alvin0319\CustomItemLoader\block\BlockIds;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockTypeIds;

interface Constants {

    public const CE_NONE = 0;
    public const CE_TIP = 1;
    public const CE_MSG = 2;

    /** @var int[] */
    public const ORE_BLOCKS
        = [
            BlockTypeIds::COAL_ORE,
            BlockTypeIds::COPPER_ORE,
            BlockTypeIds::IRON_ORE,
            BlockTypeIds::GOLD_ORE,
            BlockTypeIds::LAPIS_LAZULI_ORE,
            BlockTypeIds::NETHER_QUARTZ_ORE,
            BlockTypeIds::DIAMOND_ORE,
            BlockTypeIds::EMERALD_ORE,
            BlockTypeIds::ANCIENT_DEBRIS,
            BlockTypeIds::DEEPSLATE_COAL_ORE,
            BlockTypeIds::DEEPSLATE_COPPER_ORE,
            BlockTypeIds::DEEPSLATE_IRON_ORE,
            BlockTypeIds::DEEPSLATE_GOLD_ORE,
            BlockTypeIds::DEEPSLATE_LAPIS_LAZULI_ORE,
            BlockTypeIds::DEEPSLATE_DIAMOND_ORE,
            BlockTypeIds::DEEPSLATE_EMERALD_ORE,
            BlockTypeIds::QUARTZ,
            BlockTypeIds::NETHERITE,

        ];

    /** @var int[] */
    public const FARM_BLOCKS
        = [
            BlockTypeIds::MELON,
            BlockTypeIds::SUGARCANE,
            BlockTypeIds::CACTUS,
            BlockTypeIds::PUMPKIN,
            BlockTypeIds::CARROTS,
            BlockTypeIds::POTATOES,
            BlockTypeIds::BEETROOTS,
            BlockTypeIds::NETHER_WART,
            BlockTypeIds::WHEAT,
        ];

    /** @var int */
    public const ARMOR_TIER_CHAIN_MAX_DURABILITY = 166; // excludes all gold and leather armor

}