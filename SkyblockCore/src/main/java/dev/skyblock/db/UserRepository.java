package dev.skyblock.db;

import dev.skyblock.SkyblockCore;
import dev.skyblock.user.User;

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

    public void loadUser(String name, Consumer<User> callback) {
        databaseManager.queryAsync("SELECT * FROM player WHERE player = ?", rs -> {
            try {
                if (rs.next()) {
                    // In a real implementation, we'd need the UUID here,
                    // but for this conversion we'll use a random one if not available
                    User user = new User(UUID.randomUUID(), name);
                    user.setMoney(rs.getDouble("money"));
                    user.setMobcoin(rs.getInt("mobcoin"));
                    user.setMana(rs.getInt("mana"));
                    user.setBlocks(rs.getInt("blocks"));
                    user.setKills(rs.getInt("kills"));
                    user.setDeaths(rs.getInt("deaths"));
                    callback.accept(user);
                } else {
                    callback.accept(null);
                }
            } catch (SQLException e) {
                e.printStackTrace();
            }
        }, name);
    }

    public void saveUser(User user) {
        databaseManager.executeAsync(
            "INSERT OR REPLACE INTO player (player, money, mobcoin, mana, blocks, kills, deaths) VALUES (?, ?, ?, ?, ?, ?, ?)",
            user.getUsername(), user.getMoney(), user.getMobcoin(), user.getMana(), user.getBlocks(), user.getKills(), user.getDeaths()
        );
    }
}
