<?php

namespace SkyBlock;

use alvin0319\CustomItemLoader\CustomItemLoader;
use alvin0319\CustomItemLoader\CustomItems;
use customiesdevs\customies\block\CustomiesBlockFactory;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemTypeIds;

class Data {

    public static int $sugarcaneFarming = 1;
    public static int $melonFarming = 35;
    public static int $pumpkinFarming = 10;
    public static int $carrotFarming = 8;
    public static int $potatoFarming = 7;
    public static int $beetrootFarming = 2;
    public static int $netherWartFarming = 6;
    public static int $wheatFarming = 4;

    public static int $sugarcaneMana = 1;
    public static int $beetrootMana = 5;
    public static int $wheatMana = 15;
    public static int $cactusMana = 15;
    public static int $netherWartMana = 30;
    public static int $potatoMana = 50;
    public static int $carrotMana = 65;
    public static int $pumpkinMana = 75;
    public static int $melonMana = 100;

    public static int $sugarcaneIslandLevel = 1;
    public static int $beetrootIslandLevel = 10;
    public static int $wheatIslandLevel = 25;
    public static int $cactusIslandLevel = 75;
    public static int $netherWartIslandLevel = 150;
    public static int $potatoIslandLevel = 300;
    public static int $carrotIslandLevel = 750;
    public static int $pumpkinIslandLevel = 1000;
    public static int $melonIslandLevel = 1500;


    public static int $coalOreMining = 1;
    public static int $copperOreMining = 2;
    public static int $ironOreMining = 3;
    public static int $lapisOreMining = 4;
    public static int $goldOreMining = 5;
    public static int $diamondOreMining = 6;
    public static int $emeraldOreMining = 7;
    public static int $netherQuartzMining = 8;
    public static int $ancientDebrisMining = 9;
    public static int $deepslateCoalOreMining = 10;
    public static int $deepslateCopperOreMining = 11;
    public static int $deepslateIronOreMining = 12;
    public static int $deepslateGoldOreMining = 13;
    public static int $deepslateLapisOreMining = 14;
    public static int $deepslateDiamondOreMining = 15;
    public static int $deepslateEmeraldOreMining = 16;
    public static int $quartzMining = 17;
    public static int $netheriteMining = 18;

    public static int $commandEffectPerLevel = 2500;
    public static int $commandFeedCost = 5000;
    public static int $commandHeadSellPerHead = 50;
    public static int $commandHealCost = 5000;
    public static int $commandRemoveVECost = 5000;
    public static int $commandFixCost = 15000;
    public static int $commandFixDefaultFix = 3;
    public static int $commandFixMaxFix = 10;
    public static int $commandEnchantCost = 10000;
    public static int $commandBragCost = 2000;
    public static int $commandVulcanCost = 10000;
    public static int $commandSurgeCost = 2500;
    public static int $commandRenewCost = 5000;
    public static int $commandRemoveCECost = 12500;
    public static int $commandMergerCost = 10000;
    public static int $commandMaxerCost = 10000;
    public static int $commandLevelupCost = 5000;
    public static int $commandInfernoCost = 10000;
    public static int $commandFixerCost = 7500;
    public static int $commandEnchanterCost = 10000;
    public static int $commandCarverCost = 10000;

    public static int $commandIslandRenameCost = 75000;

    public static int $chickenSpawnerCost = 500000;
    public static int $pigSpawnerCost = 750000;
    public static int $cowSpawnerCost = 1500000;
    public static int $sheepSpawnerCost = 2500000;
    public static int $squidSpawnerCost = 3000000;
    public static int $goatSpawnerCost = 4000000;
    public static int $glowSquidSpawnerCost = 4500000;
    public static int $camelSpawnerCost = 6000000;
    public static int $pandaSpawnerCost = 7500000;
    public static int $spiderSpawnerCost = 8000000;
    public static int $pigmanSpawnerCost = 10000000;
    public static int $zombieSpawnerCost = 12500000;
    public static int $skeletonSpawnerCost = 15000000;
    public static int $polarBearSpawnerCost = 16500000;
    public static int $creeperSpawnerCost = 17500000;
    public static int $silverFishSpawnerCost = 20000000;
    public static int $ironGolemSpawnerCost = 25000000;
    public static int $blazeSpawnerCost = 22500000;

    public static int $casinoChipCostCoinFlip = 500;
    public static int $casinoMaxCoinFlipBet = 100000;
    public static int $casinoMaxDiceRollBet = 100000;
    public static int $casinoChipCostDiceRoll = 1000;
    public static int $minCasinoBet = 500;

    public static array $shopPrices
        = [

        ];

    public static array $sellPrices
        = [
            //		MISC
            //		ItemTypeIds::ACACIA_BOAT => 1, // crafting
            //		ItemTypeIds::ACACIA_SIGN => 1, // crafting
            //		ItemTypeIds::ARROW => 1, // crafting
            //		ItemTypeIds::BANNER => 1, // crafting
            //		ItemTypeIds::BIRCH_BOAT => 1, // crafting
            //		ItemTypeIds::BIRCH_SIGN => 1, // crafting
            //		ItemTypeIds::BOOK => 1, // crafting
            //		ItemTypeIds::BOWL => 1, // crafting
            //		ItemTypeIds::BRICK => 1,  // crafting
            //		ItemTypeIds::BUCKET => 1, // crafting
            //		ItemTypeIds::CLAY => 1, // crafting
            //		ItemTypeIds::CHERRY_SIGN => 1, // crafting
            //		ItemTypeIds::CLOCK => 1, // crafting
            //		ItemTypeIds::CRIMSON_SIGN => 1, // crafting
            //		ItemTypeIds::CORAL_FAN => 1, // crafting
            //		ItemTypeIds::DARK_OAK_BOAT => 1, // crafting
            //		ItemTypeIds::DARK_OAK_SIGN => 1, // crafting
            //		ItemTypeIds::DYE => 1, // crafting
            //		ItemTypeIds::DISC_FRAGMENT_5 => 1, // crafting
            //		ItemTypeIds::DRAGON_BREATH => 1, // crafting
            //		ItemTypeIds::EXPERIENCE_BOTTLE => 1, // crafting
            //		ItemTypeIds::ECHO_SHARD => 1, // crafting
            //		ItemTypeIds::GLASS_BOTTLE => 1, // crafting
            //		ItemTypeIds::GLOWSTONE_DUST => 1, // crafting
            //		ItemTypeIds::JUNGLE_BOAT => 1, // crafting
            //		ItemTypeIds::JUNGLE_SIGN => 1, // crafting
            //		ItemTypeIds::LAVA_BUCKET => 1, // crafting
            //		ItemTypeIds::LINGERING_POTION => 1, // crafting
            //		ItemTypeIds::MILK_BUCKET => 1, // crafting
            //		ItemTypeIds::MINECART => 1, // crafting
            //		ItemTypeIds::MANGROVE_SIGN => 1, // crafting
            //		ItemTypeIds::MANGROVE_BOAT => 1, // crafting
            //		ItemTypeIds::NAME_TAG => 1, // crafting
            //		ItemTypeIds::NETHER_BRICK => 1, // crafting
            //		ItemTypeIds::OAK_BOAT => 1, // crafting
            //		ItemTypeIds::OAK_SIGN => 1, // crafting
            //		ItemTypeIds::PAINTING => 1, // crafting
            //		ItemTypeIds::POWDER_SNOW_BUCKET => 1, // crafting
            //		ItemTypeIds::PAPER => 1, // crafting
            //		ItemTypeIds::POTION => 1, // crafting
            //		ItemTypeIds::SNOWBALL => 1, // crafting
            //		ItemTypeIds::SPLASH_POTION => 1, // crafting
            //		ItemTypeIds::SPRUCE_BOAT => 1, // crafting
            //		ItemTypeIds::SPRUCE_SIGN => 1, // crafting
            //		ItemTypeIds::STICK => 1, // crafting
            //		ItemTypeIds::WATER_BUCKET => 1, // crafting
            //		ItemTypeIds::WRITABLE_BOOK => 1, // crafting
            //		ItemTypeIds::WARPED_SIGN => 1, // crafting
            //		ItemTypeIds::WRITTEN_BOOK => 1, // crafting

            //		EQUIPMENT

            ItemTypeIds::WOODEN_AXE     => 1, // equipment
            ItemTypeIds::WOODEN_HOE     => 1, // equipment
            ItemTypeIds::WOODEN_PICKAXE => 1, // equipment
            ItemTypeIds::WOODEN_SHOVEL  => 1, // equipment
            ItemTypeIds::WOODEN_SWORD   => 1, // equipment
            ItemTypeIds::LEATHER_BOOTS  => 1, // equipment
            ItemTypeIds::LEATHER_CAP    => 1, // equipment
            ItemTypeIds::LEATHER_PANTS  => 1, // equipment
            ItemTypeIds::LEATHER_TUNIC  => 1, // equipment

            ItemTypeIds::CHAINMAIL_BOOTS      => 2, // equipment
            ItemTypeIds::CHAINMAIL_CHESTPLATE => 2, // equipment
            ItemTypeIds::CHAINMAIL_HELMET     => 2, // equipment
            ItemTypeIds::CHAINMAIL_LEGGINGS   => 2, // equipment
            ItemTypeIds::STONE_AXE            => 2, // equipment
            ItemTypeIds::STONE_HOE            => 2, // equipment
            ItemTypeIds::STONE_PICKAXE        => 2, // equipment
            ItemTypeIds::STONE_SHOVEL         => 2, // equipment
            ItemTypeIds::STONE_SWORD          => 2, // equipment

            ItemTypeIds::IRON_AXE        => 4, // equipment
            ItemTypeIds::IRON_BOOTS      => 4, // equipment
            ItemTypeIds::IRON_CHESTPLATE => 4, // equipment
            ItemTypeIds::IRON_HELMET     => 4, // equipment
            ItemTypeIds::IRON_HOE        => 4, // equipment
            ItemTypeIds::IRON_LEGGINGS   => 4, // equipment
            ItemTypeIds::IRON_PICKAXE    => 4, // equipment
            ItemTypeIds::IRON_SHOVEL     => 4, // equipment
            ItemTypeIds::IRON_SWORD      => 4, // equipment

            ItemTypeIds::GOLDEN_AXE        => 6, // equipment
            ItemTypeIds::GOLDEN_BOOTS      => 6, // equipment
            ItemTypeIds::GOLDEN_CHESTPLATE => 6, // equipment
            ItemTypeIds::GOLDEN_HELMET     => 6, // equipment
            ItemTypeIds::GOLDEN_HOE        => 6, // equipment
            ItemTypeIds::GOLDEN_LEGGINGS   => 6, // equipment
            ItemTypeIds::GOLDEN_PICKAXE    => 6, // equipment
            ItemTypeIds::GOLDEN_SHOVEL     => 6, // equipment
            ItemTypeIds::GOLDEN_SWORD      => 6, // equipment

            ItemTypeIds::DIAMOND_AXE        => 8, // equipment
            ItemTypeIds::DIAMOND_BOOTS      => 8, // equipment
            ItemTypeIds::DIAMOND_CHESTPLATE => 8, // equipment
            ItemTypeIds::DIAMOND_HELMET     => 8, // equipment
            ItemTypeIds::DIAMOND_HOE        => 8, // equipment
            ItemTypeIds::DIAMOND_LEGGINGS   => 8, // equipment
            ItemTypeIds::DIAMOND_PICKAXE    => 8, // equipment
            ItemTypeIds::DIAMOND_SHOVEL     => 8, // equipment
            ItemTypeIds::DIAMOND_SWORD      => 8, // equipment

            ItemTypeIds::NETHERITE_AXE        => 10, // equipment
            ItemTypeIds::NETHERITE_HOE        => 10, // equipment
            ItemTypeIds::NETHERITE_PICKAXE    => 10, // equipment
            ItemTypeIds::NETHERITE_SHOVEL     => 10, // equipment
            ItemTypeIds::NETHERITE_SWORD      => 10, // equipment
            ItemTypeIds::NETHERITE_BOOTS      => 10, // equipment
            ItemTypeIds::NETHERITE_CHESTPLATE => 10, // equipment
            ItemTypeIds::NETHERITE_HELMET     => 10, // equipment
            ItemTypeIds::NETHERITE_LEGGINGS   => 10, // equipment

            ItemTypeIds::FISHING_ROD     => 2, // equipment
            ItemTypeIds::SHEARS          => 2, // equipment
            ItemTypeIds::BOW             => 2, // equipment
            ItemTypeIds::FLINT_AND_STEEL => 2, // equipment
            ItemTypeIds::COMPASS         => 2, // equipment
            ItemTypeIds::SPYGLASS        => 4, // equipment
            ItemTypeIds::TOTEM           => 6, // equipment
            ItemTypeIds::TURTLE_HELMET   => 8, // equipment
            //
            //		FARMING
            //
            //		ItemTypeIds::WHEAT => 1,
            //		ItemTypeIds::WHEAT_SEEDS => 1,
            //		ItemTypeIds::BEETROOT_SEEDS => 1,
            //		ItemTypeIds::BEETROOT => 1,
            //		ItemTypeIds::CARROT => 1,
            //		ItemTypeIds::POTATO => 1,
            //		ItemTypeIds::POISONOUS_POTATO => 1,
            //		-BlockTypeIds::MELON => 1,
            //		ItemTypeIds::MELON => 1,
            //		-BlockTypeIds::PUMPKIN => 1,
            //		ItemTypeIds::PUMPKIN_SEEDS => 1,
            //		ItemTypeIds::TORCHFLOWER_SEEDS => 1,
            //		-BlockTypeIds::TORCHFLOWER => 1,
            //		-BlockTypeIds::TORCHFLOWER_CROP => 1,
            //		ItemTypeIds::PITCHER_POD => 1,
            //		-BlockTypeIds::PITCHER_CROP => 1,
            //		-BlockTypeIds::PITCHER_PLANT => 1,
            //		ItemTypeIds::BAMBOO => 1,
            //		ItemTypeIds::COCOA_BEANS => 1,
            //		-BlockTypeIds::SUGARCANE => 1,
            //		ItemTypeIds::SWEET_BERRIES => 1,
            //		-BlockTypeIds::CACTUS => 1,
            //		-BlockTypeIds::RED_MUSHROOM => 1,
            //		-BlockTypeIds::BROWN_MUSHROOM => 1,
            //		-BlockTypeIds::BROWN_MUSHROOM_BLOCK => 1,
            //		-BlockTypeIds::RED_MUSHROOM_BLOCK => 1,
            //		-BlockTypeIds::SEA_PICKLE => 1,
            //		-BlockTypeIds::NETHER_WART => 1,
            //		ItemTypeIds::CHORUS_FRUIT => 1,
            //		ItemTypeIds::GLOW_BERRIES => 1,
            //		ItemTypeIds::APPLE => 1,
            //		ItemTypeIds::BAKED_POTATO => 1,
            //		ItemTypeIds::BEETROOT_SOUP => 1,
            //		ItemTypeIds::BREAD => 1,
            //		-BlockTypeIds::CAKE => 1,
            //		ItemTypeIds::COOKED_CHICKEN => 1,
            //		ItemTypeIds::COOKED_FISH => 1,
            //		ItemTypeIds::COOKED_MUTTON => 1,
            //		ItemTypeIds::COOKED_PORKCHOP => 1,
            //		ItemTypeIds::COOKED_RABBIT => 1,
            //		ItemTypeIds::COOKED_SALMON => 1,
            //		ItemTypeIds::COOKIE => 1,
            //		ItemTypeIds::DRIED_KELP => 1,
            //		ItemTypeIds::ENCHANTED_GOLDEN_APPLE => 1,
            //		ItemTypeIds::GOLDEN_APPLE => 1,
            //		ItemTypeIds::GOLDEN_CARROT => 1,
            //		ItemTypeIds::HONEY_BOTTLE => 1,
            //		ItemTypeIds::MUSHROOM_STEW => 1,
            //		ItemTypeIds::PUFFERFISH => 1,
            //		ItemTypeIds::PUMPKIN_PIE => 1,
            //		ItemTypeIds::RABBIT_STEW => 1,
            //		ItemTypeIds::RAW_BEEF => 1,
            //		ItemTypeIds::RAW_CHICKEN => 1,
            //		ItemTypeIds::RAW_FISH => 1,
            //		ItemTypeIds::RAW_MUTTON => 1,
            //		ItemTypeIds::RAW_PORKCHOP => 1,
            //		ItemTypeIds::RAW_RABBIT =>1,
            //		ItemTypeIds::RAW_SALMON => 1,
            //		ItemTypeIds::STEAK => 1,
            //		ItemTypeIds::SUSPICIOUS_STEW => 1,
            //		ItemTypeIds::CLOWNFISH => 1,
            //		ItemTypeIds::BAMBOO => 1,
            //
            ////		MINING

            ItemTypeIds::COAL   => 1,
            -BlockTypeIds::COAL => 9,

            ItemTypeIds::RAW_COPPER   => 1,
            ItemTypeIds::COPPER_INGOT => 2,
            -BlockTypeIds::COPPER     => 18,
            -BlockTypeIds::RAW_COPPER => 9,

            CustomItems::ZINC_INGOT       => 2,
            -CustomItemLoader::ZINC_BLOCK => 18,

            ItemTypeIds::RAW_IRON   => 2,
            ItemTypeIds::IRON_INGOT => 3,
            -BlockTypeIds::IRON     => 27,
            -BlockTypeIds::RAW_IRON => 18,

            ItemTypeIds::RAW_GOLD   => 3,
            ItemTypeIds::GOLD_INGOT => 4,
            -BlockTypeIds::GOLD     => 36,
            -BlockTypeIds::RAW_GOLD => 27,

            ItemTypeIds::REDSTONE_DUST => 2,
            -BlockTypeIds::REDSTONE    => 18,

            ItemTypeIds::LAPIS_LAZULI   => 3,
            -BlockTypeIds::LAPIS_LAZULI => 27,

            ItemTypeIds::DIAMOND   => 6,
            -BlockTypeIds::DIAMOND => 54,

            ItemTypeIds::EMERALD   => 7,
            -BlockTypeIds::EMERALD => 63,

            CustomItems::TOPAZ             => 9,
            -CustomItemLoader::TOPAZ_BLOCK => 81,

            ItemTypeIds::NETHER_QUARTZ => 10,
            -BlockTypeIds::QUARTZ      => 90,


            -BlockTypeIds::STONE             => 1,
            -BlockTypeIds::COBBLESTONE       => 1,
            -BlockTypeIds::DEEPSLATE         => 2,
            -BlockTypeIds::COBBLED_DEEPSLATE => 2,
            -BlockTypeIds::NETHERRACK        => 3,
            -BlockTypeIds::END_STONE         => 4,
            -BlockTypeIds::BLACKSTONE        => 5,
            -BlockTypeIds::PRISMARINE        => 6,
            -BlockTypeIds::AMETHYST          => 7,

            ////		MOBS

            ItemTypeIds::STRING          => 3,
            ItemTypeIds::FEATHER         => 3,
            ItemTypeIds::RAW_CHICKEN     => 4,
            ItemTypeIds::COOKED_CHICKEN  => 5,
            ItemTypeIds::RAW_FISH        => 6,
            ItemTypeIds::COOKED_FISH     => 8,
            ItemTypeIds::LEATHER         => 3,
            ItemTypeIds::RAW_BEEF        => 4,
            ItemTypeIds::STEAK           => 5,
            ItemTypeIds::GLOW_INK_SAC    => 3,
            ItemTypeIds::RAW_PORKCHOP    => 5,
            ItemTypeIds::COOKED_PORKCHOP => 6,
            ItemTypeIds::PUFFERFISH      => 15,
            ItemTypeIds::RAW_RABBIT      => 4,
            ItemTypeIds::COOKED_RABBIT   => 5,
            ItemTypeIds::RABBIT_HIDE     => 5,
            ItemTypeIds::RAW_SALMON      => 10,
            ItemTypeIds::COOKED_SALMON   => 10,
            ItemTypeIds::RAW_MUTTON      => 5,
            ItemTypeIds::COOKED_MUTTON   => 6,
            ItemTypeIds::BONE            => 2,
            ItemTypeIds::SNOWBALL        => 1,
            ItemTypeIds::INK_SAC         => 2,
            ItemTypeIds::CLOWNFISH       => 20,

            ItemTypeIds::SPIDER_EYE          => 3,
            ItemTypeIds::ENDER_PEARL         => 7,
            -BlockTypeIds::POPPY             => 1,
            ItemTypeIds::ROTTEN_FLESH        => 2,
            ItemTypeIds::BLAZE_ROD           => 9,
            ItemTypeIds::GUNPOWDER           => 4,
            ItemTypeIds::GOLD_NUGGET         => 0.4,
            ItemTypeIds::BONE_MEAL           => 0.2,
            ItemTypeIds::RABBIT_FOOT         => 5,
            ItemTypeIds::CARROT              => 3,
            ItemTypeIds::POTATO              => 3,
            ItemTypeIds::BAKED_POTATO        => 3,
            ItemTypeIds::PRISMARINE_SHARD    => 5,
            ItemTypeIds::PRISMARINE_CRYSTALS => 5,
            ItemTypeIds::GHAST_TEAR          => 16,
            ItemTypeIds::MAGMA_CREAM         => 8,
            ItemTypeIds::ARROW               => 1,
            ItemTypeIds::SLIMEBALL           => 5,
            ItemTypeIds::GLASS_BOTTLE        => 2,
            ItemTypeIds::GLOWSTONE_DUST      => 2,
            ItemTypeIds::STICK               => 0.25,
            ItemTypeIds::SUGAR               => 2,


            //		-BlockTypeIds::ACACIA_BUTTON => 1, // crafting
            //		-BlockTypeIds::ACACIA_DOOR => 1, // crafting
            //		-BlockTypeIds::ACACIA_FENCE => 1, // crafting
            //		-BlockTypeIds::ACACIA_FENCE_GATE => 1, // crafting
            //		-BlockTypeIds::ACACIA_LEAVES => 1, // crafting
            //		-BlockTypeIds::ACACIA_LOG => 1, // crafting
            //		-BlockTypeIds::ACACIA_PLANKS => 1, // crafting
            //		-BlockTypeIds::ACACIA_PRESSURE_PLATE => 1, // crafting
            //		-BlockTypeIds::ACACIA_SAPLING => 1, // crafting
            //		-BlockTypeIds::ACACIA_SIGN => 1, // crafting
            //		-BlockTypeIds::ACACIA_SLAB => 1, // crafting
            //		-BlockTypeIds::ACACIA_STAIRS => 1, // crafting
            //		-BlockTypeIds::ACACIA_TRAPDOOR => 1, // crafting
            //		-BlockTypeIds::ACACIA_WALL_SIGN => 1, // crafting
            //		-BlockTypeIds::ACACIA_WOOD => 1, // crafting
            //		-BlockTypeIds::ACTIVATOR_RAIL => 1, // crafting
            //		-BlockTypeIds::ALL_SIDED_MUSHROOM_STEM => 1, // crafting
            //		-BlockTypeIds::ALLIUM => 1, // crafting
            //		-BlockTypeIds::ANDESITE => 1, // crafting
            //		-BlockTypeIds::ANDESITE_SLAB => 1, // crafting
            //		-BlockTypeIds::ANDESITE_STAIRS => 1, // crafting
            //		-BlockTypeIds::ANDESITE_WALL => 1, // crafting
            //		-BlockTypeIds::ANVIL => 1, // crafting
            //		-BlockTypeIds::AZURE_BLUET => 1, // crafting
            //
            //		-BlockTypeIds::BAMBOO => 5, // farming
            //		-BlockTypeIds::BAMBOO_SAPLING => 5, // farming
            //		-BlockTypeIds::BANNER => 1, // crafting
            //		-BlockTypeIds::BARREL => 1, // crafting
            //		-BlockTypeIds::BARRIER => 1, // crafting
            //		-BlockTypeIds::BEACON => 1, // crafting
            //		-BlockTypeIds::BED => 1, // crafting
            //		-BlockTypeIds::BEDROCK => 1, // crafting
            //		-BlockTypeIds::BEETROOTS => 5, // farming
            //		-BlockTypeIds::BELL => 1, // crafting
            //		-BlockTypeIds::BIRCH_BUTTON => 1, // crafting
            //		-BlockTypeIds::BIRCH_DOOR => 1, // crafting
            //		-BlockTypeIds::BIRCH_FENCE => 1, // crafting
            //		-BlockTypeIds::BIRCH_FENCE_GATE => 1, // crafting
            //		-BlockTypeIds::BIRCH_LEAVES => 1, // crafting
            //		-BlockTypeIds::BIRCH_LOG => 1, // crafting
            //		-BlockTypeIds::BIRCH_PLANKS => 1, // crafting
            //		-BlockTypeIds::BIRCH_PRESSURE_PLATE => 1, // crafting
            //		-BlockTypeIds::BIRCH_SAPLING => 1, // crafting
            //		-BlockTypeIds::BIRCH_SIGN => 1, // crafting
            //		-BlockTypeIds::BIRCH_SLAB => 1, // crafting
            //		-BlockTypeIds::BIRCH_STAIRS => 1, // crafting
            //		-BlockTypeIds::BIRCH_TRAPDOOR => 1, // crafting
            //		-BlockTypeIds::BIRCH_WALL_SIGN => 1, // crafting
            //		-BlockTypeIds::BIRCH_WOOD => 1, // crafting
            //		-BlockTypeIds::BLAST_FURNACE => 1, // crafting
            //		-BlockTypeIds::BLUE_ICE => 1, // crafting
            //		-BlockTypeIds::BLUE_ORCHID => 1, // crafting
            //		-BlockTypeIds::BLUE_TORCH => 1, // crafting
            //		-BlockTypeIds::BONE_BLOCK => 1, // crafting
            //		-BlockTypeIds::BOOKSHELF => 1, // crafting
            //		-BlockTypeIds::BREWING_STAND => 1, // crafting
            //		-BlockTypeIds::BRICK_SLAB => 1, // crafting
            //		-BlockTypeIds::BRICK_STAIRS => 1, // crafting
            //		-BlockTypeIds::BRICK_WALL => 1, // crafting
            //		-BlockTypeIds::BRICKS => 1, // crafting
            //		-BlockTypeIds::BROWN_MUSHROOM => 5, // farming
            //		-BlockTypeIds::BROWN_MUSHROOM_BLOCK => 1, // crafting
            //
            //		-BlockTypeIds::CACTUS => 5, // farming
            //		-BlockTypeIds::CAKE => 1, // crafting
            //		-BlockTypeIds::CARPET => 1, // crafting
            //		-BlockTypeIds::CARROTS => 5, // farming
            //		-BlockTypeIds::CARVED_PUMPKIN => 1, // crafting
            //		-BlockTypeIds::CHEST => 1, // crafting
            //		-BlockTypeIds::CHISELED_QUARTZ => 1, // crafting
            //		-BlockTypeIds::CHISELED_RED_SANDSTONE => 1, // crafting
            //		-BlockTypeIds::CHISELED_SANDSTONE => 1, // crafting
            //		-BlockTypeIds::CHISELED_STONE_BRICKS => 1, // crafting
            //		-BlockTypeIds::CLAY => 1, // crafting
            //		-BlockTypeIds::COAL => 5, // mining
            //		-BlockTypeIds::COAL_ORE => 5, // mining
            //		-BlockTypeIds::COBBLESTONE => 5, // mining
            //		-BlockTypeIds::COBBLESTONE_SLAB => 1, // crafting
            //		-BlockTypeIds::COBBLESTONE_STAIRS => 1, // crafting
            //		-BlockTypeIds::COBBLESTONE_WALL => 1, // crafting
            //		-BlockTypeIds::COBWEB => 1, // crafting
            //		-BlockTypeIds::COCOA_POD => 5, // farming
            //		-BlockTypeIds::CONCRETE => 1, // crafting
            //		-BlockTypeIds::CONCRETE_POWDER => 1, // crafting
            //		-BlockTypeIds::CORAL => 1, // crafting
            //		-BlockTypeIds::CORAL_BLOCK => 1, // crafting
            //		-BlockTypeIds::CORAL_FAN => 1, // crafting
            //		-BlockTypeIds::CORNFLOWER => 1, // crafting
            //		-BlockTypeIds::CRACKED_STONE_BRICKS => 1, // crafting
            //		-BlockTypeIds::CRAFTING_TABLE => 1, // crafting
            //		-BlockTypeIds::CUT_RED_SANDSTONE => 1, // crafting
            //		-BlockTypeIds::CUT_RED_SANDSTONE_SLAB => 1, // crafting
            //		-BlockTypeIds::CUT_SANDSTONE => 1, // crafting
            //		-BlockTypeIds::CUT_SANDSTONE_SLAB => 1, // crafting
            //
            //		-BlockTypeIds::DANDELION => 1, // crafting
            //		-BlockTypeIds::DARK_OAK_BUTTON => 1, // crafting
            //		-BlockTypeIds::DARK_OAK_DOOR => 1, // crafting
            //		-BlockTypeIds::DARK_OAK_FENCE => 1, // crafting
            //		-BlockTypeIds::DARK_OAK_FENCE_GATE => 1, // crafting
            //		-BlockTypeIds::DARK_OAK_LEAVES => 1, // crafting
            //		-BlockTypeIds::DARK_OAK_LOG => 1, // crafting
            //		-BlockTypeIds::DARK_OAK_PLANKS => 1, // crafting
            //		-BlockTypeIds::DARK_OAK_PRESSURE_PLATE => 1, // crafting
            //		-BlockTypeIds::DARK_OAK_SAPLING => 1, // crafting
            //		-BlockTypeIds::DARK_OAK_SIGN => 1, // crafting
            //		-BlockTypeIds::DARK_OAK_SLAB => 1, // crafting
            //		-BlockTypeIds::DARK_OAK_STAIRS => 1, // crafting
            //		-BlockTypeIds::DARK_OAK_TRAPDOOR => 1, // crafting
            //		-BlockTypeIds::DARK_OAK_WALL_SIGN => 1, // crafting
            //		-BlockTypeIds::DARK_OAK_WOOD => 1, // crafting
            //		-BlockTypeIds::DARK_PRISMARINE => 1, // crafting
            //		-BlockTypeIds::DARK_PRISMARINE_SLAB => 1, // crafting
            //		-BlockTypeIds::DARK_PRISMARINE_STAIRS => 1, // crafting
            //		-BlockTypeIds::DAYLIGHT_SENSOR => 1, // crafting
            //		-BlockTypeIds::DEAD_BUSH => 1, // crafting
            //		-BlockTypeIds::DETECTOR_RAIL => 1, // crafting
            //		-BlockTypeIds::DIAMOND => 5, // mining
            //		-BlockTypeIds::DIAMOND_ORE => 5, // mining
            //		-BlockTypeIds::DIORITE => 1, // crafting
            //		-BlockTypeIds::DIORITE_SLAB => 1, // crafting
            //		-BlockTypeIds::DIORITE_STAIRS => 1, // crafting
            //		-BlockTypeIds::DIORITE_WALL => 1, // crafting
            //		-BlockTypeIds::DIRT => 1, // crafting
            //		-BlockTypeIds::DOUBLE_TALLGRASS => 1, // crafting
            //		-BlockTypeIds::DRAGON_EGG => 1, // crafting
            //		-BlockTypeIds::DRIED_KELP => 5, // farming
            //		-BlockTypeIds::DYED_SHULKER_BOX => 1, // crafting
            //
            //		-BlockTypeIds::EMERALD => 5, // mining
            //		-BlockTypeIds::EMERALD_ORE => 5, // mining
            //		-BlockTypeIds::ENCHANTING_TABLE => 1, // crafting
            //		-BlockTypeIds::END_PORTAL_FRAME => 1, // crafting
            //		-BlockTypeIds::END_ROD => 1, // crafting
            //		-BlockTypeIds::END_STONE => 5, // mining
            //		-BlockTypeIds::END_STONE_BRICK_SLAB => 1, // crafting
            //		-BlockTypeIds::END_STONE_BRICK_STAIRS => 1, // crafting
            //		-BlockTypeIds::END_STONE_BRICK_WALL => 1, // crafting
            //		-BlockTypeIds::END_STONE_BRICKS => 1, // crafting
            //		-BlockTypeIds::ENDER_CHEST => 1, // crafting
            //
            //		-BlockTypeIds::FERN => 1, // crafting
            //		-BlockTypeIds::FLETCHING_TABLE => 1, // crafting
            //		-BlockTypeIds::FLOWER_POT => 1, // crafting
            //		-BlockTypeIds::FURNACE => 1, // crafting
            //
            //		-BlockTypeIds::GLASS => 1, // crafting
            //		-BlockTypeIds::GLASS_PANE => 1, // crafting
            //		-BlockTypeIds::GLOWING_OBSIDIAN => 1, // crafting
            //		-BlockTypeIds::GLOWSTONE => 1, // crafting
            //		-BlockTypeIds::GOLD => 5, // mining
            //		-BlockTypeIds::GOLD_ORE => 5, // mining
            //		-BlockTypeIds::GRANITE => 1, // crafting
            //		-BlockTypeIds::GRANITE_SLAB => 1, // crafting
            //		-BlockTypeIds::GRANITE_STAIRS => 1, // crafting
            //		-BlockTypeIds::GRANITE_WALL => 1, // crafting
            //		-BlockTypeIds::GRASS => 1, // crafting
            //		-BlockTypeIds::GRASS_PATH => 1, // crafting
            //		-BlockTypeIds::GRAVEL => 1, // crafting
            //
            //		-BlockTypeIds::HARDENED_CLAY => 1, // crafting
            //		-BlockTypeIds::HAY_BALE => 5, // crafting
            //		-BlockTypeIds::HOPPER => 1, // crafting
            //
            //		-BlockTypeIds::ICE => 1, // crafting
            //		-BlockTypeIds::IRON => 5, // crafting
            //		-BlockTypeIds::IRON_BARS => 1, // crafting
            //		-BlockTypeIds::IRON_DOOR => 1, // crafting
            //		-BlockTypeIds::IRON_ORE => 5, // crafting
            //		-BlockTypeIds::IRON_TRAPDOOR => 1, // crafting
            //		-BlockTypeIds::ITEM_FRAME => 1, // crafting
            //
            //		-BlockTypeIds::JUKEBOX => 1, // crafting
            //		-BlockTypeIds::JUNGLE_BUTTON => 1, // crafting
            //		-BlockTypeIds::JUNGLE_DOOR => 1, // crafting
            //		-BlockTypeIds::JUNGLE_FENCE => 1, // crafting
            //		-BlockTypeIds::JUNGLE_FENCE_GATE => 1, // crafting
            //		-BlockTypeIds::JUNGLE_LEAVES => 1, // crafting
            //		-BlockTypeIds::JUNGLE_LOG => 1, // crafting
            //		-BlockTypeIds::JUNGLE_PLANKS => 1, // crafting
            //		-BlockTypeIds::JUNGLE_PRESSURE_PLATE => 1, // crafting
            //		-BlockTypeIds::JUNGLE_SAPLING => 1, // crafting
            //		-BlockTypeIds::JUNGLE_SIGN => 1, // crafting
            //		-BlockTypeIds::JUNGLE_SLAB => 1, // crafting
            //		-BlockTypeIds::JUNGLE_STAIRS => 1, // crafting
            //		-BlockTypeIds::JUNGLE_TRAPDOOR => 1, // crafting
            //		-BlockTypeIds::JUNGLE_WALL_SIGN => 1, // crafting
            //		-BlockTypeIds::JUNGLE_WOOD => 1, // crafting
            //
            //		-BlockTypeIds::LADDER => 1, // crafting
            //		-BlockTypeIds::LANTERN => 1, // crafting
            //		-BlockTypeIds::LAPIS_LAZULI => 5, // mining
            //		-BlockTypeIds::LAPIS_LAZULI_ORE => 5, // mining
            //		-BlockTypeIds::LARGE_FERN => 1, // crafting
            //		-BlockTypeIds::LECTERN => 1, // crafting
            //		-BlockTypeIds::LEVER => 1, // crafting
            //		-BlockTypeIds::LILAC => 1, // crafting
            //		-BlockTypeIds::LILY_OF_THE_VALLEY => 1, // crafting
            //		-BlockTypeIds::LILY_PAD => 1, // crafting
            //		-BlockTypeIds::LOOM => 1, // crafting
            //
            //		-BlockTypeIds::MAGMA => 1, // crafting
            //		-BlockTypeIds::MELON => 5, // farming
            //		-BlockTypeIds::MELON_STEM => 5, // farming
            //		-BlockTypeIds::MOSSY_COBBLESTONE => 1, // crafting
            //		-BlockTypeIds::MOSSY_COBBLESTONE_SLAB => 1, // crafting
            //		-BlockTypeIds::MOSSY_COBBLESTONE_STAIRS => 1, // crafting
            //		-BlockTypeIds::MOSSY_COBBLESTONE_WALL => 1, // crafting
            //		-BlockTypeIds::MOSSY_STONE_BRICK_SLAB => 1, // crafting
            //		-BlockTypeIds::MOSSY_STONE_BRICK_STAIRS => 1, // crafting
            //		-BlockTypeIds::MOSSY_STONE_BRICK_WALL => 1, // crafting
            //		-BlockTypeIds::MOSSY_STONE_BRICKS => 1, // crafting
            //		-BlockTypeIds::MUSHROOM_STEM => 5, // mining
            //		-BlockTypeIds::MYCELIUM => 1, // crafting
            //
            //		-BlockTypeIds::NETHER_BRICK_FENCE => 1, // crafting
            //		-BlockTypeIds::NETHER_BRICK_SLAB => 1, // crafting
            //		-BlockTypeIds::NETHER_BRICK_STAIRS => 1, // crafting
            //		-BlockTypeIds::NETHER_BRICK_WALL => 1, // crafting
            //		-BlockTypeIds::NETHER_BRICKS => 1, // crafting
            //		-BlockTypeIds::NETHER_PORTAL => 1, // crafting
            //		-BlockTypeIds::NETHER_QUARTZ_ORE => 5, // mining
            //		-BlockTypeIds::NETHER_WART => 5, // farming
            //		-BlockTypeIds::NETHER_WART_BLOCK => 1, // crafting
            //		-BlockTypeIds::NETHERRACK => 5, // mining
            //		-BlockTypeIds::NOTE_BLOCK => 1, // crafting
            //
            //		-BlockTypeIds::OAK_BUTTON => 1, // crafting
            //		-BlockTypeIds::OAK_DOOR => 1, // crafting
            //		-BlockTypeIds::OAK_FENCE => 1, // crafting
            //		-BlockTypeIds::OAK_FENCE_GATE => 1, // crafting
            //		-BlockTypeIds::OAK_LEAVES => 1, // crafting
            //		-BlockTypeIds::OAK_LOG => 1, // crafting
            //		-BlockTypeIds::OAK_PLANKS => 1, // crafting
            //		-BlockTypeIds::OAK_PRESSURE_PLATE => 1, // crafting
            //		-BlockTypeIds::OAK_SAPLING => 1, // crafting
            //		-BlockTypeIds::OAK_SIGN => 1, // crafting
            //		-BlockTypeIds::OAK_SLAB => 1, // crafting
            //		-BlockTypeIds::OAK_STAIRS => 1, // crafting
            //		-BlockTypeIds::OAK_TRAPDOOR => 1, // crafting
            //		-BlockTypeIds::OAK_WALL_SIGN => 1, // crafting
            //		-BlockTypeIds::OAK_WOOD => 1, // crafting
            //		-BlockTypeIds::OBSIDIAN => 1, // crafting
            //		-BlockTypeIds::ORANGE_TULIP => 1, // crafting
            //		-BlockTypeIds::OXEYE_DAISY => 1, // crafting
            //
            //		-BlockTypeIds::PACKED_ICE => 1, // crafting
            //		-BlockTypeIds::PEONY => 1, // crafting
            //		-BlockTypeIds::PINK_TULIP => 1, // crafting
            //		-BlockTypeIds::PODZOL => 1, // crafting
            //		-BlockTypeIds::POLISHED_ANDESITE => 1, // crafting
            //		-BlockTypeIds::POLISHED_ANDESITE_SLAB => 1, // crafting
            //		-BlockTypeIds::POLISHED_ANDESITE_STAIRS => 1, // crafting
            //		-BlockTypeIds::POLISHED_DIORITE => 1, // crafting
            //		-BlockTypeIds::POLISHED_DIORITE_SLAB => 1, // crafting
            //		-BlockTypeIds::POLISHED_DIORITE_STAIRS => 1, // crafting
            //		-BlockTypeIds::POLISHED_GRANITE => 1, // crafting
            //		-BlockTypeIds::POLISHED_GRANITE_SLAB => 1, // crafting
            //		-BlockTypeIds::POLISHED_GRANITE_STAIRS => 1, // crafting
            //		-BlockTypeIds::POPPY => 1, // crafting
            //		-BlockTypeIds::POTATOES => 5, // farming
            //		-BlockTypeIds::POWERED_RAIL => 1, // crafting
            //		-BlockTypeIds::PRISMARINE => 5, // mining
            //		-BlockTypeIds::PRISMARINE_BRICKS => 1, // crafting
            //		-BlockTypeIds::PRISMARINE_BRICKS_SLAB => 1, // crafting
            //		-BlockTypeIds::PRISMARINE_BRICKS_STAIRS => 1, // crafting
            //		-BlockTypeIds::PRISMARINE_SLAB => 1, // crafting
            //		-BlockTypeIds::PRISMARINE_STAIRS => 1, // crafting
            //		-BlockTypeIds::PRISMARINE_WALL => 1, // crafting
            //		-BlockTypeIds::PUMPKIN => 5, // farming
            //		-BlockTypeIds::PUMPKIN_STEM => 5, // farming
            //		-BlockTypeIds::PURPUR => 1, // crafting
            //		-BlockTypeIds::PURPUR_PILLAR => 1, // crafting
            //		-BlockTypeIds::PURPUR_SLAB => 1, // crafting
            //		-BlockTypeIds::PURPUR_STAIRS => 1, // crafting
            //
            //		-BlockTypeIds::QUARTZ => 1, // crafting
            //		-BlockTypeIds::QUARTZ_PILLAR => 1, // crafting
            //		-BlockTypeIds::QUARTZ_SLAB => 1, // crafting
            //		-BlockTypeIds::QUARTZ_STAIRS => 1, // crafting
            //
            //		-BlockTypeIds::RAIL => 1, // crafting
            //		-BlockTypeIds::RED_MUSHROOM => 5, // farming
            //		-BlockTypeIds::RED_MUSHROOM_BLOCK => 5, // farming
            //		-BlockTypeIds::RED_NETHER_BRICK_SLAB => 1, // crafting
            //		-BlockTypeIds::RED_NETHER_BRICK_STAIRS => 1, // crafting
            //		-BlockTypeIds::RED_NETHER_BRICK_WALL => 1, // crafting
            //		-BlockTypeIds::RED_NETHER_BRICKS => 1, // crafting
            //		-BlockTypeIds::RED_SAND => 1, // crafting
            //		-BlockTypeIds::RED_SANDSTONE => 1, // crafting
            //		-BlockTypeIds::RED_SANDSTONE_SLAB => 1, // crafting
            //		-BlockTypeIds::RED_SANDSTONE_STAIRS => 1, // crafting
            //		-BlockTypeIds::RED_SANDSTONE_WALL => 1, // crafting
            //		-BlockTypeIds::RED_TULIP => 1, // crafting
            //		-BlockTypeIds::REDSTONE => 5, // mining
            //		-BlockTypeIds::REDSTONE_COMPARATOR => 1, // crafting
            //		-BlockTypeIds::REDSTONE_LAMP => 1, // crafting
            //		-BlockTypeIds::REDSTONE_ORE => 1, // crafting
            //		-BlockTypeIds::REDSTONE_REPEATER => 1, // crafting
            //		-BlockTypeIds::REDSTONE_TORCH => 1, // crafting
            //		-BlockTypeIds::REDSTONE_WIRE => 1, // crafting
            //		-BlockTypeIds::ROSE_BUSH => 5, // farming
            //
            //		-BlockTypeIds::SAND => 1, // crafting
            //		-BlockTypeIds::SANDSTONE => 1, // crafting
            //		-BlockTypeIds::SANDSTONE_SLAB => 1, // crafting
            //		-BlockTypeIds::SANDSTONE_STAIRS => 1, // crafting
            //		-BlockTypeIds::SANDSTONE_WALL => 1, // crafting
            //		-BlockTypeIds::SEA_LANTERN => 1, // crafting
            //		-BlockTypeIds::SEA_PICKLE => 1, // crafting
            //		-BlockTypeIds::SHULKER_BOX => 1, // crafting
            //		-BlockTypeIds::SLIME => 5, // mobs
            //		-BlockTypeIds::SMOKER => 1, // crafting
            //		-BlockTypeIds::SMOOTH_QUARTZ => 1, // crafting
            //		-BlockTypeIds::SMOOTH_QUARTZ_SLAB => 1, // crafting
            //		-BlockTypeIds::SMOOTH_QUARTZ_STAIRS => 1, // crafting
            //		-BlockTypeIds::SMOOTH_RED_SANDSTONE => 1, // crafting
            //		-BlockTypeIds::SMOOTH_RED_SANDSTONE_SLAB => 1, // crafting
            //		-BlockTypeIds::SMOOTH_RED_SANDSTONE_STAIRS => 1, // crafting
            //		-BlockTypeIds::SMOOTH_SANDSTONE => 1, // crafting
            //		-BlockTypeIds::SMOOTH_SANDSTONE_SLAB => 1, // crafting
            //		-BlockTypeIds::SMOOTH_SANDSTONE_STAIRS => 1, // crafting
            //		-BlockTypeIds::SMOOTH_STONE => 1, // crafting
            //		-BlockTypeIds::SMOOTH_STONE_SLAB => 1, // crafting
            //		-BlockTypeIds::SNOW => 1, // crafting
            //		-BlockTypeIds::SNOW_LAYER => 1, // crafting
            //		-BlockTypeIds::SOUL_SAND => 1, // crafting
            //		-BlockTypeIds::SPONGE => 5, // mobs
            //		-BlockTypeIds::SPRUCE_BUTTON => 1, // crafting
            //		-BlockTypeIds::SPRUCE_DOOR => 1, // crafting
            //		-BlockTypeIds::SPRUCE_FENCE => 1, // crafting
            //		-BlockTypeIds::SPRUCE_FENCE_GATE => 1, // crafting
            //		-BlockTypeIds::SPRUCE_LEAVES => 1, // crafting
            //		-BlockTypeIds::SPRUCE_LOG => 1, // crafting
            //		-BlockTypeIds::SPRUCE_PLANKS => 1, // crafting
            //		-BlockTypeIds::SPRUCE_PRESSURE_PLATE => 1, // crafting
            //		-BlockTypeIds::SPRUCE_SAPLING => 1, // crafting
            //		-BlockTypeIds::SPRUCE_SIGN => 1, // crafting
            //		-BlockTypeIds::SPRUCE_SLAB => 1, // crafting
            //		-BlockTypeIds::SPRUCE_STAIRS => 1, // crafting
            //		-BlockTypeIds::SPRUCE_TRAPDOOR => 1, // crafting
            //		-BlockTypeIds::SPRUCE_WALL_SIGN => 1, // crafting
            //		-BlockTypeIds::SPRUCE_WOOD => 1, // crafting
            //		-BlockTypeIds::STAINED_CLAY => 1, // crafting
            //		-BlockTypeIds::STAINED_GLASS => 1, // crafting
            //		-BlockTypeIds::STAINED_GLASS_PANE => 1, // crafting
            //		-BlockTypeIds::STAINED_HARDENED_GLASS => 1, // crafting
            //		-BlockTypeIds::STAINED_HARDENED_GLASS_PANE => 1, // crafting
            //		-BlockTypeIds::STONE => 1, // crafting
            //		-BlockTypeIds::STONE_BRICK_SLAB => 1, // crafting
            //		-BlockTypeIds::STONE_BRICK_STAIRS => 1, // crafting
            //		-BlockTypeIds::STONE_BRICK_WALL => 1, // crafting
            //		-BlockTypeIds::STONE_BRICKS => 1, // crafting
            //		-BlockTypeIds::STONE_BUTTON => 1, // crafting
            //		-BlockTypeIds::STONE_PRESSURE_PLATE => 1, // crafting
            //		-BlockTypeIds::STONE_SLAB => 1, // crafting
            //		-BlockTypeIds::STONE_STAIRS => 1, // crafting
            //		-BlockTypeIds::STONECUTTER => 1, // crafting
            //		-BlockTypeIds::SUGARCANE => 5, // farming
            //		-BlockTypeIds::SUNFLOWER => 1, // crafting
            //		-BlockTypeIds::SWEET_BERRY_BUSH => 5, // farming
            //
            //		-BlockTypeIds::TALL_GRASS => 1, // crafting
            //		-BlockTypeIds::TNT => 1, // crafting
            //		-BlockTypeIds::TORCH => 1, // crafting
            //		-BlockTypeIds::TRAPPED_CHEST => 1, // crafting
            //		-BlockTypeIds::TRIPWIRE => 1, // crafting
            //		-BlockTypeIds::TRIPWIRE_HOOK => 1, // crafting
            //
            //		-BlockTypeIds::VINES => 1, // crafting
            //
            //		-BlockTypeIds::WALL_BANNER => 1, // crafting
            //		-BlockTypeIds::WALL_CORAL_FAN => 1, // crafting
            //		-BlockTypeIds::WATER => 1, // crafting
            //		-BlockTypeIds::WEIGHTED_PRESSURE_PLATE_HEAVY => 1, // crafting
            //		-BlockTypeIds::WEIGHTED_PRESSURE_PLATE_LIGHT => 1, // crafting
            //		-BlockTypeIds::WHEAT => 5, // farming
            //
            //		-BlockTypeIds::BUDDING_AMETHYST => 1, // crafting
            //		-BlockTypeIds::WHITE_TULIP => 1, // crafting
            //		-BlockTypeIds::WOOL => 1, // crafting
            //		-BlockTypeIds::AMETHYST_CLUSTER => 1, // crafting
            //		-BlockTypeIds::GLAZED_TERRACOTTA => 1, // crafting
            //		-BlockTypeIds::AMETHYST => 5, // mining
            //		-BlockTypeIds::ANCIENT_DEBRIS => 5, // mining
            //		-BlockTypeIds::BASALT => 1, // crafting
            //		-BlockTypeIds::POLISHED_BASALT => 1, // crafting
            //		-BlockTypeIds::SMOOTH_BASALT => 1, // crafting
            //		-BlockTypeIds::BLACKSTONE => 5, // mining
            //		-BlockTypeIds::BLACKSTONE_SLAB => 1, // crafting
            //		-BlockTypeIds::BLACKSTONE_STAIRS => 1, // crafting
            //		-BlockTypeIds::BLACKSTONE_WALL => 1, // crafting
            //		-BlockTypeIds::POLISHED_BLACKSTONE => 1, // crafting
            //		-BlockTypeIds::POLISHED_BLACKSTONE_BUTTON => 1, // crafting
            //		-BlockTypeIds::POLISHED_BLACKSTONE_PRESSURE_PLATE => 1, // crafting
            //		-BlockTypeIds::POLISHED_BLACKSTONE_SLAB => 1, // crafting
            //		-BlockTypeIds::POLISHED_BLACKSTONE_STAIRS => 1, // crafting
            //		-BlockTypeIds::POLISHED_BLACKSTONE_WALL => 1, // crafting
            //		-BlockTypeIds::CHISELED_POLISHED_BLACKSTONE => 1, // crafting
            //		-BlockTypeIds::POLISHED_BLACKSTONE_BRICKS => 1, // crafting
            //		-BlockTypeIds::POLISHED_BLACKSTONE_BRICK_SLAB => 1, // crafting
            //		-BlockTypeIds::POLISHED_BLACKSTONE_BRICK_STAIRS => 1, // crafting
            //		-BlockTypeIds::POLISHED_BLACKSTONE_BRICK_WALL => 1, // crafting
            //		-BlockTypeIds::CRACKED_POLISHED_BLACKSTONE_BRICKS => 1, // crafting
            //		-BlockTypeIds::RAW_COPPER => 5, // mining
            //		-BlockTypeIds::RAW_GOLD => 5, // mining
            //		-BlockTypeIds::RAW_IRON => 5, // mining
            //		-BlockTypeIds::CALCITE => 1, // crafting
            //		-BlockTypeIds::DEEPSLATE => 5, // mining
            //		-BlockTypeIds::DEEPSLATE_BRICKS => 1, // crafting
            //		-BlockTypeIds::DEEPSLATE_BRICK_SLAB => 1, // crafting
            //		-BlockTypeIds::DEEPSLATE_BRICK_STAIRS => 1, // crafting
            //		-BlockTypeIds::DEEPSLATE_BRICK_WALL => 1, // crafting
            //		-BlockTypeIds::CRACKED_DEEPSLATE_BRICKS => 1, // crafting
            //		-BlockTypeIds::DEEPSLATE_TILES => 1, // crafting
            //		-BlockTypeIds::DEEPSLATE_TILE_SLAB => 1, // crafting
            //		-BlockTypeIds::DEEPSLATE_TILE_STAIRS => 1, // crafting
            //		-BlockTypeIds::DEEPSLATE_TILE_WALL => 1, // crafting
            //		-BlockTypeIds::CRACKED_DEEPSLATE_TILES => 1, // crafting
            //		-BlockTypeIds::COBBLED_DEEPSLATE => 1, // crafting
            //		-BlockTypeIds::COBBLED_DEEPSLATE_SLAB => 1, // crafting
            //		-BlockTypeIds::COBBLED_DEEPSLATE_STAIRS => 1, // crafting
            //		-BlockTypeIds::COBBLED_DEEPSLATE_WALL => 1, // crafting
            //		-BlockTypeIds::POLISHED_DEEPSLATE => 1, // crafting
            //		-BlockTypeIds::POLISHED_DEEPSLATE_SLAB => 1, // crafting
            //		-BlockTypeIds::POLISHED_DEEPSLATE_STAIRS => 1, // crafting
            //		-BlockTypeIds::POLISHED_DEEPSLATE_WALL => 1, // crafting
            //		-BlockTypeIds::QUARTZ_BRICKS => 1, // crafting
            //		-BlockTypeIds::CHISELED_DEEPSLATE => 1, // crafting
            //		-BlockTypeIds::CHISELED_NETHER_BRICKS => 1, // crafting
            //		-BlockTypeIds::CRACKED_NETHER_BRICKS => 1, // crafting
            //		-BlockTypeIds::TUFF => 1, // crafting
            //		-BlockTypeIds::SOUL_TORCH => 1, // crafting
            //		-BlockTypeIds::SOUL_LANTERN => 1, // crafting
            //		-BlockTypeIds::SOUL_SOIL => 1, // crafting
            //		-BlockTypeIds::SOUL_FIRE => 1, // crafting
            //		-BlockTypeIds::SHROOMLIGHT => 1, // crafting
            //		-BlockTypeIds::MANGROVE_PLANKS => 1, // crafting
            //		-BlockTypeIds::CRIMSON_PLANKS => 1, // crafting
            //		-BlockTypeIds::WARPED_PLANKS => 1, // crafting
            //		-BlockTypeIds::MANGROVE_FENCE => 1, // crafting
            //		-BlockTypeIds::CRIMSON_FENCE => 1, // crafting
            //		-BlockTypeIds::WARPED_FENCE => 1, // crafting
            //		-BlockTypeIds::MANGROVE_SLAB => 1, // crafting
            //		-BlockTypeIds::CRIMSON_SLAB => 1, // crafting
            //		-BlockTypeIds::WARPED_SLAB => 1, // crafting
            //		-BlockTypeIds::MANGROVE_LOG => 1, // crafting
            //		-BlockTypeIds::CRIMSON_STEM => 1, // crafting
            //		-BlockTypeIds::WARPED_STEM => 1, // crafting
            //		-BlockTypeIds::MANGROVE_WOOD => 1, // crafting
            //		-BlockTypeIds::CRIMSON_HYPHAE => 1, // crafting
            //		-BlockTypeIds::WARPED_HYPHAE => 1, // crafting
            //		-BlockTypeIds::MANGROVE_TRAPDOOR => 1, // crafting
            //		-BlockTypeIds::CRIMSON_TRAPDOOR => 1, // crafting
            //		-BlockTypeIds::WARPED_TRAPDOOR => 1, // crafting
            //		-BlockTypeIds::MANGROVE_BUTTON => 1, // crafting
            //		-BlockTypeIds::CRIMSON_BUTTON => 1, // crafting
            //		-BlockTypeIds::WARPED_BUTTON => 1, // crafting
            //		-BlockTypeIds::MANGROVE_PRESSURE_PLATE => 1, // crafting
            //		-BlockTypeIds::CRIMSON_PRESSURE_PLATE => 1, // crafting
            //		-BlockTypeIds::WARPED_PRESSURE_PLATE => 1, // crafting
            //		-BlockTypeIds::MANGROVE_DOOR => 1, // crafting
            //		-BlockTypeIds::CRIMSON_DOOR => 1, // crafting
            //		-BlockTypeIds::WARPED_DOOR => 1, // crafting
            //		-BlockTypeIds::MANGROVE_FENCE_GATE => 1, // crafting
            //		-BlockTypeIds::CRIMSON_FENCE_GATE => 1, // crafting
            //		-BlockTypeIds::WARPED_FENCE_GATE => 1, // crafting
            //		-BlockTypeIds::MANGROVE_STAIRS => 1, // crafting
            //		-BlockTypeIds::CRIMSON_STAIRS => 1, // crafting
            //		-BlockTypeIds::WARPED_STAIRS => 1, // crafting
            //		-BlockTypeIds::MANGROVE_SIGN => 1, // crafting
            //		-BlockTypeIds::CRIMSON_SIGN => 1, // crafting
            //		-BlockTypeIds::WARPED_SIGN => 1, // crafting
            //		-BlockTypeIds::MANGROVE_WALL_SIGN => 1, // crafting
            //		-BlockTypeIds::CRIMSON_WALL_SIGN => 1, // crafting
            //		-BlockTypeIds::WARPED_WALL_SIGN => 1, // crafting
            //		-BlockTypeIds::TINTED_GLASS => 1, // crafting
            //		-BlockTypeIds::HONEYCOMB => 1, // crafting
            //		-BlockTypeIds::DEEPSLATE_COAL_ORE => 5, // mining
            //		-BlockTypeIds::DEEPSLATE_DIAMOND_ORE => 5, // mining
            //		-BlockTypeIds::DEEPSLATE_EMERALD_ORE => 5, // mining
            //		-BlockTypeIds::DEEPSLATE_LAPIS_LAZULI_ORE => 5, // mining
            //		-BlockTypeIds::DEEPSLATE_REDSTONE_ORE => 5, // mining
            //		-BlockTypeIds::DEEPSLATE_IRON_ORE => 5, // mining
            //		-BlockTypeIds::DEEPSLATE_GOLD_ORE => 5, // mining
            //		-BlockTypeIds::DEEPSLATE_COPPER_ORE => 5, // mining
            //		-BlockTypeIds::COPPER_ORE => 5, // mining
            //		-BlockTypeIds::NETHER_GOLD_ORE => 5, // mining
            //		-BlockTypeIds::MUD => 1, // crafting
            //		-BlockTypeIds::MUD_BRICKS => 1, // crafting
            //		-BlockTypeIds::MUD_BRICK_SLAB => 1, // crafting
            //		-BlockTypeIds::MUD_BRICK_STAIRS => 1, // crafting
            //		-BlockTypeIds::MUD_BRICK_WALL => 1, // crafting
            //		-BlockTypeIds::PACKED_MUD => 1, // crafting
            //		-BlockTypeIds::WARPED_WART_BLOCK => 1, // crafting
            //		-BlockTypeIds::CRYING_OBSIDIAN => 1, // crafting
            //		-BlockTypeIds::GILDED_BLACKSTONE => 1, // crafting
            //		-BlockTypeIds::LIGHTNING_ROD => 1, // crafting
            //		-BlockTypeIds::COPPER => 5, // mining
            //		-BlockTypeIds::CUT_COPPER => 1, // crafting
            //		-BlockTypeIds::CUT_COPPER_SLAB => 1, // crafting
            //		-BlockTypeIds::CUT_COPPER_STAIRS => 1, // crafting
            //		-BlockTypeIds::CANDLE => 1, // crafting
            //		-BlockTypeIds::DYED_CANDLE => 1, // crafting
            //		-BlockTypeIds::CAKE_WITH_CANDLE => 1, // crafting
            //		-BlockTypeIds::CAKE_WITH_DYED_CANDLE => 1, // crafting
            //		-BlockTypeIds::WITHER_ROSE => 1, // crafting
            //		-BlockTypeIds::HANGING_ROOTS => 1, // crafting
            //		-BlockTypeIds::CARTOGRAPHY_TABLE => 1, // crafting
            //		-BlockTypeIds::SMITHING_TABLE => 1, // crafting
            //		-BlockTypeIds::NETHERITE => 5, // mining
            //		-BlockTypeIds::SPORE_BLOSSOM => 1, // crafting
            //		-BlockTypeIds::CAULDRON => 1, // crafting
            //		-BlockTypeIds::WATER_CAULDRON => 1, // crafting
            //		-BlockTypeIds::LAVA_CAULDRON => 1, // crafting
            //		-BlockTypeIds::POTION_CAULDRON => 1, // crafting
            //		-BlockTypeIds::POWDER_SNOW_CAULDRON => 1, // crafting
            //		-BlockTypeIds::CHORUS_FLOWER => 5, // farming
            //		-BlockTypeIds::CHORUS_PLANT => 5, // farming
            //		-BlockTypeIds::MANGROVE_ROOTS => 1, // crafting
            //		-BlockTypeIds::MUDDY_MANGROVE_ROOTS => 1, // crafting
            //		-BlockTypeIds::FROGLIGHT => 1, // crafting
            //		-BlockTypeIds::TWISTING_VINES => 1, // crafting
            //		-BlockTypeIds::WEEPING_VINES => 1, // crafting
            //		-BlockTypeIds::CHAIN => 1, // crafting
            //		-BlockTypeIds::SCULK => 1, // crafting
            //		-BlockTypeIds::GLOWING_ITEM_FRAME => 1, // crafting
            //		-BlockTypeIds::MANGROVE_LEAVES => 1, // crafting
            //		-BlockTypeIds::AZALEA_LEAVES => 1, // crafting
            //		-BlockTypeIds::FLOWERING_AZALEA_LEAVES => 1, // crafting
            //		-BlockTypeIds::REINFORCED_DEEPSLATE => 1, // crafting
            //		-BlockTypeIds::CAVE_VINES => 1, // crafting
            //		-BlockTypeIds::GLOW_LICHEN => 1, // crafting
            //		-BlockTypeIds::CHERRY_BUTTON => 1, // crafting
            //		-BlockTypeIds::CHERRY_DOOR => 1, // crafting
            //		-BlockTypeIds::CHERRY_FENCE => 1, // crafting
            //		-BlockTypeIds::CHERRY_FENCE_GATE => 1, // crafting
            //		-BlockTypeIds::CHERRY_LEAVES => 1, // crafting
            //		-BlockTypeIds::CHERRY_LOG => 1, // crafting
            //		-BlockTypeIds::CHERRY_PLANKS => 1, // crafting
            //		-BlockTypeIds::CHERRY_PRESSURE_PLATE => 1, // crafting
            //		-BlockTypeIds::CHERRY_SAPLING => 1, // crafting
            //		-BlockTypeIds::CHERRY_SIGN => 1, // crafting
            //		-BlockTypeIds::CHERRY_SLAB => 1, // crafting
            //		-BlockTypeIds::CHERRY_STAIRS => 1, // crafting
            //		-BlockTypeIds::CHERRY_TRAPDOOR => 1, // crafting
            //		-BlockTypeIds::CHERRY_WALL_SIGN => 1, // crafting
            //		-BlockTypeIds::CHERRY_WOOD => 1, // crafting
            //		-BlockTypeIds::SMALL_DRIPLEAF => 1, // crafting
            //		-BlockTypeIds::BIG_DRIPLEAF_HEAD => 1, // crafting
            //		-BlockTypeIds::BIG_DRIPLEAF_STEM => 1, // crafting
            //		-BlockTypeIds::PINK_PETALS => 1, // crafting
            //		-BlockTypeIds::CRIMSON_ROOTS => 1, // crafting
            //		-BlockTypeIds::WARPED_ROOTS => 1, // crafting
            //		-BlockTypeIds::CHISELED_BOOKSHELF => 1, // crafting
            //		-BlockTypeIds::TORCHFLOWER => 1, // crafting
            //		-BlockTypeIds::TORCHFLOWER_CROP => 1, // crafting
            //		-BlockTypeIds::PITCHER_PLANT => 1, // crafting
            //		-BlockTypeIds::PITCHER_CROP => 1, // crafting
            //		-BlockTypeIds::DOUBLE_PITCHER_CROP => 1, // crafting
        ];

    public static array $illegalBlocks
        = [
            BlockTypeIds::BEDROCK,
            BlockTypeIds::INVISIBLE_BEDROCK,
            BlockTypeIds::BARRIER
        ];

    public static array $customBlockBlocks
        = [
            BlockTypeIds::DRAGON_EGG
        ];

    public static array $mineableBlocks = [];

    public static array $blockManaValues
        = [
            BlockTypeIds::STONE                       => 1,
            BlockTypeIds::COAL_ORE                    => 2,
            BlockTypeIds::COPPER_ORE                  => 2,
            CustomItemLoader::ZINC_ORE                => 3,
            BlockTypeIds::IRON_ORE                    => 3,
            BlockTypeIds::GOLD_ORE                    => 6,
            BlockTypeIds::REDSTONE_ORE                => 6,
            BlockTypeIds::LAPIS_LAZULI_ORE            => 8,
            BlockTypeIds::DIAMOND_ORE                 => 4,
            BlockTypeIds::EMERALD_ORE                 => 5,
            CustomItemLoader::TOPAZ_ORE               => 6,
            CustomItemLoader::STONE_QUARTZ_ORE        => 10,
            BlockTypeIds::DEEPSLATE                   => 2,
            BlockTypeIds::DEEPSLATE_COAL_ORE          => 3,
            BlockTypeIds::DEEPSLATE_COPPER_ORE        => 3,
            CustomItemLoader::DEEPSLATE_ZINC_ORE      => 4,
            BlockTypeIds::DEEPSLATE_IRON_ORE          => 4,
            BlockTypeIds::DEEPSLATE_GOLD_ORE          => 8,
            BlockTypeIds::DEEPSLATE_REDSTONE_ORE      => 8,
            BlockTypeIds::DEEPSLATE_LAPIS_LAZULI_ORE  => 10,
            BlockTypeIds::DEEPSLATE_DIAMOND_ORE       => 5,
            BlockTypeIds::DEEPSLATE_EMERALD_ORE       => 6,
            CustomItemLoader::DEEPSLATE_TOPAZ_ORE     => 7,
            CustomItemLoader::DEEPSLATE_QUARTZ_ORE    => 12,
            BlockTypeIds::NETHERRACK                  => 3,
            CustomItemLoader::NETHERRACK_COAL_ORE     => 4,
            CustomItemLoader::NETHERRACK_COPPER_ORE   => 4,
            CustomItemLoader::NETHERRACK_LAPIS_ORE    => 5,
            CustomItemLoader::NETHERRACK_EMERALD_ORE  => 5,
            CustomItemLoader::NETHERRACK_DIAMOND_ORE  => 9,
            CustomItemLoader::NETHERRACK_GOLD_ORE     => 9,
            CustomItemLoader::NETHERRACK_IRON_ORE     => 11,
            CustomItemLoader::NETHERRACK_REDSTONE_ORE => 6,
            CustomItemLoader::NETHERRACK_TOPAZ_ORE    => 7,
            CustomItemLoader::NETHERRACK_ZINC_ORE     => 8,
            BlockTypeIds::NETHER_QUARTZ_ORE           => 14,
            BlockTypeIds::END_STONE                   => 5,
            CustomItemLoader::END_STONE_COAL_ORE      => 6,
            CustomItemLoader::END_STONE_COPPER_ORE    => 6,
            CustomItemLoader::END_STONE_LAPIS_ORE     => 7,
            CustomItemLoader::END_STONE_EMERALD_ORE   => 7,
            CustomItemLoader::END_STONE_DIAMOND_ORE   => 10,
            CustomItemLoader::END_STONE_GOLD_ORE      => 10,
            CustomItemLoader::END_STONE_IRON_ORE      => 12,
            CustomItemLoader::END_STONE_QUARTZ_ORE    => 8,
            CustomItemLoader::END_STONE_REDSTONE_ORE  => 9,
            CustomItemLoader::END_STONE_TOPAZ_ORE     => 10,
            CustomItemLoader::END_STONE_ZINC_ORE      => 16,
            BlockTypeIds::BLACKSTONE                  => 7,
            CustomItemLoader::BLACKSTONE_COAL_ORE     => 9,
            CustomItemLoader::BLACKSTONE_COPPER_ORE   => 9,
            CustomItemLoader::BLACKSTONE_LAPIS_ORE    => 9,
            CustomItemLoader::BLACKSTONE_EMERALD_ORE  => 10,
            CustomItemLoader::BLACKSTONE_DIAMOND_ORE  => 11,
            CustomItemLoader::BLACKSTONE_GOLD_ORE     => 12,
            CustomItemLoader::BLACKSTONE_IRON_ORE     => 13,
            CustomItemLoader::BLACKSTONE_QUARTZ_ORE   => 13,
            CustomItemLoader::BLACKSTONE_REDSTONE_ORE => 10,
            CustomItemLoader::BLACKSTONE_TOPAZ_ORE    => 12,
            CustomItemLoader::BLACKSTONE_ZINC_ORE     => 18,
            BlockTypeIds::PRISMARINE                  => 8,
            CustomItemLoader::PRISMARINE_COAL_ORE     => 10,
            CustomItemLoader::PRISMARINE_COPPER_ORE   => 10,
            CustomItemLoader::PRISMARINE_DIAMOND_ORE  => 10,
            CustomItemLoader::PRISMARINE_GOLD_ORE     => 12,
            CustomItemLoader::PRISMARINE_LAPIS_ORE    => 12,
            CustomItemLoader::PRISMARINE_EMERALD_ORE  => 13,
            CustomItemLoader::PRISMARINE_IRON_ORE     => 14,
            CustomItemLoader::PRISMARINE_QUARTZ_ORE   => 15,
            CustomItemLoader::PRISMARINE_REDSTONE_ORE => 12,
            CustomItemLoader::PRISMARINE_TOPAZ_ORE    => 14,
            CustomItemLoader::PRISMARINE_ZINC_ORE     => 19,
            BlockTypeIds::AMETHYST                    => 9,
            CustomItemLoader::AMETHYST_COAL_ORE       => 12,
            CustomItemLoader::AMETHYST_COPPER_ORE     => 12,
            CustomItemLoader::AMETHYST_DIAMOND_ORE    => 12,
            CustomItemLoader::AMETHYST_GOLD_ORE       => 14,
            CustomItemLoader::AMETHYST_IRON_ORE       => 15,
            CustomItemLoader::AMETHYST_QUARTZ_ORE     => 16,
            CustomItemLoader::AMETHYST_REDSTONE_ORE   => 17,
            CustomItemLoader::AMETHYST_TOPAZ_ORE      => 18,
            CustomItemLoader::AMETHYST_ZINC_ORE       => 15,
            CustomItemLoader::AMETHYST_LAPIS_ORE      => 20,
        ];
    public static array $blockXpValues
        = [
            BlockTypeIds::STONE                       => 1,
            BlockTypeIds::COAL_ORE                    => 2,
            BlockTypeIds::COPPER_ORE                  => 2,
            CustomItemLoader::ZINC_ORE                => 3,
            BlockTypeIds::IRON_ORE                    => 3,
            BlockTypeIds::GOLD_ORE                    => 6,
            BlockTypeIds::REDSTONE_ORE                => 6,
            BlockTypeIds::LAPIS_LAZULI_ORE            => 8,
            BlockTypeIds::DIAMOND_ORE                 => 4,
            BlockTypeIds::EMERALD_ORE                 => 5,
            CustomItemLoader::TOPAZ_ORE               => 6,
            CustomItemLoader::STONE_QUARTZ_ORE        => 10,
            BlockTypeIds::DEEPSLATE                   => 2,
            BlockTypeIds::DEEPSLATE_COAL_ORE          => 3,
            BlockTypeIds::DEEPSLATE_COPPER_ORE        => 3,
            CustomItemLoader::DEEPSLATE_ZINC_ORE      => 4,
            BlockTypeIds::DEEPSLATE_IRON_ORE          => 4,
            BlockTypeIds::DEEPSLATE_GOLD_ORE          => 8,
            BlockTypeIds::DEEPSLATE_REDSTONE_ORE      => 8,
            BlockTypeIds::DEEPSLATE_LAPIS_LAZULI_ORE  => 10,
            BlockTypeIds::DEEPSLATE_DIAMOND_ORE       => 5,
            BlockTypeIds::DEEPSLATE_EMERALD_ORE       => 6,
            CustomItemLoader::DEEPSLATE_TOPAZ_ORE     => 7,
            CustomItemLoader::DEEPSLATE_QUARTZ_ORE    => 12,
            BlockTypeIds::NETHERRACK                  => 3,
            CustomItemLoader::NETHERRACK_COAL_ORE     => 4,
            CustomItemLoader::NETHERRACK_COPPER_ORE   => 4,
            CustomItemLoader::NETHERRACK_LAPIS_ORE    => 5,
            CustomItemLoader::NETHERRACK_EMERALD_ORE  => 5,
            CustomItemLoader::NETHERRACK_DIAMOND_ORE  => 9,
            CustomItemLoader::NETHERRACK_GOLD_ORE     => 9,
            CustomItemLoader::NETHERRACK_IRON_ORE     => 11,
            CustomItemLoader::NETHERRACK_REDSTONE_ORE => 6,
            CustomItemLoader::NETHERRACK_TOPAZ_ORE    => 7,
            CustomItemLoader::NETHERRACK_ZINC_ORE     => 8,
            BlockTypeIds::NETHER_QUARTZ_ORE           => 14,
            BlockTypeIds::END_STONE                   => 5,
            CustomItemLoader::END_STONE_COAL_ORE      => 6,
            CustomItemLoader::END_STONE_COPPER_ORE    => 6,
            CustomItemLoader::END_STONE_LAPIS_ORE     => 7,
            CustomItemLoader::END_STONE_EMERALD_ORE   => 7,
            CustomItemLoader::END_STONE_DIAMOND_ORE   => 10,
            CustomItemLoader::END_STONE_GOLD_ORE      => 10,
            CustomItemLoader::END_STONE_IRON_ORE      => 12,
            CustomItemLoader::END_STONE_QUARTZ_ORE    => 8,
            CustomItemLoader::END_STONE_REDSTONE_ORE  => 9,
            CustomItemLoader::END_STONE_TOPAZ_ORE     => 10,
            CustomItemLoader::END_STONE_ZINC_ORE      => 16,
            BlockTypeIds::BLACKSTONE                  => 7,
            CustomItemLoader::BLACKSTONE_COAL_ORE     => 9,
            CustomItemLoader::BLACKSTONE_COPPER_ORE   => 9,
            CustomItemLoader::BLACKSTONE_LAPIS_ORE    => 9,
            CustomItemLoader::BLACKSTONE_EMERALD_ORE  => 10,
            CustomItemLoader::BLACKSTONE_DIAMOND_ORE  => 11,
            CustomItemLoader::BLACKSTONE_GOLD_ORE     => 12,
            CustomItemLoader::BLACKSTONE_IRON_ORE     => 13,
            CustomItemLoader::BLACKSTONE_QUARTZ_ORE   => 13,
            CustomItemLoader::BLACKSTONE_REDSTONE_ORE => 10,
            CustomItemLoader::BLACKSTONE_TOPAZ_ORE    => 12,
            CustomItemLoader::BLACKSTONE_ZINC_ORE     => 18,
            BlockTypeIds::PRISMARINE                  => 8,
            CustomItemLoader::PRISMARINE_COAL_ORE     => 10,
            CustomItemLoader::PRISMARINE_COPPER_ORE   => 10,
            CustomItemLoader::PRISMARINE_DIAMOND_ORE  => 10,
            CustomItemLoader::PRISMARINE_GOLD_ORE     => 12,
            CustomItemLoader::PRISMARINE_LAPIS_ORE    => 12,
            CustomItemLoader::PRISMARINE_EMERALD_ORE  => 13,
            CustomItemLoader::PRISMARINE_IRON_ORE     => 14,
            CustomItemLoader::PRISMARINE_QUARTZ_ORE   => 15,
            CustomItemLoader::PRISMARINE_REDSTONE_ORE => 12,
            CustomItemLoader::PRISMARINE_TOPAZ_ORE    => 14,
            CustomItemLoader::PRISMARINE_ZINC_ORE     => 19,
            BlockTypeIds::AMETHYST                    => 9,
            CustomItemLoader::AMETHYST_COAL_ORE       => 12,
            CustomItemLoader::AMETHYST_COPPER_ORE     => 12,
            CustomItemLoader::AMETHYST_DIAMOND_ORE    => 12,
            CustomItemLoader::AMETHYST_GOLD_ORE       => 14,
            CustomItemLoader::AMETHYST_IRON_ORE       => 15,
            CustomItemLoader::AMETHYST_QUARTZ_ORE     => 16,
            CustomItemLoader::AMETHYST_REDSTONE_ORE   => 17,
            CustomItemLoader::AMETHYST_TOPAZ_ORE      => 18,
            CustomItemLoader::AMETHYST_ZINC_ORE       => 15,
            CustomItemLoader::AMETHYST_LAPIS_ORE      => 20,
        ];
    public static array $blockDropValues = [];
    public static array $blockIslandPointValues
        = [
            BlockTypeIds::STONE                       => 1,
            BlockTypeIds::COAL_ORE                    => 2,
            BlockTypeIds::COPPER_ORE                  => 2,
            CustomItemLoader::ZINC_ORE                => 3,
            BlockTypeIds::IRON_ORE                    => 3,
            BlockTypeIds::GOLD_ORE                    => 6,
            BlockTypeIds::REDSTONE_ORE                => 6,
            BlockTypeIds::LAPIS_LAZULI_ORE            => 8,
            BlockTypeIds::DIAMOND_ORE                 => 4,
            BlockTypeIds::EMERALD_ORE                 => 5,
            CustomItemLoader::TOPAZ_ORE               => 6,
            CustomItemLoader::STONE_QUARTZ_ORE        => 10,
            BlockTypeIds::DEEPSLATE                   => 2,
            BlockTypeIds::DEEPSLATE_COAL_ORE          => 3,
            BlockTypeIds::DEEPSLATE_COPPER_ORE        => 3,
            CustomItemLoader::DEEPSLATE_ZINC_ORE      => 4,
            BlockTypeIds::DEEPSLATE_IRON_ORE          => 4,
            BlockTypeIds::DEEPSLATE_GOLD_ORE          => 8,
            BlockTypeIds::DEEPSLATE_REDSTONE_ORE      => 8,
            BlockTypeIds::DEEPSLATE_LAPIS_LAZULI_ORE  => 10,
            BlockTypeIds::DEEPSLATE_DIAMOND_ORE       => 5,
            BlockTypeIds::DEEPSLATE_EMERALD_ORE       => 6,
            CustomItemLoader::DEEPSLATE_TOPAZ_ORE     => 7,
            CustomItemLoader::DEEPSLATE_QUARTZ_ORE    => 12,
            BlockTypeIds::NETHERRACK                  => 3,
            CustomItemLoader::NETHERRACK_COAL_ORE     => 4,
            CustomItemLoader::NETHERRACK_COPPER_ORE   => 4,
            CustomItemLoader::NETHERRACK_LAPIS_ORE    => 5,
            CustomItemLoader::NETHERRACK_EMERALD_ORE  => 5,
            CustomItemLoader::NETHERRACK_DIAMOND_ORE  => 9,
            CustomItemLoader::NETHERRACK_GOLD_ORE     => 9,
            CustomItemLoader::NETHERRACK_IRON_ORE     => 11,
            CustomItemLoader::NETHERRACK_REDSTONE_ORE => 6,
            CustomItemLoader::NETHERRACK_TOPAZ_ORE    => 7,
            CustomItemLoader::NETHERRACK_ZINC_ORE     => 8,
            BlockTypeIds::NETHER_QUARTZ_ORE           => 14,
            BlockTypeIds::END_STONE                   => 5,
            CustomItemLoader::END_STONE_COAL_ORE      => 6,
            CustomItemLoader::END_STONE_COPPER_ORE    => 6,
            CustomItemLoader::END_STONE_LAPIS_ORE     => 7,
            CustomItemLoader::END_STONE_EMERALD_ORE   => 7,
            CustomItemLoader::END_STONE_DIAMOND_ORE   => 10,
            CustomItemLoader::END_STONE_GOLD_ORE      => 10,
            CustomItemLoader::END_STONE_IRON_ORE      => 12,
            CustomItemLoader::END_STONE_QUARTZ_ORE    => 8,
            CustomItemLoader::END_STONE_REDSTONE_ORE  => 9,
            CustomItemLoader::END_STONE_TOPAZ_ORE     => 10,
            CustomItemLoader::END_STONE_ZINC_ORE      => 16,
            BlockTypeIds::BLACKSTONE                  => 7,
            CustomItemLoader::BLACKSTONE_COAL_ORE     => 9,
            CustomItemLoader::BLACKSTONE_COPPER_ORE   => 9,
            CustomItemLoader::BLACKSTONE_LAPIS_ORE    => 9,
            CustomItemLoader::BLACKSTONE_EMERALD_ORE  => 10,
            CustomItemLoader::BLACKSTONE_DIAMOND_ORE  => 11,
            CustomItemLoader::BLACKSTONE_GOLD_ORE     => 12,
            CustomItemLoader::BLACKSTONE_IRON_ORE     => 13,
            CustomItemLoader::BLACKSTONE_QUARTZ_ORE   => 13,
            CustomItemLoader::BLACKSTONE_REDSTONE_ORE => 10,
            CustomItemLoader::BLACKSTONE_TOPAZ_ORE    => 12,
            CustomItemLoader::BLACKSTONE_ZINC_ORE     => 18,
            BlockTypeIds::PRISMARINE                  => 8,
            CustomItemLoader::PRISMARINE_COAL_ORE     => 10,
            CustomItemLoader::PRISMARINE_COPPER_ORE   => 10,
            CustomItemLoader::PRISMARINE_DIAMOND_ORE  => 10,
            CustomItemLoader::PRISMARINE_GOLD_ORE     => 12,
            CustomItemLoader::PRISMARINE_LAPIS_ORE    => 12,
            CustomItemLoader::PRISMARINE_EMERALD_ORE  => 13,
            CustomItemLoader::PRISMARINE_IRON_ORE     => 14,
            CustomItemLoader::PRISMARINE_QUARTZ_ORE   => 15,
            CustomItemLoader::PRISMARINE_REDSTONE_ORE => 12,
            CustomItemLoader::PRISMARINE_TOPAZ_ORE    => 14,
            CustomItemLoader::PRISMARINE_ZINC_ORE     => 19,
            BlockTypeIds::AMETHYST                    => 9,
            CustomItemLoader::AMETHYST_COAL_ORE       => 12,
            CustomItemLoader::AMETHYST_COPPER_ORE     => 12,
            CustomItemLoader::AMETHYST_DIAMOND_ORE    => 12,
            CustomItemLoader::AMETHYST_GOLD_ORE       => 14,
            CustomItemLoader::AMETHYST_IRON_ORE       => 15,
            CustomItemLoader::AMETHYST_QUARTZ_ORE     => 16,
            CustomItemLoader::AMETHYST_REDSTONE_ORE   => 17,
            CustomItemLoader::AMETHYST_TOPAZ_ORE      => 18,
            CustomItemLoader::AMETHYST_ZINC_ORE       => 15,
            CustomItemLoader::AMETHYST_LAPIS_ORE      => 20,
        ];

    public function __construct() {
        self::$mineableBlocks = array_merge(self::$mineableBlocks, self::$stoneOres, self::$deepslateOres, self::$netherrackOres, self::$blackstoneOres, self::$prismarineOres, self::$endstoneOres, self::$amethystOres);
        self::$typeIdToNameMap = [
            BlockTypeIds::STONE                       => VanillaBlocks::STONE()->getName(),
            BlockTypeIds::COAL_ORE                    => VanillaBlocks::COAL_ORE()->getName(),
            BlockTypeIds::COPPER_ORE                  => VanillaBlocks::COPPER_ORE()->getName(),
            CustomItemLoader::ZINC_ORE                => CustomiesBlockFactory::getInstance()->get("fallentech:stone_quartz_ore")->getName(),
            BlockTypeIds::IRON_ORE                    => VanillaBlocks::IRON_ORE()->getName(),
            BlockTypeIds::GOLD_ORE                    => VanillaBlocks::GOLD_ORE()->getName(),
            BlockTypeIds::REDSTONE_ORE                => VanillaBlocks::REDSTONE_ORE()->getName(),
            BlockTypeIds::LAPIS_LAZULI_ORE            => VanillaBlocks::LAPIS_LAZULI_ORE()->getName(),
            BlockTypeIds::DIAMOND_ORE                 => VanillaBlocks::DIAMOND_ORE()->getName(),
            BlockTypeIds::EMERALD_ORE                 => VanillaBlocks::EMERALD_ORE()->getName(),
            CustomItemLoader::TOPAZ_ORE               => CustomiesBlockFactory::getInstance()->get("fallentech:topaz_ore")->getName(),
            CustomItemLoader::STONE_QUARTZ_ORE        => CustomiesBlockFactory::getInstance()->get("fallentech:zinc_ore")->getName(),
            BlockTypeIds::DEEPSLATE                   => VanillaBlocks::DEEPSLATE()->getName(),
            BlockTypeIds::DEEPSLATE_COAL_ORE          => VanillaBlocks::DEEPSLATE_COAL_ORE()->getName(),
            BlockTypeIds::DEEPSLATE_COPPER_ORE        => VanillaBlocks::DEEPSLATE_COPPER_ORE()->getName(),
            CustomItemLoader::DEEPSLATE_ZINC_ORE      => CustomiesBlockFactory::getInstance()->get("fallentech:deepslate_quartz_ore")->getName(),
            BlockTypeIds::DEEPSLATE_IRON_ORE          => VanillaBlocks::DEEPSLATE_IRON_ORE()->getName(),
            BlockTypeIds::DEEPSLATE_GOLD_ORE          => VanillaBlocks::DEEPSLATE_GOLD_ORE()->getName(),
            BlockTypeIds::DEEPSLATE_REDSTONE_ORE      => VanillaBlocks::DEEPSLATE_REDSTONE_ORE()->getName(),
            BlockTypeIds::DEEPSLATE_LAPIS_LAZULI_ORE  => VanillaBlocks::DEEPSLATE_LAPIS_LAZULI_ORE()->getName(),
            BlockTypeIds::DEEPSLATE_DIAMOND_ORE       => VanillaBlocks::DEEPSLATE_DIAMOND_ORE()->getName(),
            BlockTypeIds::DEEPSLATE_EMERALD_ORE       => VanillaBlocks::DEEPSLATE_EMERALD_ORE()->getName(),
            CustomItemLoader::DEEPSLATE_TOPAZ_ORE     => CustomiesBlockFactory::getInstance()->get("fallentech:deepslate_topaz_ore")->getName(),
            CustomItemLoader::DEEPSLATE_QUARTZ_ORE    => CustomiesBlockFactory::getInstance()->get("fallentech:deepslate_zinc_ore")->getName(),
            BlockTypeIds::NETHERRACK                  => VanillaBlocks::NETHERRACK()->getName(),
            CustomItemLoader::NETHERRACK_COAL_ORE     => CustomiesBlockFactory::getInstance()->get("fallentech:netherrack_coal_ore")->getName(),
            CustomItemLoader::NETHERRACK_COPPER_ORE   => CustomiesBlockFactory::getInstance()->get("fallentech:netherrack_copper_ore")->getName(),
            CustomItemLoader::NETHERRACK_LAPIS_ORE    => CustomiesBlockFactory::getInstance()->get("fallentech:netherrack_zinc_ore")->getName(),
            CustomItemLoader::NETHERRACK_EMERALD_ORE  => CustomiesBlockFactory::getInstance()->get("fallentech:netherrack_iron_ore")->getName(),
            CustomItemLoader::NETHERRACK_DIAMOND_ORE  => CustomiesBlockFactory::getInstance()->get("fallentech:netherrack_gold_ore")->getName(),
            CustomItemLoader::NETHERRACK_GOLD_ORE     => CustomiesBlockFactory::getInstance()->get("fallentech:netherrack_redstone_ore")->getName(),
            CustomItemLoader::NETHERRACK_IRON_ORE     => CustomiesBlockFactory::getInstance()->get("fallentech:netherrack_lapis_ore")->getName(),
            CustomItemLoader::NETHERRACK_REDSTONE_ORE => CustomiesBlockFactory::getInstance()->get("fallentech:netherrack_diamond_ore")->getName(),
            CustomItemLoader::NETHERRACK_TOPAZ_ORE    => CustomiesBlockFactory::getInstance()->get("fallentech:netherrack_emerald_ore")->getName(),
            CustomItemLoader::NETHERRACK_ZINC_ORE     => CustomiesBlockFactory::getInstance()->get("fallentech:netherrack_topaz_ore")->getName(),
            BlockTypeIds::NETHER_QUARTZ_ORE           => VanillaBlocks::NETHER_QUARTZ_ORE()->getName(),
            BlockTypeIds::END_STONE                   => VanillaBlocks::END_STONE()->getName(),
            CustomItemLoader::END_STONE_COAL_ORE      => CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_coal_ore")->getName(),
            CustomItemLoader::END_STONE_COPPER_ORE    => CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_copper_ore")->getName(),
            CustomItemLoader::END_STONE_LAPIS_ORE     => CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_lapis_ore")->getName(),
            CustomItemLoader::END_STONE_EMERALD_ORE   => CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_emerald_ore")->getName(),
            CustomItemLoader::END_STONE_DIAMOND_ORE   => CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_diamond_ore")->getName(),
            CustomItemLoader::END_STONE_GOLD_ORE      => CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_gold_ore")->getName(),
            CustomItemLoader::END_STONE_IRON_ORE      => CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_iron_ore")->getName(),
            CustomItemLoader::END_STONE_QUARTZ_ORE    => CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_quartz_ore")->getName(),
            CustomItemLoader::END_STONE_REDSTONE_ORE  => CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_redstone_ore")->getName(),
            CustomItemLoader::END_STONE_TOPAZ_ORE     => CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_topaz_ore")->getName(),
            CustomItemLoader::END_STONE_ZINC_ORE      => CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_zinc_ore")->getName(),
            BlockTypeIds::BLACKSTONE                  => VanillaBlocks::BLACKSTONE()->getName(),
            CustomItemLoader::BLACKSTONE_COAL_ORE     => CustomiesBlockFactory::getInstance()->get("fallentech:blackstone_coal_ore")->getName(),
            CustomItemLoader::BLACKSTONE_COPPER_ORE   => CustomiesBlockFactory::getInstance()->get("fallentech:blackstone_copper_ore")->getName(),
            CustomItemLoader::BLACKSTONE_LAPIS_ORE    => CustomiesBlockFactory::getInstance()->get("fallentech:blackstone_lapis_ore")->getName(),
            CustomItemLoader::BLACKSTONE_EMERALD_ORE  => CustomiesBlockFactory::getInstance()->get("fallentech:blackstone_emerald_ore")->getName(),
            CustomItemLoader::BLACKSTONE_DIAMOND_ORE  => CustomiesBlockFactory::getInstance()->get("fallentech:blackstone_diamond_ore")->getName(),
            CustomItemLoader::BLACKSTONE_GOLD_ORE     => CustomiesBlockFactory::getInstance()->get("fallentech:blackstone_gold_ore")->getName(),
            CustomItemLoader::BLACKSTONE_IRON_ORE     => CustomiesBlockFactory::getInstance()->get("fallentech:blackstone_iron_ore")->getName(),
            CustomItemLoader::BLACKSTONE_QUARTZ_ORE   => CustomiesBlockFactory::getInstance()->get("fallentech:blackstone_quartz_ore")->getName(),
            CustomItemLoader::BLACKSTONE_REDSTONE_ORE => CustomiesBlockFactory::getInstance()->get("fallentech:blackstone_redstone_ore")->getName(),
            CustomItemLoader::BLACKSTONE_TOPAZ_ORE    => CustomiesBlockFactory::getInstance()->get("fallentech:blackstone_topaz_ore")->getName(),
            CustomItemLoader::BLACKSTONE_ZINC_ORE     => CustomiesBlockFactory::getInstance()->get("fallentech:blackstone_zinc_ore")->getName(),
            BlockTypeIds::PRISMARINE                  => VanillaBlocks::PRISMARINE()->getName(),
            CustomItemLoader::PRISMARINE_COAL_ORE     => CustomiesBlockFactory::getInstance()->get("fallentech:prismarine_coal_ore")->getName(),
            CustomItemLoader::PRISMARINE_COPPER_ORE   => CustomiesBlockFactory::getInstance()->get("fallentech:prismarine_copper_ore")->getName(),
            CustomItemLoader::PRISMARINE_DIAMOND_ORE  => CustomiesBlockFactory::getInstance()->get("fallentech:prismarine_diamond_ore")->getName(),
            CustomItemLoader::PRISMARINE_GOLD_ORE     => CustomiesBlockFactory::getInstance()->get("fallentech:prismarine_gold_ore")->getName(),
            CustomItemLoader::PRISMARINE_LAPIS_ORE    => CustomiesBlockFactory::getInstance()->get("fallentech:prismarine_lapis_ore")->getName(),
            CustomItemLoader::PRISMARINE_EMERALD_ORE  => CustomiesBlockFactory::getInstance()->get("fallentech:prismarine_emerald_ore")->getName(),
            CustomItemLoader::PRISMARINE_IRON_ORE     => CustomiesBlockFactory::getInstance()->get("fallentech:prismarine_iron_ore")->getName(),
            CustomItemLoader::PRISMARINE_QUARTZ_ORE   => CustomiesBlockFactory::getInstance()->get("fallentech:prismarine_quartz_ore")->getName(),
            CustomItemLoader::PRISMARINE_REDSTONE_ORE => CustomiesBlockFactory::getInstance()->get("fallentech:prismarine_redstone_ore")->getName(),
            CustomItemLoader::PRISMARINE_TOPAZ_ORE    => CustomiesBlockFactory::getInstance()->get("fallentech:prismarine_topaz_ore")->getName(),
            CustomItemLoader::PRISMARINE_ZINC_ORE     => CustomiesBlockFactory::getInstance()->get("fallentech:prismarine_zinc_ore")->getName(),
            BlockTypeIds::AMETHYST                    => VanillaBlocks::AMETHYST()->getName(),
            CustomItemLoader::AMETHYST_COAL_ORE       => CustomiesBlockFactory::getInstance()->get("fallentech:amethyst_coal_ore")->getName(),
            CustomItemLoader::AMETHYST_COPPER_ORE     => CustomiesBlockFactory::getInstance()->get("fallentech:amethyst_copper_ore")->getName(),
            CustomItemLoader::AMETHYST_DIAMOND_ORE    => CustomiesBlockFactory::getInstance()->get("fallentech:amethyst_diamond_ore")->getName(),
            CustomItemLoader::AMETHYST_GOLD_ORE       => CustomiesBlockFactory::getInstance()->get("fallentech:amethyst_gold_ore")->getName(),
            CustomItemLoader::AMETHYST_IRON_ORE       => CustomiesBlockFactory::getInstance()->get("fallentech:amethyst_iron_ore")->getName(),
            CustomItemLoader::AMETHYST_QUARTZ_ORE     => CustomiesBlockFactory::getInstance()->get("fallentech:amethyst_quartz_ore")->getName(),
            CustomItemLoader::AMETHYST_REDSTONE_ORE   => CustomiesBlockFactory::getInstance()->get("fallentech:amethyst_redstone_ore")->getName(),
            CustomItemLoader::AMETHYST_TOPAZ_ORE      => CustomiesBlockFactory::getInstance()->get("fallentech:amethyst_topaz_ore")->getName(),
            CustomItemLoader::AMETHYST_ZINC_ORE       => CustomiesBlockFactory::getInstance()->get("fallentech:amethyst_zinc_ore")->getName(),
            CustomItemLoader::AMETHYST_LAPIS_ORE      => CustomiesBlockFactory::getInstance()->get("fallentech:amethyst_lapis_ore")->getName(),
        ];
    }

    public static array $stoneOres
        = [
            BlockTypeIds::STONE,
            BlockTypeIds::COAL_ORE,
            BlockTypeIds::COPPER_ORE,
            CustomItemLoader::ZINC_ORE,
            BlockTypeIds::IRON_ORE,
            BlockTypeIds::GOLD_ORE,
            BlockTypeIds::REDSTONE_ORE,
            BlockTypeIds::LAPIS_LAZULI_ORE,
            BlockTypeIds::DIAMOND_ORE,
            BlockTypeIds::EMERALD_ORE,
            CustomItemLoader::TOPAZ_ORE,
            CustomItemLoader::STONE_QUARTZ_ORE,
        ];

    public static array $deepslateOres
        = [
            BlockTypeIds::DEEPSLATE,
            BlockTypeIds::DEEPSLATE_COAL_ORE,
            BlockTypeIds::DEEPSLATE_COPPER_ORE,
            CustomItemLoader::DEEPSLATE_ZINC_ORE,
            BlockTypeIds::DEEPSLATE_IRON_ORE,
            BlockTypeIds::DEEPSLATE_GOLD_ORE,
            BlockTypeIds::DEEPSLATE_REDSTONE_ORE,
            BlockTypeIds::DEEPSLATE_LAPIS_LAZULI_ORE,
            BlockTypeIds::DEEPSLATE_DIAMOND_ORE,
            BlockTypeIds::DEEPSLATE_EMERALD_ORE,
            CustomItemLoader::DEEPSLATE_TOPAZ_ORE,
            CustomItemLoader::DEEPSLATE_QUARTZ_ORE,
        ];

    public static array $netherrackOres
        = [
            BlockTypeIds::NETHERRACK,
            CustomItemLoader::NETHERRACK_COAL_ORE,
            CustomItemLoader::NETHERRACK_COPPER_ORE,
            CustomItemLoader::NETHERRACK_ZINC_ORE,
            CustomItemLoader::NETHERRACK_IRON_ORE,
            CustomItemLoader::NETHERRACK_GOLD_ORE,
            CustomItemLoader::NETHERRACK_REDSTONE_ORE,
            CustomItemLoader::NETHERRACK_LAPIS_ORE,
            CustomItemLoader::NETHERRACK_DIAMOND_ORE,
            CustomItemLoader::NETHERRACK_EMERALD_ORE,
            CustomItemLoader::NETHERRACK_TOPAZ_ORE,
            BlockTypeIds::NETHER_QUARTZ_ORE
        ];

    public static array $endstoneOres
        = [
            BlockTypeIds::END_STONE,
            CustomItemLoader::END_STONE_COAL_ORE,
            CustomItemLoader::END_STONE_COPPER_ORE,
            CustomItemLoader::END_STONE_ZINC_ORE,
            CustomItemLoader::END_STONE_IRON_ORE,
            CustomItemLoader::END_STONE_GOLD_ORE,
            CustomItemLoader::END_STONE_REDSTONE_ORE,
            CustomItemLoader::END_STONE_LAPIS_ORE,
            CustomItemLoader::END_STONE_DIAMOND_ORE,
            CustomItemLoader::END_STONE_EMERALD_ORE,
            CustomItemLoader::END_STONE_TOPAZ_ORE,
            CustomItemLoader::END_STONE_QUARTZ_ORE,
        ];

    public static array $blackstoneOres
        = [
            BlockTypeIds::BLACKSTONE,
            CustomItemLoader::BLACKSTONE_COAL_ORE,
            CustomItemLoader::BLACKSTONE_COPPER_ORE,
            CustomItemLoader::BLACKSTONE_ZINC_ORE,
            CustomItemLoader::BLACKSTONE_IRON_ORE,
            CustomItemLoader::BLACKSTONE_GOLD_ORE,
            CustomItemLoader::BLACKSTONE_REDSTONE_ORE,
            CustomItemLoader::BLACKSTONE_LAPIS_ORE,
            CustomItemLoader::BLACKSTONE_DIAMOND_ORE,
            CustomItemLoader::BLACKSTONE_EMERALD_ORE,
            CustomItemLoader::BLACKSTONE_TOPAZ_ORE,
            CustomItemLoader::BLACKSTONE_QUARTZ_ORE,
        ];

    public static array $prismarineOres
        = [
            BlockTypeIds::PRISMARINE,
            CustomItemLoader::PRISMARINE_COAL_ORE,
            CustomItemLoader::PRISMARINE_COPPER_ORE,
            CustomItemLoader::PRISMARINE_ZINC_ORE,
            CustomItemLoader::PRISMARINE_IRON_ORE,
            CustomItemLoader::PRISMARINE_GOLD_ORE,
            CustomItemLoader::PRISMARINE_REDSTONE_ORE,
            CustomItemLoader::PRISMARINE_LAPIS_ORE,
            CustomItemLoader::PRISMARINE_DIAMOND_ORE,
            CustomItemLoader::PRISMARINE_EMERALD_ORE,
            CustomItemLoader::PRISMARINE_TOPAZ_ORE,
            CustomItemLoader::PRISMARINE_QUARTZ_ORE,
        ];

    public static array $amethystOres
        = [
            BlockTypeIds::AMETHYST,
            CustomItemLoader::AMETHYST_COAL_ORE,
            CustomItemLoader::AMETHYST_COPPER_ORE,
            CustomItemLoader::AMETHYST_ZINC_ORE,
            CustomItemLoader::AMETHYST_IRON_ORE,
            CustomItemLoader::AMETHYST_GOLD_ORE,
            CustomItemLoader::AMETHYST_REDSTONE_ORE,
            CustomItemLoader::AMETHYST_LAPIS_ORE,
            CustomItemLoader::AMETHYST_DIAMOND_ORE,
            //		todo emerald
            CustomItemLoader::AMETHYST_TOPAZ_ORE,
            CustomItemLoader::AMETHYST_QUARTZ_ORE,
        ];

    public static array $typeIdToNameMap = [];

}