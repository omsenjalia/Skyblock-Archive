package dev.skyblock.events;

import dev.skyblock.SkyblockCore;
import dev.skyblock.island.Island;
import dev.skyblock.tiles.*;
import dev.skyblock.user.User;
import org.bukkit.Location;
import org.bukkit.Material;
import org.bukkit.NamespacedKey;
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
import org.bukkit.inventory.meta.ItemMeta;
import org.bukkit.persistence.PersistentDataType;

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

        // Island permission check
        Island island = getIslandForBlock(block.getLocation());
        if (island != null && !island.isAnOwner(player.getName()) && !island.getHelpers().contains(player.getName().toLowerCase())) {
            event.setCancelled(true);
            player.sendMessage("§cYou cannot break blocks on this island.");
            return;
        }

        User user = plugin.getUserManager().getOnlineUser(player.getUniqueId());
        if (user != null) user.addBlocks(1);

        TileManager tm = plugin.getTileManager();
        Location loc = block.getLocation();
        if (tm.hasTile(loc)) {
            BaseTile tile = tm.getTile(loc);
            tm.removeTile(loc);

            // Cancel default drops and give back the tile item
            event.setDropItems(false);
            ItemStack drop = getTileItem(tile);
            if (drop != null) {
                block.getWorld().dropItemNaturally(block.getLocation().add(0.5, 0.5, 0.5), drop);
            }
        }
    }

    private ItemStack getTileItem(BaseTile tile) {
        if (tile instanceof OreGenTile ogt) {
            return TileItemFactory.createOreGen(ogt.getOreType(), 1);
        } else if (tile instanceof AutoSellerTile ast) {
            return TileItemFactory.createAutoSeller(ast.getData().getLevel(), ast.getData().getType());
        } else if (tile instanceof AutoMinerTile amt) {
            return TileItemFactory.createAutoMiner(amt.getData().getLevel(), amt.getData().getFortuneEnabled(), amt.getData().getFortuneLevel());
        } else if (tile instanceof CatalystTile) {
            return TileItemFactory.createCatalyst();
        }
        return null;
    }

    @EventHandler
    public void onBlockPlace(BlockPlaceEvent event) {
        Player player = event.getPlayer();
        Block block = event.getBlockPlaced();
        ItemStack item = event.getItemInHand();

        // Only process if item has our tile_type PDC tag
        if (!item.hasItemMeta()) return;
        ItemMeta meta = item.getItemMeta();
        NamespacedKey typeKey = new NamespacedKey(plugin, TileItemFactory.KEY_TILE_TYPE);
        if (!meta.getPersistentDataContainer().has(typeKey, PersistentDataType.STRING)) return;

        String tileType = meta.getPersistentDataContainer().get(typeKey, PersistentDataType.STRING);

        // Island permission check
        Island island = getIslandForBlock(block.getLocation());
        if (island != null && !island.isAnOwner(player.getName()) && !island.getHelpers().contains(player.getName().toLowerCase())) {
            event.setCancelled(true);
            player.sendMessage("§cYou cannot build on this island.");
            return;
        }

        TileManager tm = plugin.getTileManager();
        NamespacedKey levelKey = new NamespacedKey(plugin, TileItemFactory.KEY_TILE_LEVEL);
        int level = meta.getPersistentDataContainer().getOrDefault(levelKey, PersistentDataType.INTEGER, 1);

        BaseTile tile = switch (tileType) {
            case TileItemFactory.TYPE_OREGEN -> {
                NamespacedKey oreKey = new NamespacedKey(plugin, TileItemFactory.KEY_ORE_TYPE);
                String oreName = meta.getPersistentDataContainer().get(oreKey, PersistentDataType.STRING);
                Material oreType = oreName != null ? Material.getMaterial(oreName) : Material.IRON_ORE;
                if (oreType == null) oreType = Material.IRON_ORE;
                yield new OreGenTile(block.getLocation(), oreType);
            }
            case TileItemFactory.TYPE_AUTOSELLER -> {
                TileData d = new TileData(level);
                NamespacedKey sellerType = new NamespacedKey(plugin, "seller_type");
                d.setType(meta.getPersistentDataContainer().getOrDefault(sellerType, PersistentDataType.INTEGER, 0));
                yield new AutoSellerTile(block.getLocation(), d);
            }
            case TileItemFactory.TYPE_AUTOMINER -> {
                TileData d = new TileData(level);
                NamespacedKey fortuneKey    = new NamespacedKey(plugin, "fortune");
                NamespacedKey fortuneLvlKey = new NamespacedKey(plugin, "fortune_level");
                d.setFortuneEnabled(meta.getPersistentDataContainer().getOrDefault(fortuneKey,    PersistentDataType.INTEGER, 0));
                d.setFortuneLevel(meta.getPersistentDataContainer().getOrDefault(fortuneLvlKey,   PersistentDataType.INTEGER, 1));
                yield new AutoMinerTile(block.getLocation(), d);
            }
            case TileItemFactory.TYPE_CATALYST -> new CatalystTile(block.getLocation());
            case TileItemFactory.TYPE_HOPPER   -> new HopperTile(block.getLocation());
            default -> null;
        };

        if (tile != null) {
            tm.addTile(block.getLocation(), tile);
            player.sendMessage("§a" + tileType + " placed and activated!");
        }
    }

    private Island getIslandForBlock(Location loc) {
        String worldName = loc.getWorld().getName();
        return plugin.getIslandManager().getOnlineIslandByWorld(worldName).orElse(null);
    }
}
