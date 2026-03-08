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
        // Also save members
        for (String member : gang.getMembers()) {
            addGangMember(member, gang.getName());
        }
    }

    public void saveGangSync(Gang gang) {
        databaseManager.executeSync(
            "INSERT OR REPLACE INTO creator (gang, leader, level, points, motd) VALUES (?, ?, ?, ?, ?)",
            gang.getName(), gang.getLeader(), gang.getLevel(), gang.getPoints(), gang.getMotd()
        );
        for (String member : gang.getMembers()) {
            addGangMemberSync(member, gang.getName());
        }
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

    public void deleteGang(String name) {
        databaseManager.executeAsync("DELETE FROM creator WHERE gang = ?", name);
        databaseManager.executeAsync("DELETE FROM gang WHERE gang = ?", name);
    }
}
