package dev.skyblock.tiles;

import dev.skyblock.SkyblockCore;
import org.bukkit.Location;
import org.bukkit.scheduler.BukkitRunnable;
import java.util.Map;
import java.util.concurrent.ConcurrentHashMap;

public class TileManager {
    private final SkyblockCore plugin;
    private final Map<Location, BaseTile> tiles = new ConcurrentHashMap<>();
    private final BukkitRunnable updateTask;

    public TileManager(SkyblockCore plugin) {
        this.plugin = plugin;
        this.updateTask = new BukkitRunnable() {
            @Override
            public void run() {
                for (BaseTile tile : tiles.values()) {
                    tile.onUpdate();
                }
            }
        };
        this.updateTask.runTaskTimer(plugin, 20L, 10L);
    }

    public void addTile(Location loc, BaseTile tile) {
        tiles.put(loc, tile);
    }

    public void removeTile(Location loc) {
        tiles.remove(loc);
    }

    public void shutdown() {
        updateTask.cancel();
        tiles.clear();
    }
}
