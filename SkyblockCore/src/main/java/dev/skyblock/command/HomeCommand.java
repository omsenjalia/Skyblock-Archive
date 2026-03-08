package dev.skyblock.command;
import dev.skyblock.SkyblockCore;
import dev.skyblock.island.Island;
import dev.skyblock.user.User;
import org.bukkit.Location;
import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.bukkit.entity.Player;
import org.jetbrains.annotations.NotNull;
import java.util.Optional;
public class HomeCommand implements CommandExecutor {
    private final SkyblockCore plugin;
    public HomeCommand(SkyblockCore plugin) { this.plugin = plugin; }
    @Override
    public boolean onCommand(@NotNull CommandSender sender, @NotNull Command command, @NotNull String label, @NotNull String[] args) {
        if (!(sender instanceof Player player)) return true;
        User user = plugin.getUserManager().getOnlineUser(player.getUniqueId());
        if (user.getIsland().isEmpty()) {
            player.sendMessage("§cYou don't have an island!");
            return true;
        }
        Optional<Island> island = plugin.getIslandManager().getIslandByName(user.getIsland());
        if (island.isEmpty()) return true;
        if (label.equalsIgnoreCase("sethome")) {
            String name = args.length > 0 ? args[0] : "default";
            island.get().setHome(name, player.getLocation());
            player.sendMessage("§aHome '" + name + "' set!");
            return true;
        }
        String name = args.length > 0 ? args[0] : "default";
        Location loc = island.get().getHome(name, player.getWorld());
        if (loc != null) {
            player.teleport(loc);
            player.sendMessage("§aTeleported home!");
        } else {
            player.sendMessage("§cHome not found.");
        }
        return true;
    }
}
