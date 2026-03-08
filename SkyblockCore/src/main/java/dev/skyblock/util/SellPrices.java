package dev.skyblock.util;

import org.bukkit.Material;
import java.util.HashMap;
import java.util.Map;

public class SellPrices {
    private static final Map<Material, Double> PRICES = new HashMap<>();
    private static final Map<Material, Double> BUY_PRICES = new HashMap<>();

    static {
        // Ores (raw)
        PRICES.put(Material.COAL, 8.0);
        PRICES.put(Material.RAW_COPPER, 10.0);
        PRICES.put(Material.RAW_IRON, 15.0);
        PRICES.put(Material.LAPIS_LAZULI, 12.0);
        PRICES.put(Material.RAW_GOLD, 25.0);
        PRICES.put(Material.DIAMOND, 80.0);
        PRICES.put(Material.EMERALD, 60.0);
        PRICES.put(Material.QUARTZ, 8.0);
        PRICES.put(Material.NETHERITE_SCRAP, 200.0);
        PRICES.put(Material.ANCIENT_DEBRIS, 150.0);
        // Ingots
        PRICES.put(Material.COPPER_INGOT, 18.0);
        PRICES.put(Material.IRON_INGOT, 25.0);
        PRICES.put(Material.GOLD_INGOT, 40.0);
        PRICES.put(Material.NETHERITE_INGOT, 500.0);
        // Farming
        PRICES.put(Material.WHEAT, 3.0);
        PRICES.put(Material.CARROT, 4.0);
        PRICES.put(Material.POTATO, 4.0);
        PRICES.put(Material.BEETROOT, 5.0);
        PRICES.put(Material.MELON_SLICE, 3.0);
        PRICES.put(Material.PUMPKIN, 6.0);
        PRICES.put(Material.SUGAR_CANE, 2.0);
        PRICES.put(Material.CACTUS, 2.0);
        PRICES.put(Material.COCOA_BEANS, 5.0);
        PRICES.put(Material.NETHER_WART, 6.0);
        // Mob drops
        PRICES.put(Material.ROTTEN_FLESH, 1.0);
        PRICES.put(Material.BONE, 3.0);
        PRICES.put(Material.SPIDER_EYE, 8.0);
        PRICES.put(Material.STRING, 5.0);
        PRICES.put(Material.GUNPOWDER, 10.0);
        PRICES.put(Material.BLAZE_ROD, 20.0);
        PRICES.put(Material.GHAST_TEAR, 30.0);
        PRICES.put(Material.ENDER_PEARL, 15.0);
        PRICES.put(Material.SLIME_BALL, 8.0);
        PRICES.put(Material.MAGMA_CREAM, 10.0);
        PRICES.put(Material.LEATHER, 6.0);
        PRICES.put(Material.FEATHER, 3.0);
        PRICES.put(Material.INK_SAC, 4.0);
        PRICES.put(Material.GLOW_INK_SAC, 10.0);

        // BUY PRICES
        // Resources
        BUY_PRICES.put(Material.COAL, 15.0);
        BUY_PRICES.put(Material.RAW_IRON, 25.0);
        BUY_PRICES.put(Material.RAW_GOLD, 45.0);
        BUY_PRICES.put(Material.DIAMOND, 150.0);
        BUY_PRICES.put(Material.EMERALD, 120.0);
        BUY_PRICES.put(Material.LAPIS_LAZULI, 20.0);
        BUY_PRICES.put(Material.QUARTZ, 15.0);
        BUY_PRICES.put(Material.LEATHER, 12.0);
        BUY_PRICES.put(Material.IRON_INGOT, 40.0);
        BUY_PRICES.put(Material.GOLD_INGOT, 70.0);
        BUY_PRICES.put(Material.PRISMARINE_SHARD, 10.0);
        BUY_PRICES.put(Material.PRISMARINE_CRYSTALS, 15.0);
        BUY_PRICES.put(Material.GLOW_INK_SAC, 20.0);
        // Blocks
        BUY_PRICES.put(Material.OAK_LOG, 5.0);
        BUY_PRICES.put(Material.BIRCH_LOG, 5.0);
        BUY_PRICES.put(Material.SPRUCE_LOG, 5.0);
        BUY_PRICES.put(Material.JUNGLE_LOG, 5.0);
        BUY_PRICES.put(Material.ACACIA_LOG, 5.0);
        BUY_PRICES.put(Material.DARK_OAK_LOG, 5.0);
        BUY_PRICES.put(Material.STONE, 3.0);
        BUY_PRICES.put(Material.DIRT, 1.0);
        BUY_PRICES.put(Material.SAND, 2.0);
        BUY_PRICES.put(Material.OBSIDIAN, 50.0);
        BUY_PRICES.put(Material.CHEST, 20.0);
        BUY_PRICES.put(Material.FURNACE, 15.0);
        BUY_PRICES.put(Material.SOUL_SAND, 8.0);
        BUY_PRICES.put(Material.GLOWSTONE, 10.0);
        BUY_PRICES.put(Material.BONE_BLOCK, 8.0);
        BUY_PRICES.put(Material.SEA_LANTERN, 25.0);
        BUY_PRICES.put(Material.TNT, 30.0);
        BUY_PRICES.put(Material.ENDER_CHEST, 200.0);
        // Food
        BUY_PRICES.put(Material.GOLDEN_APPLE, 100.0);
        BUY_PRICES.put(Material.ENCHANTED_GOLDEN_APPLE, 500.0);
        // Farm
        BUY_PRICES.put(Material.BEETROOT_SEEDS, 3.0);
        BUY_PRICES.put(Material.OAK_SAPLING, 4.0);
        BUY_PRICES.put(Material.BONE_MEAL, 2.0);
        BUY_PRICES.put(Material.POTATO, 5.0);
        BUY_PRICES.put(Material.CACTUS, 3.0);
        BUY_PRICES.put(Material.CARROT, 5.0);
        BUY_PRICES.put(Material.MELON_SEEDS, 3.0);
        BUY_PRICES.put(Material.PUMPKIN_SEEDS, 3.0);
        BUY_PRICES.put(Material.WHEAT_SEEDS, 2.0);
        BUY_PRICES.put(Material.SUGAR_CANE, 3.0);
        BUY_PRICES.put(Material.EGG, 2.0);
        BUY_PRICES.put(Material.NETHER_WART, 8.0);
        // Brewing
        BUY_PRICES.put(Material.BLAZE_POWDER, 25.0);
        BUY_PRICES.put(Material.MAGMA_CREAM, 15.0);
        BUY_PRICES.put(Material.GHAST_TEAR, 50.0);
        BUY_PRICES.put(Material.SPIDER_EYE, 12.0);
        BUY_PRICES.put(Material.GLISTERING_MELON_SLICE, 20.0);
        BUY_PRICES.put(Material.FERMENTED_SPIDER_EYE, 25.0);
        BUY_PRICES.put(Material.RABBIT_FOOT, 30.0);
        // Arrows
        BUY_PRICES.put(Material.ARROW, 1.0);
        // Dyes
        BUY_PRICES.put(Material.WHITE_DYE, 3.0);
        BUY_PRICES.put(Material.RED_DYE, 3.0);
        BUY_PRICES.put(Material.BLUE_DYE, 3.0);
        BUY_PRICES.put(Material.YELLOW_DYE, 3.0);
        BUY_PRICES.put(Material.GREEN_DYE, 3.0);
        BUY_PRICES.put(Material.BLACK_DYE, 3.0);
        BUY_PRICES.put(Material.PURPLE_DYE, 3.0);
        BUY_PRICES.put(Material.CYAN_DYE, 3.0);
        BUY_PRICES.put(Material.ORANGE_DYE, 3.0);
        BUY_PRICES.put(Material.PINK_DYE, 3.0);
        BUY_PRICES.put(Material.LIME_DYE, 3.0);
        BUY_PRICES.put(Material.MAGENTA_DYE, 3.0);
        BUY_PRICES.put(Material.LIGHT_BLUE_DYE, 3.0);
        BUY_PRICES.put(Material.BROWN_DYE, 3.0);
        BUY_PRICES.put(Material.GRAY_DYE, 3.0);
        BUY_PRICES.put(Material.LIGHT_GRAY_DYE, 3.0);
        // Equipments
        BUY_PRICES.put(Material.IRON_SWORD, 80.0);
        BUY_PRICES.put(Material.DIAMOND_SWORD, 300.0);
        BUY_PRICES.put(Material.BOW, 60.0);
        BUY_PRICES.put(Material.CROSSBOW, 120.0);
        BUY_PRICES.put(Material.IRON_PICKAXE, 100.0);
        BUY_PRICES.put(Material.DIAMOND_PICKAXE, 400.0);
        BUY_PRICES.put(Material.IRON_SHOVEL, 60.0);
        BUY_PRICES.put(Material.IRON_AXE, 80.0);
        BUY_PRICES.put(Material.FISHING_ROD, 40.0);
        BUY_PRICES.put(Material.FLINT_AND_STEEL, 50.0);
        BUY_PRICES.put(Material.SHEARS, 40.0);
        BUY_PRICES.put(Material.BUCKET, 30.0);
        BUY_PRICES.put(Material.WATER_BUCKET, 35.0);
        BUY_PRICES.put(Material.LAVA_BUCKET, 50.0);
        BUY_PRICES.put(Material.SADDLE, 150.0);

        // Furniture (from resources/shop.yml or legacy source)
        BUY_PRICES.put(Material.BOOKSHELF, 2000.0);
        BUY_PRICES.put(Material.JUKEBOX, 10000.0);
        BUY_PRICES.put(Material.FLOWER_POT, 100.0);
        BUY_PRICES.put(Material.ITEM_FRAME, 800.0);
        BUY_PRICES.put(Material.TORCH, 100.0);
        BUY_PRICES.put(Material.LANTERN, 100.0);
        BUY_PRICES.put(Material.SOUL_TORCH, 100.0);
        BUY_PRICES.put(Material.SOUL_LANTERN, 100.0);
        BUY_PRICES.put(Material.CANDLE, 100.0);
        // Missing in source, set to 0.0 per instructions
        BUY_PRICES.put(Material.CRAFTING_TABLE, 0.0);
        BUY_PRICES.put(Material.PAINTING, 0.0);
        BUY_PRICES.put(Material.LADDER, 0.0);
    }

    public static double getPrice(Material material) {
        return PRICES.getOrDefault(material, 0.0);
    }

    public static double getBuyPrice(Material material) {
        return BUY_PRICES.getOrDefault(material, 0.0);
    }

    public static boolean isSellable(Material material) {
        return PRICES.containsKey(material);
    }

    public static boolean isBuyable(Material material) {
        return BUY_PRICES.containsKey(material);
    }

    public static Map<Material, Double> getAllPrices() {
        return PRICES;
    }
}
