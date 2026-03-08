package dev.skyblock.command;

import dev.skyblock.SkyblockCore;
import dev.skyblock.user.User;
import dev.skyblock.util.Format;
import dev.skyblock.util.SellPrices;
import net.kyori.adventure.text.Component;
import org.bukkit.Material;
import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.bukkit.entity.Player;
import org.bukkit.inventory.ItemStack;
import org.jetbrains.annotations.NotNull;

public class SellCommand implements CommandExecutor {
    private final SkyblockCore plugin;

    public SellCommand(SkyblockCore plugin) {
        this.plugin = plugin;
    }

    @Override
    public boolean onCommand(@NotNull CommandSender sender, @NotNull Command command, @NotNull String label, @NotNull String[] args) {
        if (!(sender instanceof Player player)) return true;
        User user = plugin.getUserManager().getOnlineUser(player.getUniqueId());
        if (user == null) return true;

        String mode = args.length > 0 ? args[0].toLowerCase() : "hand";

        if (mode.equals("all")) {
            double total = 0;
            for (ItemStack item : player.getInventory().getContents()) {
                if (item == null || item.getType() == Material.AIR) continue;
                if (item.hasItemMeta() && (item.getItemMeta().hasEnchants() || item.getItemMeta().hasDisplayName())) continue;
                double price = SellPrices.getPrice(item.getType());
                if (price <= 0) continue;
                total += price * item.getAmount();
                player.getInventory().remove(item);
            }
            if (total == 0) {
                player.sendMessage("§cNo sellable items found.");
                return true;
            }
            user.addMoney(total);
            player.sendActionBar(Component.text("§l§6>> §eSold all for §6" + Format.formatMoney(total) + "$ §6<<"));
            plugin.getScoreboardManager().updateScoreboard(player);
        } else {
            // hand (default)
            ItemStack item = player.getInventory().getItemInMainHand();
            if (item == null || item.getType() == Material.AIR) {
                player.sendMessage("§cHold an item to sell.");
                return true;
            }
            if (item.hasItemMeta() && (item.getItemMeta().hasEnchants() || item.getItemMeta().hasDisplayName())) {
                player.sendMessage("§cYou cannot sell enchanted or named items.");
                return true;
            }
            double price = SellPrices.getPrice(item.getType());
            if (price <= 0) {
                player.sendMessage("§cYou cannot sell this item.");
                return true;
            }
            double total = price * item.getAmount();
            user.addMoney(total);
            player.getInventory().setItemInMainHand(new ItemStack(Material.AIR));
            player.sendActionBar(Component.text("§l§6>> §eSold §f" + item.getAmount() + "x §efor §6" + Format.formatMoney(total) + "$ §6<<"));
            plugin.getScoreboardManager().updateScoreboard(player);
        }
        return true;
    }
}
