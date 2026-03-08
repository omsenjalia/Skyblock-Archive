package dev.skyblock.user;

import dev.skyblock.SkyblockCore;
import dev.skyblock.db.UserRepository;
import org.bukkit.entity.Player;

import java.util.Map;
import java.util.UUID;
import java.util.concurrent.ConcurrentHashMap;

public class UserManager {
    private final SkyblockCore plugin;
    private final UserRepository repository;
    private final Map<UUID, User> users = new ConcurrentHashMap<>();

    public UserManager(SkyblockCore plugin) {
        this.plugin = plugin;
        this.repository = new UserRepository(plugin);
    }

    public void loadUser(Player player) {
        UUID uuid = player.getUniqueId();
        String name = player.getName();

        repository.loadUser(name, user -> {
            if (user != null) {
                users.put(uuid, user);
            } else {
                // Create new user
                User newUser = new User(uuid, name);
                newUser.setMoney(3000);
                newUser.setMana(50);
                users.put(uuid, newUser);
                saveUser(uuid);
            }
        });
    }

    public void saveUser(UUID uuid) {
        User user = users.get(uuid);
        if (user == null) return;
        repository.saveUser(user);
    }

    public User getOnlineUser(UUID uuid) {
        return users.get(uuid);
    }

    public void unloadUser(UUID uuid) {
        saveUser(uuid);
        users.remove(uuid);
    }

    public void shutdown() {
        for (UUID uuid : users.keySet()) {
            saveUser(uuid);
        }
        users.clear();
    }
}
