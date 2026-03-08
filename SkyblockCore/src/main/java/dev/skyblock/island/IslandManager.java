package dev.skyblock.island;

import dev.skyblock.SkyblockCore;
import dev.skyblock.db.IslandRepository;
import org.bukkit.Bukkit;
import org.bukkit.World;
import org.bukkit.WorldCreator;
import org.bukkit.entity.Player;
import org.bukkit.generator.ChunkGenerator;
import org.bukkit.generator.WorldInfo;

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
