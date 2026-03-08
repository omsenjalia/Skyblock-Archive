package dev.skyblock.enchants;

import org.bukkit.NamespacedKey;
import org.bukkit.entity.Player;
import org.bukkit.inventory.ItemStack;
import org.bukkit.inventory.meta.ItemMeta;
import org.bukkit.persistence.PersistentDataContainer;
import org.bukkit.persistence.PersistentDataType;
import dev.skyblock.SkyblockCore;
import net.kyori.adventure.text.Component;
import net.kyori.adventure.text.format.NamedTextColor;

import java.util.ArrayList;
import java.util.List;

public abstract class BaseEnchant {
    private final int id;
    private final String name;
    private final int maxLevel;

    public BaseEnchant(int id, String name, int maxLevel) {
        this.id = id;
        this.name = name;
        this.maxLevel = maxLevel;
    }

    public int getId() { return id; }
    public String getName() { return name; }
    public int getMaxLevel() { return maxLevel; }

    public abstract boolean isApplicableTo(ItemStack item);

    public int getLevel(ItemStack item) {
        if (item == null || !item.hasItemMeta()) return 0;
        PersistentDataContainer pdc = item.getItemMeta().getPersistentDataContainer();
        NamespacedKey key = new NamespacedKey(SkyblockCore.getInstance(), "enchant_" + id);
        return pdc.getOrDefault(key, PersistentDataType.INTEGER, 0);
    }

    public ItemStack applyToItem(ItemStack item, int level) {
        ItemMeta meta = item.getItemMeta();
        PersistentDataContainer pdc = meta.getPersistentDataContainer();
        NamespacedKey key = new NamespacedKey(SkyblockCore.getInstance(), "enchant_" + id);
        pdc.set(key, PersistentDataType.INTEGER, level);

        List<Component> lore = meta.lore();
        if (lore == null) lore = new ArrayList<>();

        // Remove old lore for this enchant if exists and add new
        lore.removeIf(component -> component.toString().contains(name));
        lore.add(Component.text(name + " " + level, NamedTextColor.GRAY));

        meta.lore(lore);
        item.setItemMeta(meta);
        return item;
    }

    protected void sendActivation(Player player, String message) {
        player.sendMessage(message);
    }
}
