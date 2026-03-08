package dev.skyblock.command;
import dev.skyblock.SkyblockCore;
import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.jetbrains.annotations.NotNull;
public class ShopCommand implements CommandExecutor {
    public ShopCommand(SkyblockCore plugin) {}
    @Override
    public boolean onCommand(@NotNull CommandSender sender, @NotNull Command command, @NotNull String label, @NotNull String[] args) {
        sender.sendMessage("§cShop system not yet implemented.");
        return true;
    }
}
