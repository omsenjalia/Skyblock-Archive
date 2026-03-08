package dev.skyblock.db;

import dev.skyblock.SkyblockCore;
import dev.skyblock.island.Island;

import java.util.Map;

public class IslandRepository {
    private final DatabaseManager databaseManager;

    public IslandRepository(SkyblockCore plugin) {
        this.databaseManager = plugin.getDatabaseManager();
    }

    public void saveIsland(Island island) {
        saveIslandInternal(island, false);
    }

    public void saveIslandSync(Island island) {
        saveIslandInternal(island, true);
    }

    private void saveIslandInternal(Island island, boolean sync) {
        String name = island.getName();
        String owner = island.getOwner();
        String helpers = String.join(",", island.getHelpers());
        String admins = String.join(",", island.getAdmins());
        String coowners = String.join(",", island.getCoowners());
        String bans = String.join(",", island.getBans());

        execute(sync, "INSERT OR REPLACE INTO island (name, world) VALUES (?, ?)", name, island.getId());
        execute(sync, "INSERT OR REPLACE INTO info (name, owner, helpers, admins, coowners, receiver, spawner, oregen, autominer, autoseller, hopper, farm, vlimit) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
                name, owner, helpers, admins, coowners, island.getReceiver(),
                island.getSpawnerUpgrade(), island.getOregenUpgrade(), island.getAutominerUpgrade(),
                island.getAutosellerUpgrade(), island.getHopperUpgrade(), island.getFarmUpgrade(), island.getVlimitUpgrade());
        execute(sync, "INSERT OR REPLACE INTO info2 (name, creator, bans, mining, farming) VALUES (?,?,?,?,?)",
                name, island.getCreator(), bans, island.getMiningUpgrade(), island.getFarmingUpgrade());
        execute(sync, "INSERT OR REPLACE INTO lock (name, locked) VALUES (?, ?)", name, island.getLocked());
        execute(sync, "INSERT OR REPLACE INTO level (name, points, level) VALUES (?, ?, ?)", name, island.getPoints(), island.getLevel());
        execute(sync, "INSERT OR REPLACE INTO bank (name, money) VALUES (?, ?)", name, island.getMoney());
        execute(sync, "INSERT OR REPLACE INTO expansion (name, radius) VALUES (?, ?)", name, island.getRadius());
        execute(sync, "INSERT OR REPLACE INTO motd (name, motd) VALUES (?, ?)", name, island.getMotd());

        // Save homes
        execute(sync, "DELETE FROM home WHERE name = ?", name);
        for (Map.Entry<String, double[]> entry : island.getHomes().entrySet()) {
            double[] c = entry.getValue();
            execute(sync, "INSERT INTO home (name, x, y, z, home, world) VALUES (?,?,?,?,?,?)",
                    name, c[0], c[1], c[2], entry.getKey(), island.getId());
        }

        // Save ore data
        String[] keys = {"coal","copper","iron","lapis","gold","diamond","emerald","quartz","netherite",
                         "deep_coal","deep_copper","deep_iron","deep_lapis","deep_gold","deep_diamond","deep_emerald","deep_quartz","deep_netherite"};
        Object[] oreParams = new Object[keys.length + 1];
        oreParams[0] = name;
        for (int i = 0; i < keys.length; i++) oreParams[i+1] = island.getOredata().getOrDefault(keys[i], 0);
        execute(sync, "INSERT OR REPLACE INTO info8 (name, coal, copper, iron, lapis, gold, diamond, emerald, quartz, netherite, deep_coal, deep_copper, deep_iron, deep_lapis, deep_gold, deep_diamond, deep_emerald, deep_quartz, deep_netherite) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", oreParams);

        // Save ore prefs
        Object[] prefParams = new Object[keys.length + 2];
        prefParams[0] = name;
        prefParams[1] = island.getOredatapref().getOrDefault("cobblestone", 0);
        for (int i = 0; i < keys.length; i++) prefParams[i+2] = island.getOredatapref().getOrDefault(keys[i], 0);
        execute(sync, "INSERT OR REPLACE INTO info8pref (name, cobblestone, coal, copper, iron, lapis, gold, diamond, emerald, quartz, netherite, deep_coal, deep_copper, deep_iron, deep_lapis, deep_gold, deep_diamond, deep_emerald, deep_quartz, deep_netherite) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", prefParams);
    }

    private void execute(boolean sync, String sql, Object... params) {
        if (sync) databaseManager.executeSync(sql, params);
        else databaseManager.executeAsync(sql, params);
    }
}
