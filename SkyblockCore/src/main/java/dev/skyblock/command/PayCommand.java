package dev.skyblock.command;
import dev.skyblock.SkyblockCore;
import dev.skyblock.user.User;
import dev.skyblock.util.Format;
import org.bukkit.Bukkit;
import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.bukkit.entity.Player;
import org.jetbrains.annotations.NotNull;
public class PayCommand implements CommandExecutor {
    private final SkyblockCore plugin;
    public PayCommand(SkyblockCore plugin) { this.plugin = plugin; }
    @Override
    public boolean onCommand(@NotNull CommandSender sender, @NotNull Command command, @NotNull String label, @NotNull String[] args) {
        if (!(sender instanceof Player player)) return true;
        if (args.length < 2) {
            player.sendMessage("§cUsage: /pay <player> <amount>");
            return true;
        }
        Player target = Bukkit.getPlayer(args[0]);
        if (target == null) {
            player.sendMessage("§cPlayer is not online.");
            return true;
        }
        double amount;
        try { amount = Double.parseDouble(args[1]); } catch (NumberFormatException e) {
            player.sendMessage("§cInvalid amount.");
            return true;
        }
        if (amount <= 0) {
            player.sendMessage("§cAmount must be positive.");
            return true;
        }
        User user = plugin.getUserManager().getOnlineUser(player.getUniqueId());
        User targetUser = plugin.getUserManager().getOnlineUser(target.getUniqueId());
        if (user.getMoney() < amount) {
            player.sendMessage("§cYou don't have enough money!");
            return true;
        }
        user.removeMoney(amount);
        targetUser.addMoney(amount);
        player.sendMessage("§aYou paid §e$" + Format.formatMoney(amount) + " §ato §e" + target.getName());
        target.sendMessage("§aYou received §e$" + Format.formatMoney(amount) + " §afrom §e" + player.getName());

        plugin.getScoreboardManager().updateScoreboard(player);
        plugin.getScoreboardManager().updateScoreboard(target);
        return true;
    }
}
