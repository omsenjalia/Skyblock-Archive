package dev.skyblock.scoreboard;

import dev.skyblock.SkyblockCore;
import dev.skyblock.island.Island;
import dev.skyblock.user.User;
import dev.skyblock.util.Format;
import org.bukkit.Bukkit;
import org.bukkit.entity.Player;
import org.bukkit.scoreboard.*;

import java.util.Optional;

public class ScoreboardManager {
    private final SkyblockCore plugin;

    public ScoreboardManager(SkyblockCore plugin) {
        this.plugin = plugin;
    }

    public void updateScoreboard(Player player) {
        User user = plugin.getUserManager().getOnlineUser(player.getUniqueId());
        if (user == null) return;

        Scoreboard board = Bukkit.getScoreboardManager().getNewScoreboard();
        Objective obj = board.registerNewObjective("skyblock", Criteria.DUMMY, "§3§lF§bT §eSkyBlock");
        obj.setDisplaySlot(DisplaySlot.SIDEBAR);

        String worldName = player.getWorld().getName();
        Optional<Island> islandOpt = plugin.getIslandManager().getOnlineIslandByWorld(worldName);

        int line = 14;

        // Header
        obj.getScore("§7▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬").setScore(line--);

        // Always shown
        obj.getScore("§3 ✪ §aIGN: §f" + player.getName()).setScore(line--);
        obj.getScore("§3 ✪ §ePlayers: §f" + Bukkit.getOnlinePlayers().size() + "/" + Bukkit.getMaxPlayers()).setScore(line--);
        obj.getScore("§3 ✪ §6Money: §f" + Format.formatMoney(user.getMoney()) + "$").setScore(line--);
        obj.getScore("§3 ✪ §dMana: §f" + Format.formatMoney(user.getMana())).setScore(line--);
        obj.getScore("§3 ✪ §eMobCoin: §f" + Format.formatMoney(user.getMobcoin())).setScore(line--);
        obj.getScore("§3 ✪ §cXP: §f" + Format.formatMoney(user.getXp())).setScore(line--);

        if (islandOpt.isEmpty()) {
            // Spawn/hub mode
            obj.getScore("§d † Your Stats †").setScore(line--);

            // Rank via LuckPerms if available, fallback to Default
            String rank = "Default";
            try {
                if (Bukkit.getPluginManager().getPlugin("LuckPerms") != null) {
                    net.luckperms.api.LuckPerms lp = net.luckperms.api.LuckPermsProvider.get();
                    net.luckperms.api.model.user.User lpUser = lp.getUserManager().getUser(player.getUniqueId());
                    if (lpUser != null) {
                        rank = lpUser.getPrimaryGroup();
                        rank = rank.substring(0, 1).toUpperCase() + rank.substring(1);
                    }
                }
            } catch (Exception ignored) {}

            obj.getScore("§3 》 §aRank: §f" + rank).setScore(line--);

            String islandName = user.getIsland().isEmpty() ? "---" : user.getIsland();
            obj.getScore("§3 》 §2Island: §f" + islandName).setScore(line--);

            String gangName = user.getGang() == null || user.getGang().isEmpty() ? "---" : user.getGang();
            obj.getScore("§3 》 §eGang: §f" + gangName).setScore(line--);

            obj.getScore("§3 》 §9K-D: §f" + user.getKills() + "-" + user.getDeaths()).setScore(line--);
            obj.getScore("§3 》 §eStreak: §f" + user.getKillstreak()).setScore(line--);
            obj.getScore("§b ➺ §6Do /is help").setScore(line--);
        } else {
            // Island mode
            Island island = islandOpt.get();
            obj.getScore("§d † Island Stats †").setScore(line--);
            obj.getScore("§3 》 §bIsland: §f" + island.getName()).setScore(line--);
            obj.getScore("§3 》 §6Owner: §f" + island.getReceiver()).setScore(line--);
            obj.getScore("§3 》 §5Bank: §f" + Format.formatMoney(island.getMoney()) + "$").setScore(line--);
            obj.getScore("§3 》 §eLevel: §f" + island.getLevel()).setScore(line--);
            obj.getScore("§3 》 §2Points: §f" + island.getPoints()).setScore(line--);
            String handItem = player.getInventory().getItemInMainHand().getType().name().toLowerCase().replace("_", " ");
            obj.getScore("§3 》 §aItem: §f" + handItem).setScore(line--);
            obj.getScore("§b ➺ §6Do /is help").setScore(line--);
        }

        // Footer
        obj.getScore("§7▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬ ").setScore(line--);

        player.setScoreboard(board);
    }

    public void refreshAll() {
        for (Player player : Bukkit.getOnlinePlayers()) {
            updateScoreboard(player);
        }
    }

}
