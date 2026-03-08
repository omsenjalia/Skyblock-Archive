package dev.skyblock.events;

import dev.skyblock.SkyblockCore;
import dev.skyblock.island.Island;
import dev.skyblock.tiles.*;
import dev.skyblock.user.User;
import org.bukkit.Location;
import org.bukkit.Material;
import org.bukkit.block.Block;
import org.bukkit.entity.Player;
import org.bukkit.event.EventHandler;
import org.bukkit.event.Listener;
import org.bukkit.event.player.PlayerJoinEvent;
import org.bukkit.event.player.PlayerMoveEvent;
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
    public void onPlayerMove(PlayerMoveEvent event) {
        if (!event.hasChangedBlock()) return;
        Player player = event.getPlayer();
        String worldName = player.getWorld().getName();
        Island island = plugin.getIslandManager().getOnlineIslandByWorld(worldName).orElse(null);
        if (island == null) return;

        org.bukkit.World world = player.getWorld();
        Location spawn = world.getSpawnLocation();
        Location to = event.getTo();
        double radius = island.getRadius();

        if (Math.abs(to.getX() - spawn.getX()) > radius || Math.abs(to.getZ() - spawn.getZ()) > radius) {
            event.setCancelled(true);
            player.sendMessage("§cYou have reached the island border!");
        }
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
        Block block = event.getBlock();

        // Island build permission
        Island island = getIslandForBlock(block.getLocation());
        if (island != null && !island.isAnOwner(player.getName()) && !island.getHelpers().contains(player.getName().toLowerCase())) {
            event.setCancelled(true);
            player.sendMessage("§cYou cannot break blocks on this island.");
            return;
        }

        User user = plugin.getUserManager().getOnlineUser(player.getUniqueId());
        if (user != null) user.addBlocks(1);

        TileManager tm = plugin.getTileManager();
        if (tm.hasTile(block.getLocation())) {
            tm.removeTile(block.getLocation());
        }
    }

    // Ore Gens — fixed ore per block
    private static final java.util.Map<Material, Material> ORE_GEN_BLOCKS = java.util.Map.of(
        Material.CYAN_GLAZED_TERRACOTTA,   Material.DIAMOND_ORE,
        Material.GREEN_GLAZED_TERRACOTTA,  Material.EMERALD_ORE,
        Material.WHITE_GLAZED_TERRACOTTA,  Material.IRON_ORE,
        Material.YELLOW_GLAZED_TERRACOTTA, Material.GOLD_ORE,
        Material.BLUE_GLAZED_TERRACOTTA,   Material.LAPIS_ORE
    );

    // Functional tiles
    private static final Material AUTOSELLER_BLOCK = Material.BARREL;
    private static final Material AUTOMINER_BLOCK  = Material.SLIME_BLOCK;
    private static final Material CATALYST_BLOCK   = Material.PURPLE_GLAZED_TERRACOTTA;
    private static final Material HOPPER_BLOCK     = Material.HOPPER;

    @EventHandler
    public void onBlockPlace(BlockPlaceEvent event) {
        Player player = event.getPlayer();
        Block block = event.getBlockPlaced();
        TileManager tm = plugin.getTileManager();

        // Check island permission
        Island island = getIslandForBlock(block.getLocation());
        if (island != null && !island.isAnOwner(player.getName()) && !island.getHelpers().contains(player.getName().toLowerCase())) {
            event.setCancelled(true);
            player.sendMessage("§cYou cannot build on this island.");
            return;
        }

        ItemStack item = event.getItemInHand();
        int level = 1;
        if (item.hasItemMeta()) {
            var pdc = item.getItemMeta().getPersistentDataContainer();
            var key = new org.bukkit.NamespacedKey(plugin, "tile_level");
            level = pdc.getOrDefault(key, org.bukkit.persistence.PersistentDataType.INTEGER, 1);
        }

        // Ore gen blocks
        if (ORE_GEN_BLOCKS.containsKey(block.getType())) {
            Material oreType = ORE_GEN_BLOCKS.get(block.getType());
            tm.addTile(block.getLocation(), new OreGenTile(block.getLocation(), oreType));
            return;
        }

        // Functional tiles
        TileData data = new TileData(level);
        BaseTile tile = switch (block.getType()) {
            case BARREL     -> new AutoSellerTile(block.getLocation(), data);
            case SLIME_BLOCK -> new AutoMinerTile(block.getLocation(), data);
            case PURPLE_GLAZED_TERRACOTTA -> new CatalystTile(block.getLocation());
            case HOPPER -> level > 1 ? new HopperTile(block.getLocation()) : null;
            default -> null;
        };

        if (tile != null) {
            tm.addTile(block.getLocation(), tile);
        }
    }

    private Island getIslandForBlock(Location loc) {
        String worldName = loc.getWorld().getName();
        return plugin.getIslandManager().getOnlineIslandByWorld(worldName).orElse(null);
    }
}
