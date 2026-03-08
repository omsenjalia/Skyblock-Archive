package dev.skyblock.command;
import dev.skyblock.SkyblockCore;
import org.bukkit.Bukkit;
import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.jetbrains.annotations.NotNull;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;
public class BaltopCommand implements CommandExecutor {
    private final SkyblockCore plugin;
    public BaltopCommand(SkyblockCore plugin) { this.plugin = plugin; }
    @Override
    public boolean onCommand(@NotNull CommandSender sender, @NotNull Command command, @NotNull String label, @NotNull String[] args) {
        plugin.getDatabaseManager().queryAsync("SELECT player, money FROM player ORDER BY money DESC LIMIT 10", rs -> {
            List<String> lines = new ArrayList<>();
            lines.add("§6--- Bal Top ---");
            try {
                int rank = 1;
                while (rs.next()) {
                    lines.add("§e" + rank + ". §7" + rs.getString("player") + ": §a$" + rs.getDouble("money"));
                    rank++;
                }
            } catch (SQLException e) { e.printStackTrace(); }
            Bukkit.getScheduler().runTask(plugin, () -> {
                for (String line : lines) sender.sendMessage(line);
            });
        });
        return true;
    }
}
