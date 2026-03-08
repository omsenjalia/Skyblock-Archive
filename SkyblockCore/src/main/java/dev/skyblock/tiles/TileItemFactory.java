package dev.skyblock.tiles;

import dev.skyblock.SkyblockCore;
import net.kyori.adventure.text.Component;
import net.kyori.adventure.text.format.NamedTextColor;
import net.kyori.adventure.text.format.TextDecoration;
import org.bukkit.Material;
import org.bukkit.NamespacedKey;
import org.bukkit.inventory.ItemStack;
import org.bukkit.inventory.meta.ItemMeta;
import org.bukkit.persistence.PersistentDataType;

import java.util.ArrayList;
import java.util.List;

public class TileItemFactory {

    // PDC keys
    public static final String KEY_TILE_TYPE  = "tile_type";
    public static final String KEY_TILE_LEVEL = "tile_level";
    public static final String KEY_ORE_TYPE   = "ore_type";

    // Tile type values stored in PDC
    public static final String TYPE_OREGEN     = "OREGEN";
    public static final String TYPE_AUTOSELLER = "AUTOSELLER";
    public static final String TYPE_AUTOMINER  = "AUTOMINER";
    public static final String TYPE_CATALYST   = "CATALYST";
    public static final String TYPE_HOPPER     = "HOPPER";

    // Ore gen material → display name — matches PHP oregens registry
    public static final java.util.Map<Material, String> ORE_GEN_TYPES = java.util.Map.ofEntries(
        java.util.Map.entry(Material.COAL_ORE,              "Coal"),
        java.util.Map.entry(Material.IRON_ORE,              "Iron"),
        java.util.Map.entry(Material.LAPIS_ORE,             "Lapis"),
        java.util.Map.entry(Material.GOLD_ORE,              "Gold"),
        java.util.Map.entry(Material.DIAMOND_ORE,           "Diamond"),
        java.util.Map.entry(Material.EMERALD_ORE,           "Emerald"),
        java.util.Map.entry(Material.NETHER_QUARTZ_ORE,     "Quartz"),
        java.util.Map.entry(Material.ANCIENT_DEBRIS,        "Ancient Debris"),
        java.util.Map.entry(Material.DEEPSLATE_COAL_ORE,    "Deepslate Coal"),
        java.util.Map.entry(Material.DEEPSLATE_IRON_ORE,    "Deepslate Iron"),
        java.util.Map.entry(Material.DEEPSLATE_LAPIS_ORE,   "Deepslate Lapis"),
        java.util.Map.entry(Material.DEEPSLATE_GOLD_ORE,    "Deepslate Gold"),
        java.util.Map.entry(Material.DEEPSLATE_DIAMOND_ORE, "Deepslate Diamond"),
        java.util.Map.entry(Material.DEEPSLATE_EMERALD_ORE, "Deepslate Emerald")
    );

    // Ore type → visual block material (what gets placed in world / used as item icon)
    // Matches PHP oregens string mapping — player remembers: cyan=diamond, green=emerald, white=iron etc.
    public static final java.util.Map<Material, Material> ORE_TO_BLOCK = java.util.Map.ofEntries(
        java.util.Map.entry(Material.COAL_ORE,              Material.BLACK_GLAZED_TERRACOTTA),
        java.util.Map.entry(Material.IRON_ORE,              Material.WHITE_GLAZED_TERRACOTTA),
        java.util.Map.entry(Material.LAPIS_ORE,             Material.BLUE_GLAZED_TERRACOTTA),
        java.util.Map.entry(Material.GOLD_ORE,              Material.YELLOW_GLAZED_TERRACOTTA),
        java.util.Map.entry(Material.DIAMOND_ORE,           Material.CYAN_GLAZED_TERRACOTTA),
        java.util.Map.entry(Material.EMERALD_ORE,           Material.GREEN_GLAZED_TERRACOTTA),
        java.util.Map.entry(Material.NETHER_QUARTZ_ORE,     Material.RED_GLAZED_TERRACOTTA),
        java.util.Map.entry(Material.ANCIENT_DEBRIS,        Material.GRAY_GLAZED_TERRACOTTA),
        java.util.Map.entry(Material.DEEPSLATE_COAL_ORE,    Material.GRAY_GLAZED_TERRACOTTA),
        java.util.Map.entry(Material.DEEPSLATE_IRON_ORE,    Material.LIGHT_GRAY_GLAZED_TERRACOTTA),
        java.util.Map.entry(Material.DEEPSLATE_LAPIS_ORE,   Material.BLUE_GLAZED_TERRACOTTA),
        java.util.Map.entry(Material.DEEPSLATE_GOLD_ORE,    Material.YELLOW_GLAZED_TERRACOTTA),
        java.util.Map.entry(Material.DEEPSLATE_DIAMOND_ORE, Material.CYAN_GLAZED_TERRACOTTA),
        java.util.Map.entry(Material.DEEPSLATE_EMERALD_ORE, Material.GREEN_GLAZED_TERRACOTTA)
    );

    /**
     * Create an ore gen item with PDC metadata.
     * oreType = the Material that will be spawned above the block (e.g. Material.DIAMOND_ORE)
     * level = ore gen level (affects spawn speed in future)
     */
    public static ItemStack createOreGen(Material oreType, int level) {
        Material blockMat = ORE_TO_BLOCK.getOrDefault(oreType, Material.CYAN_GLAZED_TERRACOTTA);
        String oreName = ORE_GEN_TYPES.getOrDefault(oreType, oreType.name());
        ItemStack item = new ItemStack(blockMat);
        ItemMeta meta = item.getItemMeta();

        meta.displayName(Component.text(oreName + " OreGen " + level)
            .color(NamedTextColor.GOLD)
            .decoration(TextDecoration.ITALIC, false)
            .decoration(TextDecoration.BOLD, true));

        List<Component> lore = new ArrayList<>();
        lore.add(Component.text("Place this block anywhere").color(NamedTextColor.GRAY).decoration(TextDecoration.ITALIC, false));
        lore.add(Component.text("on ground with upper").color(NamedTextColor.GRAY).decoration(TextDecoration.ITALIC, false));
        lore.add(Component.text("block as Air!").color(NamedTextColor.GRAY).decoration(TextDecoration.ITALIC, false));
        meta.lore(lore);

        NamespacedKey typeKey  = new NamespacedKey(SkyblockCore.getInstance(), KEY_TILE_TYPE);
        NamespacedKey levelKey = new NamespacedKey(SkyblockCore.getInstance(), KEY_TILE_LEVEL);
        NamespacedKey oreKey   = new NamespacedKey(SkyblockCore.getInstance(), KEY_ORE_TYPE);
        meta.getPersistentDataContainer().set(typeKey,  PersistentDataType.STRING, TYPE_OREGEN);
        meta.getPersistentDataContainer().set(levelKey, PersistentDataType.INTEGER, level);
        meta.getPersistentDataContainer().set(oreKey,   PersistentDataType.STRING, oreType.name());

        item.setItemMeta(meta);
        return item;
    }

    public static ItemStack createAutoSeller(int level, int type) {
        ItemStack item = new ItemStack(Material.BARREL);
        ItemMeta meta = item.getItemMeta();
        String typeStr = type == 1 ? "XP" : "Money";
        meta.displayName(Component.text("AutoSeller " + level + " [" + typeStr + "]")
            .color(NamedTextColor.YELLOW).decoration(TextDecoration.ITALIC, false).decoration(TextDecoration.BOLD, true));
        List<Component> lore = new ArrayList<>();
        lore.add(Component.text("Place on a chest to").color(NamedTextColor.GRAY).decoration(TextDecoration.ITALIC, false));
        lore.add(Component.text("auto sell contents.").color(NamedTextColor.GRAY).decoration(TextDecoration.ITALIC, false));
        meta.lore(lore);
        NamespacedKey typeKey      = new NamespacedKey(SkyblockCore.getInstance(), KEY_TILE_TYPE);
        NamespacedKey levelKey     = new NamespacedKey(SkyblockCore.getInstance(), KEY_TILE_LEVEL);
        NamespacedKey sellerType   = new NamespacedKey(SkyblockCore.getInstance(), "seller_type");
        meta.getPersistentDataContainer().set(typeKey,    PersistentDataType.STRING,  TYPE_AUTOSELLER);
        meta.getPersistentDataContainer().set(levelKey,   PersistentDataType.INTEGER, level);
        meta.getPersistentDataContainer().set(sellerType, PersistentDataType.INTEGER, type);
        item.setItemMeta(meta);
        return item;
    }

    public static ItemStack createAutoMiner(int level, int fortune, int fortuneLevel) {
        ItemStack item = new ItemStack(Material.SLIME_BLOCK);
        ItemMeta meta = item.getItemMeta();
        String fortuneStr = fortune == 1 ? " with Fortune " + fortuneLevel : "";
        meta.displayName(Component.text("AutoMiner " + level + fortuneStr)
            .color(NamedTextColor.YELLOW).decoration(TextDecoration.ITALIC, false).decoration(TextDecoration.BOLD, true));
        List<Component> lore = new ArrayList<>();
        lore.add(Component.text("Place on an ore with").color(NamedTextColor.GRAY).decoration(TextDecoration.ITALIC, false));
        lore.add(Component.text("chest above this block.").color(NamedTextColor.GRAY).decoration(TextDecoration.ITALIC, false));
        meta.lore(lore);
        NamespacedKey typeKey        = new NamespacedKey(SkyblockCore.getInstance(), KEY_TILE_TYPE);
        NamespacedKey levelKey       = new NamespacedKey(SkyblockCore.getInstance(), KEY_TILE_LEVEL);
        NamespacedKey fortuneKey     = new NamespacedKey(SkyblockCore.getInstance(), "fortune");
        NamespacedKey fortuneLvlKey  = new NamespacedKey(SkyblockCore.getInstance(), "fortune_level");
        meta.getPersistentDataContainer().set(typeKey,       PersistentDataType.STRING,  TYPE_AUTOMINER);
        meta.getPersistentDataContainer().set(levelKey,      PersistentDataType.INTEGER, level);
        meta.getPersistentDataContainer().set(fortuneKey,    PersistentDataType.INTEGER, fortune);
        meta.getPersistentDataContainer().set(fortuneLvlKey, PersistentDataType.INTEGER, fortuneLevel);
        item.setItemMeta(meta);
        return item;
    }

    public static ItemStack createCatalyst() {
        ItemStack item = new ItemStack(Material.PURPLE_GLAZED_TERRACOTTA);
        ItemMeta meta = item.getItemMeta();
        meta.displayName(Component.text("Catalyst")
            .color(NamedTextColor.LIGHT_PURPLE).decoration(TextDecoration.ITALIC, false).decoration(TextDecoration.BOLD, true));
        List<Component> lore = new ArrayList<>();
        lore.add(Component.text("Place this block anywhere").color(NamedTextColor.GRAY).decoration(TextDecoration.ITALIC, false));
        lore.add(Component.text("with upper block as Air!").color(NamedTextColor.GRAY).decoration(TextDecoration.ITALIC, false));
        meta.lore(lore);
        NamespacedKey typeKey = new NamespacedKey(SkyblockCore.getInstance(), KEY_TILE_TYPE);
        meta.getPersistentDataContainer().set(typeKey, PersistentDataType.STRING, TYPE_CATALYST);
        item.setItemMeta(meta);
        return item;
    }
}
