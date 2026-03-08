package dev.skyblock.command;
import dev.skyblock.SkyblockCore;
import dev.skyblock.gang.Gang;
import dev.skyblock.user.User;
import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.bukkit.entity.Player;
import org.jetbrains.annotations.NotNull;
import java.util.Optional;
public class GangCommand implements CommandExecutor {
    private final SkyblockCore plugin;
    public GangCommand(SkyblockCore plugin) { this.plugin = plugin; }
    @Override
    public boolean onCommand(@NotNull CommandSender sender, @NotNull Command command, @NotNull String label, @NotNull String[] args) {
        if (!(sender instanceof Player player)) return true;
        User user = plugin.getUserManager().getOnlineUser(player.getUniqueId());
        if (args.length == 0) {
            player.sendMessage("§e/gang create <name>");
            player.sendMessage("§e/gang disband");
            player.sendMessage("§e/gang info");
            return true;
        }
        switch (args[0].toLowerCase()) {
            case "create":
                if (args.length < 2) return true;
                if (!user.getGang().isEmpty()) {
                    player.sendMessage("§cYou are already in a gang!");
                    return true;
                }
                plugin.getGangManager().createGang(player, args[1]);
                player.sendMessage("§aGang created!");
                break;
            case "disband":
                // Logic for disband
                player.sendMessage("§aGang disbanded!");
                break;
            case "info":
                player.sendMessage("§6--- Gang Info ---");
                player.sendMessage("§eName: §7" + user.getGang());
                break;
        }
        return true;
    }
}
