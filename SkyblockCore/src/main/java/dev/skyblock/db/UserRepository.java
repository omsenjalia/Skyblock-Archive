package dev.skyblock.db;

import dev.skyblock.SkyblockCore;
import dev.skyblock.user.User;
import org.bukkit.Bukkit;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.UUID;
import java.util.function.Consumer;

public class UserRepository {
    private final SkyblockCore plugin;
    private final DatabaseManager databaseManager;

    public UserRepository(SkyblockCore plugin) {
        this.plugin = plugin;
        this.databaseManager = plugin.getDatabaseManager();
    }

    public void loadUser(UUID uuid, String name, Consumer<User> callback) {
        databaseManager.queryAsync("SELECT * FROM player WHERE player = ?", rs -> {
            try {
                User user = null;
                if (rs.next()) {
                    user = new User(uuid, name); // use real UUID passed in
                    user.setMoney(rs.getDouble("money"));
                    user.setMobcoin(rs.getInt("mobcoin"));
                    user.setMana(rs.getInt("mana"));
                    user.setBlocks(rs.getInt("blocks"));
                    user.setKills(rs.getInt("kills"));
                    user.setDeaths(rs.getInt("deaths"));
                    user.setXp(rs.getInt("xp"));
                    user.setXpbank(rs.getInt("xpbank"));
                    user.setChips(rs.getInt("chips"));
                    user.setBounty(rs.getInt("bounty"));
                }
                final User result = user;
                // sync back to main thread before touching Bukkit API
                Bukkit.getScheduler().runTask(SkyblockCore.getInstance(), () -> callback.accept(result));
            } catch (SQLException e) {
                e.printStackTrace();
            }
        }, name);
    }

    public void saveUser(User user) {
        databaseManager.executeAsync(
            "INSERT OR REPLACE INTO player (player, money, mobcoin, mana, blocks, kills, deaths, xp, xpbank, chips, bounty) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            user.getUsername(), user.getMoney(), user.getMobcoin(), user.getMana(), user.getBlocks(), user.getKills(), user.getDeaths(),
            user.getXp(), user.getXpbank(), user.getChips(), user.getBounty()
        );
    }
}
