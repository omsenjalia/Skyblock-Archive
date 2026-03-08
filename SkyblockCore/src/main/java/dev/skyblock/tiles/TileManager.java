package dev.skyblock.tiles;

import dev.skyblock.SkyblockCore;
import org.bukkit.Location;
import org.bukkit.Material;
import org.bukkit.configuration.file.YamlConfiguration;
import org.bukkit.scheduler.BukkitRunnable;
import java.io.File;
import java.io.IOException;
import java.util.Map;
import java.util.concurrent.ConcurrentHashMap;

public class TileManager {
    private final SkyblockCore plugin;
    private final Map<Location, BaseTile> tiles = new ConcurrentHashMap<>();
    private final BukkitRunnable updateTask;
    private final File dataFile;
    private YamlConfiguration dataConfig;

    public TileManager(SkyblockCore plugin) {
        this.plugin = plugin;
        this.dataFile = new File(plugin.getDataFolder(), "tiles.yml");
        this.dataConfig = YamlConfiguration.loadConfiguration(dataFile);
        loadTiles();

        this.updateTask = new BukkitRunnable() {
            @Override
            public void run() {
                for (BaseTile tile : tiles.values()) {
                    tile.onUpdate();
                }
            }
        };
        this.updateTask.runTaskTimer(plugin, 20L, 1L); // run every tick
    }

    public void addTile(Location loc, BaseTile tile) {
        tiles.put(loc, tile);
        saveTile(loc, tile);
    }

    public void removeTile(Location loc) {
        tiles.remove(loc);
        removeTileFromConfig(loc);
    }

    public BaseTile getTile(Location loc) {
        return tiles.get(loc);
    }

    public boolean hasTile(Location loc) {
        return tiles.containsKey(loc);
    }

    private String locKey(Location loc) {
        return loc.getWorld().getName() + "," + loc.getBlockX() + "," + loc.getBlockY() + "," + loc.getBlockZ();
    }

    private void saveTile(Location loc, BaseTile tile) {
        String key = locKey(loc);
        String type = tile instanceof AutoSellerTile ? "AUTOSELLER"
                    : tile instanceof AutoMinerTile  ? "AUTOMINER"
                    : tile instanceof CatalystTile   ? "CATALYST"
                    : tile instanceof HopperTile     ? "HOPPER"
                    : tile instanceof OreGenTile     ? "OREGEN_" + ((OreGenTile) tile).getOreType().name()
                    : "UNKNOWN";
        dataConfig.set(key + ".type", type);
        if (tile instanceof AutoSellerTile ast) {
            dataConfig.set(key + ".level", ast.getData().getLevel());
            dataConfig.set(key + ".tileType", ast.getData().getType());
        } else if (tile instanceof AutoMinerTile amt) {
            dataConfig.set(key + ".level", amt.getData().getLevel());
            dataConfig.set(key + ".fortune", amt.getData().getFortuneEnabled());
            dataConfig.set(key + ".fortuneLevel", amt.getData().getFortuneLevel());
        }
        try { dataConfig.save(dataFile); } catch (IOException e) { e.printStackTrace(); }
    }

    private void removeTileFromConfig(Location loc) {
        dataConfig.set(locKey(loc), null);
        try { dataConfig.save(dataFile); } catch (IOException e) { e.printStackTrace(); }
    }

    private void loadTiles() {
        for (String key : dataConfig.getKeys(false)) {
            try {
                String[] parts = key.split(",");
                org.bukkit.World world = plugin.getServer().getWorld(parts[0]);
                if (world == null) continue;
                int x = Integer.parseInt(parts[1]);
                int y = Integer.parseInt(parts[2]);
                int z = Integer.parseInt(parts[3]);
                Location loc = new Location(world, x, y, z);

                String type = dataConfig.getString(key + ".type", "");
                BaseTile tile = switch (type) {
                    case "AUTOSELLER" -> {
                        TileData d = new TileData(dataConfig.getInt(key + ".level", 1));
                        d.setType(dataConfig.getInt(key + ".tileType", 0));
                        yield new AutoSellerTile(loc, d);
                    }
                    case "AUTOMINER" -> {
                        TileData d = new TileData(dataConfig.getInt(key + ".level", 1));
                        d.setFortuneEnabled(dataConfig.getInt(key + ".fortune", 0));
                        d.setFortuneLevel(dataConfig.getInt(key + ".fortuneLevel", 1));
                        yield new AutoMinerTile(loc, d);
                    }
                    case "CATALYST" -> new CatalystTile(loc);
                    case "HOPPER"   -> new HopperTile(loc);
                    default -> {
                        if (type.startsWith("OREGEN_")) {
                            Material mat = Material.getMaterial(type.substring(7));
                            if (mat != null) yield new OreGenTile(loc, mat);
                        }
                        yield null;
                    }
                };
                if (tile != null) tiles.put(loc, tile);
            } catch (Exception e) {
                plugin.getLogger().warning("Failed to load tile at " + key + ": " + e.getMessage());
            }
        }
        plugin.getLogger().info("Loaded " + tiles.size() + " tiles.");
    }

    public void shutdown() {
        updateTask.cancel();
        // Save all tiles
        for (Map.Entry<Location, BaseTile> entry : tiles.entrySet()) {
            saveTile(entry.getKey(), entry.getValue());
        }
        tiles.clear();
    }
}
