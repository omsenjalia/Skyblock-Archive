package dev.skyblock.db;

import com.zaxxer.hikari.HikariConfig;
import com.zaxxer.hikari.HikariDataSource;
import dev.skyblock.SkyblockCore;
import org.bukkit.Bukkit;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.concurrent.CompletableFuture;
import java.util.function.Consumer;

public class DatabaseManager {
    private final SkyblockCore plugin;
    private HikariDataSource dataSource;

    public DatabaseManager(SkyblockCore plugin) {
        this.plugin = plugin;
        setupPool();
        initTables();
    }

    private void setupPool() {
        HikariConfig config = new HikariConfig();
        String type = plugin.getConfig().getString("database.type", "sqlite");

        if (type.equalsIgnoreCase("mysql")) {
            String host = plugin.getConfig().getString("database.host");
            int port = plugin.getConfig().getInt("database.port");
            String database = plugin.getConfig().getString("database.database");
            String username = plugin.getConfig().getString("database.username");
            String password = plugin.getConfig().getString("database.password");

            config.setJdbcUrl("jdbc:mysql://" + host + ":" + port + "/" + database);
            config.setUsername(username);
            config.setPassword(password);
            config.addDataSourceProperty("cachePrepStmts", "true");
            config.addDataSourceProperty("prepStmtCacheSize", "250");
            config.addDataSourceProperty("prepStmtCacheSqlLimit", "2048");
        } else {
            String fileName = plugin.getConfig().getString("database.file", "skyblock.db");
            config.setJdbcUrl("jdbc:sqlite:" + plugin.getDataFolder() + "/" + fileName);
            config.setDriverClassName("org.sqlite.JDBC");
            config.setPoolName("SkyblockSQLitePool");
        }

        config.setMaximumPoolSize(10);
        dataSource = new HikariDataSource(config);
    }

    private void initTables() {
        executeSync("CREATE TABLE IF NOT EXISTS island (name TEXT PRIMARY KEY, world TEXT)");
        executeSync("CREATE TABLE IF NOT EXISTS bank (name TEXT PRIMARY KEY, money INT)");
        executeSync("CREATE TABLE IF NOT EXISTS expansion (name TEXT PRIMARY KEY, radius INT)");
        executeSync("CREATE TABLE IF NOT EXISTS motd (name TEXT PRIMARY KEY, motd TEXT)");
        executeSync("CREATE TABLE IF NOT EXISTS info (name TEXT PRIMARY KEY, owner TEXT, helpers TEXT, admins TEXT, coowners TEXT, receiver TEXT, perms TEXT, spawner INT, oregen INT, autominer INT, autoseller INT, hopper INT, farm INT, vlimit INT, islanddata TEXT)");
        executeSync("CREATE TABLE IF NOT EXISTS info2 (name TEXT PRIMARY KEY, creator TEXT, bans TEXT, mining INT, farming INT)");
        executeSync("CREATE TABLE IF NOT EXISTS info4 (name TEXT PRIMARY KEY, miners TEXT, farmers TEXT, placers TEXT, builders TEXT, labourers TEXT, butchers TEXT)");
        executeSync("CREATE TABLE IF NOT EXISTS info8 (name TEXT PRIMARY KEY, coal INT, copper INT, iron INT, lapis INT, gold INT, diamond INT, emerald INT, quartz INT, netherite INT, deep_coal INT, deep_copper INT, deep_iron INT, deep_lapis INT, deep_gold INT, deep_diamond INT, deep_emerald INT, deep_quartz INT, deep_netherite INT)");
        executeSync("CREATE TABLE IF NOT EXISTS info8pref (name TEXT PRIMARY KEY, cobblestone INT, coal INT, copper INT, iron INT, lapis INT, gold INT, diamond INT, emerald INT, quartz INT, netherite INT, deep_coal INT, deep_copper INT, deep_iron INT, deep_lapis INT, deep_gold INT, deep_diamond INT, deep_emerald INT, deep_quartz INT, deep_netherite INT)");
        executeSync("CREATE TABLE IF NOT EXISTS lock (name TEXT PRIMARY KEY, locked TEXT)");
        executeSync("CREATE TABLE IF NOT EXISTS level (name TEXT PRIMARY KEY, points INT, level INT)");
        executeSync("CREATE TABLE IF NOT EXISTS home (ID INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, x INT, y INT, z INT, home TEXT)");
        executeSync("CREATE TABLE IF NOT EXISTS helper (player TEXT PRIMARY KEY, count INT)");
        executeSync("CREATE TABLE IF NOT EXISTS player (player TEXT PRIMARY KEY, money REAL, mobcoin INT, xp INT, xpbank INT, mana INT, blocks INT, kills INT, deaths INT, killstreak INT, chips INT, won INT, bounty INT, seltag INT, tags TEXT, wm TEXT, homes TEXT, pref TEXT, extradata TEXT, quests TEXT)");
        executeSync("CREATE TABLE IF NOT EXISTS kit (player TEXT PRIMARY KEY, achilles INT, theo INT, cosmo INT, arcadia INT, artemis INT, calisto INT)");
        executeSync("CREATE TABLE IF NOT EXISTS gang (player TEXT PRIMARY KEY, gang TEXT, kills INT, deaths INT)");
        executeSync("CREATE TABLE IF NOT EXISTS creator (gang TEXT PRIMARY KEY, leader TEXT, level INT, points INT, motd TEXT)");
        executeSync("CREATE TABLE IF NOT EXISTS goals (player TEXT PRIMARY KEY, goal TEXT)");
        executeSync("CREATE TABLE IF NOT EXISTS combat (player TEXT PRIMARY KEY, level INT, exp INT)");
        executeSync("CREATE TABLE IF NOT EXISTS mining (player TEXT PRIMARY KEY, level INT, exp INT)");
        executeSync("CREATE TABLE IF NOT EXISTS farming (player TEXT PRIMARY KEY, level INT, exp INT)");
        executeSync("CREATE TABLE IF NOT EXISTS gambling (player TEXT PRIMARY KEY, level INT, exp INT)");
        executeSync("CREATE TABLE IF NOT EXISTS votes (server TEXT PRIMARY KEY, votes INT)");
        executeSync("CREATE TABLE IF NOT EXISTS timings (player TEXT PRIMARY KEY, seconds INT)");
        executeSync("CREATE TABLE IF NOT EXISTS pets (player TEXT PRIMARY KEY, name TEXT, unlocked TEXT, current TEXT)");
    }

    public void executeSync(String sql, Object... params) {
        try (Connection conn = dataSource.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql)) {
            for (int i = 0; i < params.length; i++) {
                stmt.setObject(i + 1, params[i]);
            }
            stmt.executeUpdate();
        } catch (SQLException e) {
            plugin.getLogger().severe("Error executing sync query: " + sql);
            e.printStackTrace();
        }
    }

    public void executeAsync(String sql, Object... params) {
        Bukkit.getScheduler().runTaskAsynchronously(plugin, () -> executeSync(sql, params));
    }

    public void queryAsync(String sql, Consumer<ResultSet> callback, Object... params) {
        Bukkit.getScheduler().runTaskAsynchronously(plugin, () -> {
            try (Connection conn = dataSource.getConnection();
                 PreparedStatement stmt = conn.prepareStatement(sql)) {
                for (int i = 0; i < params.length; i++) {
                    stmt.setObject(i + 1, params[i]);
                }
                try (ResultSet rs = stmt.executeQuery()) {
                    callback.accept(rs);
                }
            } catch (SQLException e) {
                plugin.getLogger().severe("Error executing async query: " + sql);
                e.printStackTrace();
            }
        });
    }

    public void shutdown() {
        if (dataSource != null) {
            dataSource.close();
        }
    }
}
