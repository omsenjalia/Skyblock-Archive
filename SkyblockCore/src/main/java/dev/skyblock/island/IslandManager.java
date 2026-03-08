package dev.skyblock.island;

import dev.skyblock.SkyblockCore;
import dev.skyblock.db.IslandRepository;
import org.bukkit.Bukkit;
import org.bukkit.World;
import org.bukkit.WorldCreator;
import org.bukkit.entity.Player;
import org.bukkit.generator.ChunkGenerator;
import org.bukkit.generator.WorldInfo;

import java.io.File;
import java.util.*;
import java.util.concurrent.ConcurrentHashMap;

public class IslandManager {
    private final SkyblockCore plugin;
    private final IslandRepository repository;
    private final Map<String, Island> islandsByWorld = new ConcurrentHashMap<>();
    private final Map<String, String> islandNameToWorld = new ConcurrentHashMap<>();

    public IslandManager(SkyblockCore plugin) {
        this.plugin = plugin;
        this.repository = new IslandRepository(plugin);
    }

    public Island createIsland(Player owner, String name) {
        String worldName = "island_" + UUID.randomUUID().toString();
        Island island = new Island(worldName, name, owner.getName());

        islandsByWorld.put(worldName, island);
        islandNameToWorld.put(name.toLowerCase(), worldName);

        // Generate the world
        WorldCreator creator = new WorldCreator(worldName);
        creator.generator(new VoidGenerator());
        World world = creator.createWorld();

        // Place starting platform
        SchematicPlacer.placeStartingPlatform(world.getSpawnLocation());

        // Save to DB
        repository.saveIsland(island);

        return island;
    }

    public Optional<Island> getIslandByName(String name) {
        String worldName = islandNameToWorld.get(name.toLowerCase());
        if (worldName == null) return Optional.empty();
        return Optional.ofNullable(islandsByWorld.get(worldName));
    }

    public Optional<Island> getOnlineIslandByWorld(String worldName) {
        return Optional.ofNullable(islandsByWorld.get(worldName));
    }

    public void loadAllIslands() {
        plugin.getDatabaseManager().queryAsync("SELECT name, world FROM island", rs -> {
            try {
                List<String[]> rows = new ArrayList<>();
                while (rs.next()) {
                    rows.add(new String[]{rs.getString("name"), rs.getString("world")});
                }
                Bukkit.getScheduler().runTask(plugin, () -> {
                    for (String[] row : rows) {
                        String islandName = row[0];
                        String worldName = row[1];
                        // Load the world if it exists on disk
                        World world = Bukkit.getWorld(worldName);
                        if (world == null) {
                            File worldFolder = new File(Bukkit.getWorldContainer(), worldName);
                            if (worldFolder.exists()) {
                                WorldCreator creator = new WorldCreator(worldName);
                                creator.generator(new VoidGenerator());
                                world = creator.createWorld();
                            }
                        }
                        // Create a basic Island object and cache it
                        // Full data loading from other tables can be added later
                        Island island = new Island(worldName, islandName, "");
                        islandsByWorld.put(worldName, island);
                        islandNameToWorld.put(islandName.toLowerCase(), worldName);
                    }
                    plugin.getLogger().info("Loaded " + islandsByWorld.size() + " islands.");
                });
            } catch (Exception e) {
                e.printStackTrace();
            }
        });
    }

    public void shutdown() {
        for (Island island : islandsByWorld.values()) {
            repository.saveIsland(island);
        }
    }

    private static class VoidGenerator extends ChunkGenerator {
        @Override
        public void generateNoise(WorldInfo worldInfo, Random random, int chunkX, int chunkZ, ChunkData chunkData) {
            // Empty
        }
    }
}
