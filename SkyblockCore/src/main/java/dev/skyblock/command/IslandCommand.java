package dev.skyblock.command;

import dev.skyblock.SkyblockCore;
import dev.skyblock.island.Island;
import dev.skyblock.user.User;
import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.bukkit.entity.Player;
import org.jetbrains.annotations.NotNull;

public class IslandCommand implements CommandExecutor {
    private final SkyblockCore plugin;

    public IslandCommand(SkyblockCore plugin) {
        this.plugin = plugin;
    }

    @Override
    public boolean onCommand(@NotNull CommandSender sender, @NotNull Command command, @NotNull String label, @NotNull String[] args) {
        if (!(sender instanceof Player player)) {
            sender.sendMessage("This command can only be used by players.");
            return true;
        }

        User user = plugin.getUserManager().getOnlineUser(player.getUniqueId());
        if (user == null) return true;

        if (args.length == 0) {
            player.sendMessage("§e/is create <name> §7- Create an island");
            player.sendMessage("§e/is go §7- Teleport to your island");
            return true;
        }

        switch (args[0].toLowerCase()) {
            case "create":
                if (args.length < 2) {
                    player.sendMessage("§cUsage: /is create <name>");
                    return true;
                }
                String name = args[1];
                if (!user.getIsland().isEmpty()) {
                    player.sendMessage("§cYou already have an island!");
                    return true;
                }
                Island island = plugin.getIslandManager().createIsland(player, name);
                user.setIsland(name);
                player.sendMessage("§aIsland §e" + name + " §acreated!");
                break;
            case "go":
                if (user.getIsland().isEmpty()) {
                    player.sendMessage("§cYou don't have an island!");
                    return true;
                }
                // Teleport logic
                player.sendMessage("§aTeleporting to your island...");
                break;
            default:
                player.sendMessage("§cUnknown subcommand.");
                break;
        }

        return true;
    }
}
