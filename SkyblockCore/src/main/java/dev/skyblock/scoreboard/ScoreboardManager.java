package dev.skyblock.scoreboard;

import dev.skyblock.SkyblockCore;
import dev.skyblock.user.User;
import org.bukkit.Bukkit;
import org.bukkit.entity.Player;
import org.bukkit.scoreboard.*;

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

        obj.getScore("§7----------------").setScore(6);
        obj.getScore("§fSeason: §e1").setScore(5);
        obj.getScore("§fIsland Level: §e" + (user.getIsland().isEmpty() ? "0" : "1")).setScore(4);
        obj.getScore("§fBalance: §a$" + (int)user.getMoney()).setScore(3);
        obj.getScore("§fKills: §c" + user.getKills()).setScore(2);
        obj.getScore("§7---------------- ").setScore(1);

        player.setScoreboard(board);
    }
}
