package dev.skyblock.island;

import org.bukkit.Location;
import org.bukkit.Material;

public class SchematicPlacer {
    public static void placeStartingPlatform(Location origin) {
        for (int x = -2; x <= 2; x++) {
            for (int z = -2; z <= 2; z++) {
                origin.clone().add(x, 0, z).getBlock().setType(Material.COBBLESTONE);
            }
        }
        origin.clone().add(0, 1, 0).getBlock().setType(Material.GRASS_BLOCK);
    }
}
