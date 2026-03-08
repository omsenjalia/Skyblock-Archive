package dev.skyblock.command;

import org.bukkit.Bukkit;
import org.bukkit.Location;
import org.bukkit.World;
import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.bukkit.entity.Player;

public class SpawnCommand implements CommandExecutor {
    @Override
    public boolean onCommand(CommandSender sender, Command command, String label, String[] args) {
        if (!(sender instanceof Player player)) {
            sender.sendMessage("Players only.");
            return true;
        }
        World spawn = Bukkit.getWorld("world");
        if (spawn == null) {
            player.sendMessage("§cSpawn world not found.");
            return true;
        }
        player.teleport(spawn.getSpawnLocation());
        player.sendMessage("§aTeleported to spawn!");
        return true;
    }
}
