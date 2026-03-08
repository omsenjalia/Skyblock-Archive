package dev.skyblock.command;
import dev.skyblock.SkyblockCore;
import dev.skyblock.user.User;
import org.bukkit.Bukkit;
import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.bukkit.entity.Player;
import org.jetbrains.annotations.NotNull;
public class BalCommand implements CommandExecutor {
    private final SkyblockCore plugin;
    public BalCommand(SkyblockCore plugin) { this.plugin = plugin; }
    @Override
    public boolean onCommand(@NotNull CommandSender sender, @NotNull Command command, @NotNull String label, @NotNull String[] args) {
        if (!(sender instanceof Player player)) return true;
        User user = plugin.getUserManager().getOnlineUser(player.getUniqueId());
        if (args.length == 0) {
            player.sendMessage("§aYour balance: §e$" + user.getMoney());
        } else {
            Player target = Bukkit.getPlayer(args[0]);
            if (target != null) {
                User targetUser = plugin.getUserManager().getOnlineUser(target.getUniqueId());
                player.sendMessage("§e" + target.getName() + "'s §abalance: §e$" + targetUser.getMoney());
            } else {
                player.sendMessage("§cPlayer not found.");
            }
        }
        return true;
    }
}
