package dev.skyblock.command;
import dev.skyblock.SkyblockCore;
import dev.skyblock.user.User;
import org.bukkit.Material;
import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.bukkit.entity.Player;
import org.bukkit.inventory.ItemStack;
import org.jetbrains.annotations.NotNull;
import java.util.HashMap;
import java.util.Map;
public class SellCommand implements CommandExecutor {
    private final SkyblockCore plugin;
    private final Map<Material, Double> prices = new HashMap<>();
    public SellCommand(SkyblockCore plugin) {
        this.plugin = plugin;
        prices.put(Material.COBBLESTONE, 0.1);
        prices.put(Material.DIAMOND, 10.0);
        prices.put(Material.IRON_INGOT, 2.0);
        prices.put(Material.GOLD_INGOT, 5.0);
    }
    @Override
    public boolean onCommand(@NotNull CommandSender sender, @NotNull Command command, @NotNull String label, @NotNull String[] args) {
        if (!(sender instanceof Player player)) return true;
        User user = plugin.getUserManager().getOnlineUser(player.getUniqueId());
        ItemStack item = player.getInventory().getItemInMainHand();
        if (item == null || !prices.containsKey(item.getType())) {
            player.sendMessage("§cYou cannot sell this item.");
            return true;
        }
        double price = prices.get(item.getType()) * item.getAmount();
        user.addMoney(price);
        player.getInventory().setItemInMainHand(null);
        player.sendMessage("§aYou sold items for §e$" + price);
        return true;
    }
}
