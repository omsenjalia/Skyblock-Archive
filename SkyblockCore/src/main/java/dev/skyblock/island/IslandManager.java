package dev.skyblock.island;

import dev.skyblock.SkyblockCore;
import dev.skyblock.db.IslandRepository;
import org.bukkit.Bukkit;
import org.bukkit.Location;
import org.bukkit.World;
import org.bukkit.WorldCreator;
import org.bukkit.entity.Player;

import java.io.File;
import java.util.*;
import java.util.concurrent.ConcurrentHashMap;

public class IslandManager {
    private final SkyblockCore plugin;
    private final IslandRepository repository;
    private final Map<String, Island> islandsByWorld = new ConcurrentHashMap<>();
    private final Map<String, String> islandNameToWorld = new ConcurrentHashMap<>();

    public IslandManager(SkyblockCore plugin) {
        this.plugin = plugin;
        this.repository = new IslandRepository(plugin);
    }

    public Island createIsland(Player owner, String name) {
        String worldName = "island_" + UUID.randomUUID().toString();
        Island island = new Island(worldName, name, owner.getName());

        islandsByWorld.put(worldName, island);
        islandNameToWorld.put(name.toLowerCase(), worldName);

        // Generate the world
        WorldCreator creator = new WorldCreator(worldName);
        creator.generator(new VoidChunkGenerator());
        World world = creator.createWorld();

        // Place starting platform
        SchematicPlacer.placeStartingPlatform(world.getSpawnLocation());

        // After SchematicPlacer.placeStartingPlatform(world.getSpawnLocation());
        island.setHome("default", world.getSpawnLocation().add(0, 1, 0));

        // After island object is created, before saving:
        Map<String, Integer> defaultPrefs = new HashMap<>();
        defaultPrefs.put("cobblestone", 70);
        defaultPrefs.put("coal", 15);
        defaultPrefs.put("iron", 10);
        defaultPrefs.put("gold", 3);
        defaultPrefs.put("diamond", 2);
        island.getOredatapref().putAll(defaultPrefs);

        Map<String, Integer> defaultOredata = new HashMap<>();
        defaultOredata.put("coal", 0);
        defaultOredata.put("copper", 0);
        defaultOredata.put("iron", 0);
        defaultOredata.put("gold", 0);
        defaultOredata.put("diamond", 0);
        defaultOredata.put("emerald", 0);
        island.getOredata().putAll(defaultOredata);

        // Save to DB
        repository.saveIsland(island);

        return island;
    }

    public Optional<Island> getIslandByName(String name) {
        String worldName = islandNameToWorld.get(name.toLowerCase());
        if (worldName == null) return Optional.empty();
        return Optional.ofNullable(islandsByWorld.get(worldName));
    }

    public Optional<Island> getOnlineIslandByWorld(String worldName) {
        return Optional.ofNullable(islandsByWorld.get(worldName));
    }

    public void loadAllIslands() {
        plugin.getDatabaseManager().queryAsync(
                "SELECT i.name, i.world, " +
                        "inf.owner, inf.helpers, inf.admins, inf.coowners, inf.receiver, " +
                        "inf.spawner, inf.oregen, inf.autominer, inf.autoseller, inf.hopper, inf.farm, inf.vlimit, " +
                        "inf2.creator, inf2.bans, inf2.mining, inf2.farming, " +
                        "lk.locked, lv.points, lv.level, " +
                        "b.money, e.radius, m.motd " +
                        "FROM island i " +
                        "LEFT JOIN info inf ON i.name = inf.name " +
                        "LEFT JOIN info2 inf2 ON i.name = inf2.name " +
                        "LEFT JOIN lock lk ON i.name = lk.name " +
                        "LEFT JOIN level lv ON i.name = lv.name " +
                        "LEFT JOIN bank b ON i.name = b.name " +
                        "LEFT JOIN expansion e ON i.name = e.name " +
                        "LEFT JOIN motd m ON i.name = m.name",
                rs -> {
                    try {
                        List<Object[]> rows = new ArrayList<>();
                        while (rs.next()) {
                            rows.add(new Object[]{
                                    rs.getString("name"), rs.getString("world"),
                                    rs.getString("owner"), rs.getString("helpers"),
                                    rs.getString("admins"), rs.getString("coowners"),
                                    rs.getString("receiver"), rs.getString("creator"),
                                    rs.getString("bans"), rs.getString("locked"),
                                    rs.getInt("points"), rs.getInt("level"),
                                    rs.getDouble("money"), rs.getInt("radius"),
                                    rs.getString("motd"),
                                    rs.getInt("spawner"), rs.getInt("oregen"),
                                    rs.getInt("autominer"), rs.getInt("autoseller"),
                                    rs.getInt("hopper"), rs.getInt("farm"), rs.getInt("vlimit"),
                                    rs.getInt("mining"), rs.getInt("farming")
                            });
                        }
                        Bukkit.getScheduler().runTask(plugin, () -> {
                            for (Object[] row : rows) {
                                String islandName = (String) row[0];
                                String worldName  = (String) row[1];
                                String owner      = row[2] != null ? (String) row[2] : "";
                                String creator    = row[7] != null ? (String) row[7] : owner;

                                Island island = new Island(worldName, islandName, owner);
                                island.setCreator(creator);

                                // Parse comma-separated lists
                                if (row[3] != null) parseList((String) row[3]).forEach(island.getHelpers()::add);
                                if (row[4] != null) parseList((String) row[4]).forEach(island.getAdmins()::add);
                                if (row[5] != null) parseList((String) row[5]).forEach(island.getCoowners()::add);
                                if (row[8] != null) parseList((String) row[8]).forEach(island.getBans()::add);

                                if (row[6] != null) island.setReceiver((String) row[6]);
                                if (row[9] != null) island.setLocked((String) row[9]);
                                island.setPoints((int) row[10]);
                                island.setLevel((int) row[11]);
                                island.setMoney((double) row[12]);
                                island.setRadius((int) row[13]);
                                if (row[14] != null) island.setMotd((String) row[14]);
                                island.setSpawnerUpgrade((int) row[15]);
                                island.setOregenUpgrade((int) row[16]);
                                island.setAutominerUpgrade((int) row[17]);
                                island.setAutosellerUpgrade((int) row[18]);
                                island.setHopperUpgrade((int) row[19]);
                                island.setFarmUpgrade((int) row[20]);
                                island.setVlimitUpgrade((int) row[21]);
                                island.setMiningUpgrade((int) row[22]);
                                island.setFarmingUpgrade((int) row[23]);

                                // Load world
                                org.bukkit.World world = Bukkit.getWorld(worldName);
                                if (world == null) {
                                    File worldFolder = new File(Bukkit.getWorldContainer(), worldName);
                                    if (worldFolder.exists()) {
                                        WorldCreator creator2 = new WorldCreator(worldName);
                                        creator2.generator(new VoidChunkGenerator());
                                        world = creator2.createWorld();
                                    }
                                }

                                islandsByWorld.put(worldName, island);
                                islandNameToWorld.put(islandName.toLowerCase(), worldName);
                            }

                            // Load homes separately
                            loadAllHomes();

                            // Load ore data separately
                            loadAllOreData();

                            plugin.getLogger().info("Loaded " + islandsByWorld.size() + " islands.");
                        });
                    } catch (Exception e) {
                        e.printStackTrace();
                    }
                }
        );
    }

    private List<String> parseList(String csv) {
        if (csv == null || csv.isEmpty()) return new ArrayList<>();
        return Arrays.stream(csv.split(","))
                .map(String::trim)
                .filter(s -> !s.isEmpty())
                .collect(java.util.stream.Collectors.toList());
    }

    private void loadAllHomes() {
        plugin.getDatabaseManager().queryAsync("SELECT name, x, y, z, home, world FROM home", rs -> {
            try {
                List<Object[]> rows = new ArrayList<>();
                while (rs.next()) {
                    rows.add(new Object[]{
                            rs.getString("name"), rs.getDouble("x"),
                            rs.getDouble("y"), rs.getDouble("z"),
                            rs.getString("home")
                    });
                }
                Bukkit.getScheduler().runTask(plugin, () -> {
                    for (Object[] row : rows) {
                        Island island = islandsByWorld.values().stream()
                                .filter(i -> i.getName().equals(row[0]))
                                .findFirst().orElse(null);
                        if (island == null) continue;
                        org.bukkit.World world = Bukkit.getWorld(island.getId());
                        if (world == null) continue;
                        Location loc = new Location(world, (double)row[1], (double)row[2], (double)row[3]);
                        island.setHome((String) row[4], loc);
                    }
                });
            } catch (Exception e) { e.printStackTrace(); }
        });
    }

    private void loadAllOreData() {
        plugin.getDatabaseManager().queryAsync(
                "SELECT name, coal, copper, iron, lapis, gold, diamond, emerald, quartz, netherite, " +
                        "deep_coal, deep_copper, deep_iron, deep_lapis, deep_gold, deep_diamond, deep_emerald, deep_quartz, deep_netherite FROM info8",
                rs -> {
                    try {
                        List<Object[]> rows = new ArrayList<>();
                        while (rs.next()) {
                            rows.add(new Object[]{
                                    rs.getString("name"),
                                    rs.getInt("coal"), rs.getInt("copper"), rs.getInt("iron"),
                                    rs.getInt("lapis"), rs.getInt("gold"), rs.getInt("diamond"),
                                    rs.getInt("emerald"), rs.getInt("quartz"), rs.getInt("netherite"),
                                    rs.getInt("deep_coal"), rs.getInt("deep_copper"), rs.getInt("deep_iron"),
                                    rs.getInt("deep_lapis"), rs.getInt("deep_gold"), rs.getInt("deep_diamond"),
                                    rs.getInt("deep_emerald"), rs.getInt("deep_quartz"), rs.getInt("deep_netherite")
                            });
                        }
                        Bukkit.getScheduler().runTask(plugin, () -> {
                            String[] keys = {"coal","copper","iron","lapis","gold","diamond","emerald",
                                    "quartz","netherite","deep_coal","deep_copper","deep_iron",
                                    "deep_lapis","deep_gold","deep_diamond","deep_emerald",
                                    "deep_quartz","deep_netherite"};
                            for (Object[] row : rows) {
                                Island island = islandsByWorld.values().stream()
                                        .filter(i -> i.getName().equals(row[0]))
                                        .findFirst().orElse(null);
                                if (island == null) continue;
                                for (int i = 0; i < keys.length; i++) {
                                    island.getOredata().put(keys[i], (int) row[i + 1]);
                                }
                            }
                        });
                    } catch (Exception e) { e.printStackTrace(); }
                }
        );

        // Load oredatapref from info8pref
        plugin.getDatabaseManager().queryAsync(
                "SELECT name, cobblestone, coal, copper, iron, lapis, gold, diamond, emerald, quartz, netherite, " +
                        "deep_coal, deep_copper, deep_iron, deep_lapis, deep_gold, deep_diamond, deep_emerald, deep_quartz, deep_netherite FROM info8pref",
                rs -> {
                    try {
                        List<Object[]> rows = new ArrayList<>();
                        while (rs.next()) {
                            rows.add(new Object[]{
                                    rs.getString("name"),
                                    rs.getInt("cobblestone"),
                                    rs.getInt("coal"), rs.getInt("copper"), rs.getInt("iron"),
                                    rs.getInt("lapis"), rs.getInt("gold"), rs.getInt("diamond"),
                                    rs.getInt("emerald"), rs.getInt("quartz"), rs.getInt("netherite"),
                                    rs.getInt("deep_coal"), rs.getInt("deep_copper"), rs.getInt("deep_iron"),
                                    rs.getInt("deep_lapis"), rs.getInt("deep_gold"), rs.getInt("deep_diamond"),
                                    rs.getInt("deep_emerald"), rs.getInt("deep_quartz"), rs.getInt("deep_netherite")
                            });
                        }
                        Bukkit.getScheduler().runTask(plugin, () -> {
                            String[] keys = {"cobblestone","coal","copper","iron","lapis","gold","diamond",
                                    "emerald","quartz","netherite","deep_coal","deep_copper",
                                    "deep_iron","deep_lapis","deep_gold","deep_diamond",
                                    "deep_emerald","deep_quartz","deep_netherite"};
                            for (Object[] row : rows) {
                                Island island = islandsByWorld.values().stream()
                                        .filter(i -> i.getName().equals(row[0]))
                                        .findFirst().orElse(null);
                                if (island == null) continue;
                                for (int i = 0; i < keys.length; i++) {
                                    int val = (int) row[i + 1];
                                    if (val > 0) island.getOredatapref().put(keys[i], val);
                                }
                                // If no prefs saved yet, set defaults
                                if (island.getOredatapref().isEmpty()) {
                                    island.getOredatapref().put("cobblestone", 70);
                                    island.getOredatapref().put("coal", 15);
                                    island.getOredatapref().put("iron", 10);
                                    island.getOredatapref().put("gold", 3);
                                    island.getOredatapref().put("diamond", 2);
                                }
                            }
                        });
                    } catch (Exception e) { e.printStackTrace(); }
                }
        );
    }

    public void shutdown() {
        for (Island island : islandsByWorld.values()) {
            repository.saveIslandSync(island);
        }
    }

}
