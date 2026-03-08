package dev.skyblock.command;
import dev.skyblock.SkyblockCore;
import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.bukkit.entity.Player;
import org.jetbrains.annotations.NotNull;
public class ShopCommand implements CommandExecutor {
    private final SkyblockCore plugin;
    public ShopCommand(SkyblockCore plugin) {
        this.plugin = plugin;
    }
    @Override
    public boolean onCommand(@NotNull CommandSender sender, @NotNull Command command, @NotNull String label, @NotNull String[] args) {
        if (sender instanceof Player player) {
            plugin.getShopManager().openMainMenu(player);
        }
        return true;
    }
}
