package dev.skyblock.gang;

import dev.skyblock.SkyblockCore;
import dev.skyblock.db.GangRepository;
import org.bukkit.entity.Player;

import java.util.Map;
import java.util.concurrent.ConcurrentHashMap;

public class GangManager {
    private final SkyblockCore plugin;
    private final GangRepository repository;
    private final Map<String, Gang> gangs = new ConcurrentHashMap<>();

    public GangManager(SkyblockCore plugin) {
        this.plugin = plugin;
        this.repository = new GangRepository(plugin);
    }

    public void createGang(Player leader, String name) {
        Gang gang = new Gang(name, leader.getName());
        gangs.put(name.toLowerCase(), gang);

        repository.saveGang(gang);
        repository.addGangMember(leader.getName(), name);
    }

    public Gang getGang(String name) {
        return gangs.get(name.toLowerCase());
    }

    public void shutdown() {
        for (Gang gang : gangs.values()) {
            repository.saveGang(gang);
        }
        gangs.clear();
    }
}
