package dev.skyblock.command;
import dev.skyblock.SkyblockCore;
import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.jetbrains.annotations.NotNull;
public class WarpCommand implements CommandExecutor {
    public WarpCommand(SkyblockCore plugin) {}
    @Override
    public boolean onCommand(@NotNull CommandSender sender, @NotNull Command command, @NotNull String label, @NotNull String[] args) {
        sender.sendMessage("§cWarp system not yet implemented.");
        return true;
    }
}
