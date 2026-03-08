package dev.skyblock.enchants;

import dev.skyblock.SkyblockCore;
import org.bukkit.inventory.ItemStack;
import java.util.HashMap;
import java.util.Map;

public class EnchantManager {
    private final SkyblockCore plugin;
    private final Map<Integer, BaseEnchant> enchants = new HashMap<>();

    public EnchantManager(SkyblockCore plugin) {
        this.plugin = plugin;
        registerAll();
    }

    private void registerAll() {
        // To be implemented: register each enchant class
        // enchants.put(100, new Lifesteal());
    }

    public void shutdown() {
    }

    public int getEnchantLevel(ItemStack item, int id) {
        BaseEnchant enchant = enchants.get(id);
        return enchant != null ? enchant.getLevel(item) : 0;
    }
}
