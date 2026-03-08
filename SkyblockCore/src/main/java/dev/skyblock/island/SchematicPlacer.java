package dev.skyblock.island;

import org.bukkit.Location;
import org.bukkit.Material;
import org.bukkit.World;
import org.bukkit.inventory.ItemStack;

public class SchematicPlacer {
    public static void placeStartingPlatform(Location center) {
        World world = center.getWorld();
        int x = center.getBlockX();
        int y = 64;
        int z = center.getBlockZ();

        // 5x5 grass base
        for (int dx = -2; dx <= 2; dx++) {
            for (int dz = -2; dz <= 2; dz++) {
                world.getBlockAt(x + dx, y, z + dz).setType(Material.GRASS_BLOCK);
            }
        }

        // Dirt layer below
        for (int dx = -2; dx <= 2; dx++) {
            for (int dz = -2; dz <= 2; dz++) {
                world.getBlockAt(x + dx, y - 1, z + dz).setType(Material.DIRT);
                world.getBlockAt(x + dx, y - 2, z + dz).setType(Material.DIRT);
            }
        }

        // Stone bottom
        for (int dx = -2; dx <= 2; dx++) {
            for (int dz = -2; dz <= 2; dz++) {
                world.getBlockAt(x + dx, y - 3, z + dz).setType(Material.STONE);
            }
        }

        // Single tree
        world.getBlockAt(x, y + 1, z).setType(Material.OAK_LOG);
        world.getBlockAt(x, y + 2, z).setType(Material.OAK_LOG);
        world.getBlockAt(x, y + 3, z).setType(Material.OAK_LOG);
        for (int dx = -2; dx <= 2; dx++) {
            for (int dz = -2; dz <= 2; dz++) {
                if (dx == 0 && dz == 0) continue;
                world.getBlockAt(x + dx, y + 3, z + dz).setType(Material.OAK_LEAVES);
                world.getBlockAt(x + dx, y + 4, z + dz).setType(Material.OAK_LEAVES);
            }
        }
        world.getBlockAt(x, y + 4, z).setType(Material.OAK_LEAVES);
        world.getBlockAt(x, y + 5, z).setType(Material.OAK_LEAVES);

        // Chest with starter items
        world.getBlockAt(x + 2, y + 1, z).setType(Material.CHEST);
        org.bukkit.block.Chest chest = (org.bukkit.block.Chest) world.getBlockAt(x + 2, y + 1, z).getState();
        org.bukkit.inventory.Inventory inv = chest.getInventory();
        inv.setItem(0, new ItemStack(Material.OAK_SAPLING, 4));
        inv.setItem(1, new ItemStack(Material.BREAD, 16));
        inv.setItem(2, new ItemStack(Material.BONE_MEAL, 16));
        inv.setItem(3, new ItemStack(Material.LAVA_BUCKET, 1));
        inv.setItem(4, new ItemStack(Material.WATER_BUCKET, 1));
        inv.setItem(5, new ItemStack(Material.COBBLESTONE, 32));
        inv.setItem(6, new ItemStack(Material.WOODEN_PICKAXE, 1));
        inv.setItem(7, new ItemStack(Material.WOODEN_AXE, 1));
        chest.update();
    }
}
