package dev.skyblock.events;

import dev.skyblock.SkyblockCore;
import dev.skyblock.tiles.*;
import dev.skyblock.user.User;
import org.bukkit.Location;
import org.bukkit.Material;
import org.bukkit.block.Block;
import org.bukkit.entity.Player;
import org.bukkit.event.EventHandler;
import org.bukkit.event.Listener;
import org.bukkit.event.player.PlayerJoinEvent;
import org.bukkit.event.player.PlayerQuitEvent;
import org.bukkit.event.block.BlockBreakEvent;
import org.bukkit.event.block.BlockPlaceEvent;
import org.bukkit.inventory.ItemStack;

public class MainEventListener implements Listener {
    private final SkyblockCore plugin;

    public MainEventListener(SkyblockCore plugin) {
        this.plugin = plugin;
    }

    @EventHandler
    public void onJoin(PlayerJoinEvent event) {
        Player player = event.getPlayer();
        plugin.getUserManager().loadUser(player);

        // Update scoreboard
        plugin.getScoreboardManager().updateScoreboard(player);
    }

    @EventHandler
    public void onQuit(PlayerQuitEvent event) {
        plugin.getUserManager().unloadUser(event.getPlayer().getUniqueId());
        plugin.getPetManager().removePet(event.getPlayer());
    }

    @EventHandler
    public void onBlockBreak(BlockBreakEvent event) {
        Player player = event.getPlayer();
        User user = plugin.getUserManager().getOnlineUser(player.getUniqueId());
        if (user != null) {
            user.addBlocks(1);
        }

        TileManager tm = plugin.getTileManager();
        Location loc = event.getBlock().getLocation();
        if (tm.hasTile(loc)) {
            tm.removeTile(loc);
        }
    }

    // These materials represent the custom tile blocks — players place specific blocks to activate tiles
    private static final Material AUTOSELLER_BLOCK = Material.CYAN_GLAZED_TERRACOTTA;
    private static final Material AUTOMINER_BLOCK  = Material.ORANGE_GLAZED_TERRACOTTA;
    private static final Material CATALYST_BLOCK   = Material.PURPLE_GLAZED_TERRACOTTA;
    private static final Material HOPPER_BLOCK     = Material.HOPPER;

    @EventHandler
    public void onBlockPlace(BlockPlaceEvent event) {
        // Implement island building permissions

        Player player = event.getPlayer();
        Block block = event.getBlockPlaced();
        TileManager tm = plugin.getTileManager();

        // Check if player is placing a tile block
        // Tile level is stored in the item's PDC
        ItemStack item = event.getItemInHand();
        int level = 1;
        if (item.hasItemMeta()) {
            var pdc = item.getItemMeta().getPersistentDataContainer();
            var key = new org.bukkit.NamespacedKey(plugin, "tile_level");
            level = pdc.getOrDefault(key, org.bukkit.persistence.PersistentDataType.INTEGER, 1);
        }

        TileData data = new TileData(level);
        BaseTile tile = switch (block.getType()) {
            case CYAN_GLAZED_TERRACOTTA   -> new AutoSellerTile(block.getLocation(), data);
            case ORANGE_GLAZED_TERRACOTTA -> new AutoMinerTile(block.getLocation(), data);
            case PURPLE_GLAZED_TERRACOTTA -> new CatalystTile(block.getLocation());
            case HOPPER -> {
                // Only register as custom HopperTile if item has tile_level PDC tag
                if (level > 1) yield new HopperTile(block.getLocation());
                yield null;
            }
            default -> null;
        };

        if (tile != null) {
            tm.addTile(block.getLocation(), tile);
        }
    }
}
