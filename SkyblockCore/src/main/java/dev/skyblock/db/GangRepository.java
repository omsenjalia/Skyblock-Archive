package dev.skyblock.db;

import dev.skyblock.SkyblockCore;
import dev.skyblock.gang.Gang;

public class GangRepository {
    private final DatabaseManager databaseManager;

    public GangRepository(SkyblockCore plugin) {
        this.databaseManager = plugin.getDatabaseManager();
    }

    public void saveGang(Gang gang) {
        databaseManager.executeAsync(
            "INSERT OR REPLACE INTO creator (gang, leader, level, points, motd) VALUES (?, ?, ?, ?, ?)",
            gang.getName(), gang.getLeader(), gang.getLevel(), gang.getPoints(), gang.getMotd()
        );
    }

    public void saveGangSync(Gang gang) {
        databaseManager.executeSync(
            "INSERT OR REPLACE INTO creator (gang, leader, level, points, motd) VALUES (?, ?, ?, ?, ?)",
            gang.getName(), gang.getLeader(), gang.getLevel(), gang.getPoints(), gang.getMotd()
        );
    }

    public void addGangMember(String player, String gang) {
        databaseManager.executeAsync(
            "INSERT OR REPLACE INTO gang (player, gang, kills, deaths) VALUES (?, ?, ?, ?)",
            player, gang, 0, 0
        );
    }

    public void addGangMemberSync(String player, String gang) {
        databaseManager.executeSync(
            "INSERT OR REPLACE INTO gang (player, gang, kills, deaths) VALUES (?, ?, ?, ?)",
            player, gang, 0, 0
        );
    }
}
