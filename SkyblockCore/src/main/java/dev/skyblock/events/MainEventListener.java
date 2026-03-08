package dev.skyblock.events;

import dev.skyblock.SkyblockCore;
import dev.skyblock.user.User;
import org.bukkit.entity.Player;
import org.bukkit.event.EventHandler;
import org.bukkit.event.Listener;
import org.bukkit.event.player.PlayerJoinEvent;
import org.bukkit.event.player.PlayerQuitEvent;
import org.bukkit.event.block.BlockBreakEvent;
import org.bukkit.event.block.BlockPlaceEvent;

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
    }

    @EventHandler
    public void onBlockPlace(BlockPlaceEvent event) {
        // Implement island building permissions
    }
}
