package dev.skyblock.util;

import org.bukkit.Material;
import java.util.HashMap;
import java.util.Map;

public class SellPrices {
    private static final Map<Material, Double> PRICES = new HashMap<>();

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
    }

    public static double getPrice(Material material) {
        return PRICES.getOrDefault(material, 0.0);
    }

    public static boolean isSellable(Material material) {
        return PRICES.containsKey(material);
    }

    public static Map<Material, Double> getAllPrices() {
        return PRICES;
    }
}
