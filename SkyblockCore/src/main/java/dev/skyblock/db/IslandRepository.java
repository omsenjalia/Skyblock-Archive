package dev.skyblock.db;

import dev.skyblock.SkyblockCore;
import dev.skyblock.island.Island;

import java.util.UUID;

public class IslandRepository {
    private final DatabaseManager databaseManager;

    public IslandRepository(SkyblockCore plugin) {
        this.databaseManager = plugin.getDatabaseManager();
    }

    public void saveIsland(Island island) {
        databaseManager.executeAsync(
            "INSERT OR REPLACE INTO island (name, world) VALUES (?, ?)",
            island.getName(), island.getId()
        );
        // Save more island fields here
    }
}
