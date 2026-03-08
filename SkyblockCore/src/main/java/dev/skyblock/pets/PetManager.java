package dev.skyblock.pets;

import dev.skyblock.SkyblockCore;
import org.bukkit.Location;
import org.bukkit.entity.ArmorStand;
import org.bukkit.entity.EntityType;
import org.bukkit.entity.Player;

import java.util.Map;
import java.util.UUID;
import java.util.concurrent.ConcurrentHashMap;

public class PetManager {
    private final SkyblockCore plugin;
    private final Map<UUID, ArmorStand> activePets = new ConcurrentHashMap<>();

    public PetManager(SkyblockCore plugin) {
        this.plugin = plugin;
    }

    public void spawnPet(Player owner, String type) {
        Location loc = owner.getLocation();
        ArmorStand pet = (ArmorStand) loc.getWorld().spawnEntity(loc, EntityType.ARMOR_STAND);
        pet.setVisible(false);
        pet.setSmall(true);
        pet.setCustomName("§6" + owner.getName() + "'s " + type);
        pet.setCustomNameVisible(true);

        activePets.put(owner.getUniqueId(), pet);
    }

    public void removePet(Player owner) {
        ArmorStand pet = activePets.remove(owner.getUniqueId());
        if (pet != null) {
            pet.remove();
        }
    }

    public void shutdown() {
        for (ArmorStand pet : activePets.values()) {
            pet.remove();
        }
        activePets.clear();
    }
}
