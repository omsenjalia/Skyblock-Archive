package dev.skyblock.shop;

import dev.skyblock.SkyblockCore;
import dev.skyblock.user.User;
import dev.skyblock.util.SellPrices;
import net.kyori.adventure.text.Component;
import org.bukkit.Bukkit;
import org.bukkit.Material;
import org.bukkit.entity.Player;
import org.bukkit.inventory.Inventory;
import org.bukkit.inventory.ItemStack;
import org.bukkit.inventory.meta.ItemMeta;

import java.util.ArrayList;
import java.util.List;

public class ShopManager {
    private final SkyblockCore plugin;

    public ShopManager(SkyblockCore plugin) {
        this.plugin = plugin;
    }

    public void openMainMenu(Player player) {
        User user = plugin.getUserManager().getOnlineUser(player.getUniqueId());
        double money = (user != null) ? user.getMoney() : 0;

        ShopHolder holder = new ShopHolder(ShopHolder.ShopMenu.MAIN);
        Inventory inv = Bukkit.createInventory(holder, 54, Component.text("§b§lShop §7- §6" + String.format("%.1f", money) + "$"));
        holder.setInventory(inv);

        fillBorder(inv);

        // Categories
        addCategoryItem(inv, 20, Material.OAK_LOG, "§aBlocks");
        addCategoryItem(inv, 21, Material.IRON_INGOT, "§fResources");
        addCategoryItem(inv, 22, Material.GOLDEN_APPLE, "§eFood");
        addCategoryItem(inv, 23, Material.WHEAT_SEEDS, "§2Farm");
        addCategoryItem(inv, 24, Material.CRAFTING_TABLE, "§dFurniture");
        addCategoryItem(inv, 29, Material.DIAMOND_CHESTPLATE, "§bEquipments");
        addCategoryItem(inv, 30, Material.GLASS_BOTTLE, "§9Brewing");
        addCategoryItem(inv, 31, Material.WHITE_DYE, "§fDyes");
        addCategoryItem(inv, 32, Material.ARROW, "§7Arrow");
        addCategoryItem(inv, 33, Material.BOOK, "§6CE Books");

        player.openInventory(inv);
    }

    public void openBlocksMenu(Player player) {
        ShopHolder holder = new ShopHolder(ShopHolder.ShopMenu.BLOCKS);
        Inventory inv = Bukkit.createInventory(holder, 54, Component.text("§aBlocks"));
        holder.setInventory(inv);
        fillBorder(inv);
        addBackButton(inv);

        Material[] items = {
            Material.OAK_LOG, Material.BIRCH_LOG, Material.SPRUCE_LOG, Material.JUNGLE_LOG, Material.ACACIA_LOG, Material.DARK_OAK_LOG,
            Material.STONE, Material.DIRT, Material.SAND, Material.OBSIDIAN,
            Material.CHEST, Material.FURNACE, Material.ENDER_CHEST,
            Material.SOUL_SAND, Material.GLOWSTONE, Material.BONE_BLOCK, Material.SEA_LANTERN, Material.TNT
        };

        int[] slots = {10, 11, 12, 13, 14, 15, 19, 20, 21, 22, 24, 25, 26, 28, 29, 30, 31, 32};
        for (int i = 0; i < items.length; i++) {
            addShopItem(inv, slots[i], items[i], 64);
        }
        player.openInventory(inv);
    }

    public void openResourcesMenu(Player player) {
        ShopHolder holder = new ShopHolder(ShopHolder.ShopMenu.RESOURCES);
        Inventory inv = Bukkit.createInventory(holder, 54, Component.text("§fResources"));
        holder.setInventory(inv);
        fillBorder(inv);
        addBackButton(inv);

        Material[] items = {
            Material.COAL, Material.RAW_IRON, Material.RAW_GOLD, Material.DIAMOND, Material.EMERALD, Material.LAPIS_LAZULI,
            Material.IRON_INGOT, Material.GOLD_INGOT, Material.LEATHER, Material.PRISMARINE_SHARD, Material.PRISMARINE_CRYSTALS, Material.GLOW_INK_SAC
        };
        int[] slots = {10, 11, 12, 13, 14, 15, 19, 20, 21, 22, 23, 24};
        for (int i = 0; i < items.length; i++) {
            addShopItem(inv, slots[i], items[i], 64);
        }
        player.openInventory(inv);
    }

    public void openFoodMenu(Player player) {
        ShopHolder holder = new ShopHolder(ShopHolder.ShopMenu.FOOD);
        Inventory inv = Bukkit.createInventory(holder, 54, Component.text("§eFood"));
        holder.setInventory(inv);
        fillBorder(inv);
        addBackButton(inv);

        addShopItem(inv, 21, Material.GOLDEN_APPLE, 1);
        addShopItem(inv, 23, Material.ENCHANTED_GOLDEN_APPLE, 1);
        player.openInventory(inv);
    }

    public void openFarmMenu(Player player) {
        ShopHolder holder = new ShopHolder(ShopHolder.ShopMenu.FARM);
        Inventory inv = Bukkit.createInventory(holder, 54, Component.text("§2Farm"));
        holder.setInventory(inv);
        fillBorder(inv);
        addBackButton(inv);

        Material[] items = {
            Material.BEETROOT_SEEDS, Material.OAK_SAPLING, Material.BONE_MEAL, Material.POTATO, Material.CACTUS, Material.CARROT,
            Material.MELON_SEEDS, Material.PUMPKIN_SEEDS, Material.WHEAT_SEEDS, Material.SUGAR_CANE, Material.EGG, Material.NETHER_WART
        };
        int[] slots = {10, 11, 12, 13, 14, 15, 19, 20, 21, 22, 23, 24};
        for (int i = 0; i < items.length; i++) {
            if (items[i] == Material.OAK_SAPLING) {
                ItemStack item = new ItemStack(Material.OAK_SAPLING);
                ItemMeta meta = item.getItemMeta();
                meta.displayName(Component.text("§eOak Sapling"));
                List<Component> lore = new ArrayList<>();
                lore.add(Component.text("§7All 6 wood types"));
                lore.add(Component.text("§6Price: §f" + SellPrices.getBuyPrice(Material.OAK_SAPLING) + "$ §7per item"));
                lore.add(Component.text("§7Click to browse"));
                meta.lore(lore);
                item.setItemMeta(meta);
                inv.setItem(11, item);
            } else {
                addShopItem(inv, slots[i], items[i], 64);
            }
        }
        player.openInventory(inv);
    }

    public void openFarmSaplingsMenu(Player player) {
        ShopHolder holder = new ShopHolder(ShopHolder.ShopMenu.FARM_SAPLINGS);
        Inventory inv = Bukkit.createInventory(holder, 54, Component.text("§2Saplings"));
        holder.setInventory(inv);
        fillBorder(inv);
        addBackButton(inv);

        Material[] items = {
            Material.OAK_SAPLING, Material.BIRCH_SAPLING, Material.SPRUCE_SAPLING, Material.JUNGLE_SAPLING, Material.ACACIA_SAPLING, Material.DARK_OAK_SAPLING
        };
        int[] slots = {10, 11, 12, 13, 14, 15};
        for (int i = 0; i < items.length; i++) {
            addShopItem(inv, slots[i], items[i], 64);
        }
        player.openInventory(inv);
    }

    public void openDyesMenu(Player player) {
        ShopHolder holder = new ShopHolder(ShopHolder.ShopMenu.DYES);
        Inventory inv = Bukkit.createInventory(holder, 54, Component.text("§fDyes"));
        holder.setInventory(inv);
        fillBorder(inv);
        addBackButton(inv);

        Material[] items = {
            Material.WHITE_DYE, Material.ORANGE_DYE, Material.MAGENTA_DYE, Material.LIGHT_BLUE_DYE, Material.YELLOW_DYE, Material.LIME_DYE, Material.PINK_DYE, Material.GRAY_DYE,
            Material.LIGHT_GRAY_DYE, Material.CYAN_DYE, Material.PURPLE_DYE, Material.BLUE_DYE, Material.BROWN_DYE, Material.GREEN_DYE, Material.RED_DYE, Material.BLACK_DYE
        };
        int slot = 10;
        for (Material material : items) {
            addShopItem(inv, slot++, material, 16);
            if (slot == 17) slot = 19;
        }
        player.openInventory(inv);
    }

    public void openBrewingMenu(Player player) {
        ShopHolder holder = new ShopHolder(ShopHolder.ShopMenu.BREWING);
        Inventory inv = Bukkit.createInventory(holder, 54, Component.text("§9Brewing"));
        holder.setInventory(inv);
        fillBorder(inv);
        addBackButton(inv);

        Material[] items = {
            Material.BLAZE_POWDER, Material.MAGMA_CREAM, Material.GHAST_TEAR, Material.SPIDER_EYE, Material.GLISTERING_MELON_SLICE, Material.FERMENTED_SPIDER_EYE, Material.RABBIT_FOOT
        };
        int[] slots = {10, 11, 12, 13, 14, 15, 16};
        for (int i = 0; i < items.length; i++) {
            addShopItem(inv, slots[i], items[i], 64);
        }
        player.openInventory(inv);
    }

    public void openFurnitureMenu(Player player) {
        ShopHolder holder = new ShopHolder(ShopHolder.ShopMenu.FURNITURE);
        Inventory inv = Bukkit.createInventory(holder, 54, Component.text("§dFurniture"));
        holder.setInventory(inv);
        fillBorder(inv);
        addBackButton(inv);

        Material[] items = {
            Material.CRAFTING_TABLE, Material.BOOKSHELF, Material.JUKEBOX, Material.FLOWER_POT, Material.ITEM_FRAME, Material.PAINTING,
            Material.LADDER, Material.TORCH, Material.LANTERN, Material.SOUL_TORCH, Material.SOUL_LANTERN, Material.CANDLE
        };
        int[] slots = {10, 11, 12, 13, 14, 15, 19, 20, 21, 22, 23, 24};
        for (int i = 0; i < items.length; i++) {
            addShopItem(inv, slots[i], items[i], 1);
        }
        player.openInventory(inv);
    }

    public void openEquipmentsMenu(Player player) {
        ShopHolder holder = new ShopHolder(ShopHolder.ShopMenu.EQUIPMENTS);
        Inventory inv = Bukkit.createInventory(holder, 54, Component.text("§bEquipments"));
        holder.setInventory(inv);
        fillBorder(inv);
        addBackButton(inv);

        Material[] items = {
            Material.IRON_SWORD, Material.DIAMOND_SWORD, Material.BOW, Material.CROSSBOW, Material.IRON_PICKAXE, Material.DIAMOND_PICKAXE,
            Material.IRON_SHOVEL, Material.IRON_AXE, Material.FISHING_ROD, Material.FLINT_AND_STEEL, Material.SHEARS, Material.BUCKET,
            Material.WATER_BUCKET, Material.LAVA_BUCKET, Material.SADDLE
        };
        int slot = 10;
        for (Material material : items) {
            addShopItem(inv, slot++, material, 1);
            if (slot == 17) slot = 19;
            if (slot == 26) slot = 28;
        }
        player.openInventory(inv);
    }

    public void handleClick(Player player, ShopHolder.ShopMenu menu, Material material) {
        if (menu == ShopHolder.ShopMenu.MAIN) {
            switch (material) {
                case OAK_LOG -> openBlocksMenu(player);
                case IRON_INGOT -> openResourcesMenu(player);
                case GOLDEN_APPLE -> openFoodMenu(player);
                case WHEAT_SEEDS -> openFarmMenu(player);
                case CRAFTING_TABLE -> openFurnitureMenu(player);
                case DIAMOND_CHESTPLATE -> openEquipmentsMenu(player);
                case GLASS_BOTTLE -> openBrewingMenu(player);
                case WHITE_DYE -> openDyesMenu(player);
                case ARROW -> processPurchase(player, Material.ARROW, 64);
                case BOOK -> player.sendMessage("§cComing soon");
            }
        } else if (menu == ShopHolder.ShopMenu.FARM && material == Material.OAK_SAPLING) {
            openFarmSaplingsMenu(player);
        } else {
            int quantity = switch (menu) {
                case BLOCKS, RESOURCES, BREWING, FARM_SAPLINGS -> 64;
                case FARM -> (material == Material.OAK_SAPLING) ? 64 : 64; // already handled sapling navigation above
                case DYES -> 16;
                case FOOD, FURNITURE, EQUIPMENTS -> 1;
                default -> 64;
            };
            if (menu == ShopHolder.ShopMenu.FARM && material == Material.OAK_SAPLING) return; // double safety
            processPurchase(player, material, quantity);
        }
    }

    public void handleBack(Player player, ShopHolder.ShopMenu currentMenu) {
        if (currentMenu == ShopHolder.ShopMenu.FARM_SAPLINGS) {
            openFarmMenu(player);
        } else {
            openMainMenu(player);
        }
    }

    public boolean processPurchase(Player player, Material material, int quantity) {
        if (!SellPrices.isBuyable(material)) return false;

        User user = plugin.getUserManager().getOnlineUser(player.getUniqueId());
        if (user == null) return false;

        double cost = SellPrices.getBuyPrice(material) * quantity;
        if (user.getMoney() < cost) {
            player.sendMessage("§cYou need §6" + String.format("%.1f", cost) + "$ §cto buy this!");
            return false;
        }

        user.removeMoney(cost);
        ItemStack item = new ItemStack(material, quantity);
        player.getInventory().addItem(item);
        player.sendActionBar(Component.text(
                "§aBought §f" + quantity + "x " + material.name().toLowerCase().replace("_", " ") + " §afor §6" + String.format("%.1f", cost) + "$"
        ));
        return true;
    }

    private void fillBorder(Inventory inv) {
        ItemStack pane = new ItemStack(Material.GRAY_STAINED_GLASS_PANE);
        ItemMeta meta = pane.getItemMeta();
        meta.displayName(Component.text("§7"));
        pane.setItemMeta(meta);

        for (int i = 0; i < 54; i++) {
            if (i < 9 || i >= 45 || i % 9 == 0 || i % 9 == 8) {
                inv.setItem(i, pane);
            }
        }
    }

    private void addBackButton(Inventory inv) {
        ItemStack back = new ItemStack(Material.RED_STAINED_GLASS_PANE);
        ItemMeta meta = back.getItemMeta();
        meta.displayName(Component.text("§cBack"));
        back.setItemMeta(meta);
        inv.setItem(45, back);
    }

    private void addCategoryItem(Inventory inv, int slot, Material material, String name) {
        ItemStack item = new ItemStack(material);
        ItemMeta meta = item.getItemMeta();
        meta.displayName(Component.text(name));
        List<Component> lore = new ArrayList<>();
        lore.add(Component.text("§7Click to browse"));
        meta.lore(lore);
        item.setItemMeta(meta);
        inv.setItem(slot, item);
    }

    private void addShopItem(Inventory inv, int slot, Material material, int quantity) {
        ItemStack item = new ItemStack(material);
        ItemMeta meta = item.getItemMeta();
        // Use generic name or custom name if needed, here we just use Material name nicely formatted
        meta.displayName(Component.text("§e" + capitalize(material.name())));
        List<Component> lore = new ArrayList<>();
        lore.add(Component.text("§6Price: §f" + SellPrices.getBuyPrice(material) + "$ §7per item"));
        lore.add(Component.text("§7Click to buy §f" + quantity + "x"));
        meta.lore(lore);
        item.setItemMeta(meta);
        inv.setItem(slot, item);
    }

    private String capitalize(String name) {
        String[] parts = name.toLowerCase().split("_");
        StringBuilder sb = new StringBuilder();
        for (String part : parts) {
            sb.append(Character.toUpperCase(part.charAt(0))).append(part.substring(1)).append(" ");
        }
        return sb.toString().trim();
    }
}
