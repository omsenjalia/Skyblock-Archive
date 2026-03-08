package dev.skyblock.item;

import dev.skyblock.SkyblockCore;
import org.bukkit.Material;
import org.bukkit.inventory.ItemStack;
import java.util.HashSet;
import java.util.Set;

public class ItemManager {
    private final SkyblockCore plugin;
    private final Set<Material> pickaxes = new HashSet<>();
    private final Set<Material> axes = new HashSet<>();
    private final Set<Material> shovels = new HashSet<>();
    private final Set<Material> swords = new HashSet<>();

    public ItemManager(SkyblockCore plugin) {
        this.plugin = plugin;
        initToolSets();
    }

    private void initToolSets() {
        pickaxes.add(Material.WOODEN_PICKAXE);
        pickaxes.add(Material.STONE_PICKAXE);
        pickaxes.add(Material.IRON_PICKAXE);
        pickaxes.add(Material.GOLDEN_PICKAXE);
        pickaxes.add(Material.DIAMOND_PICKAXE);
        pickaxes.add(Material.NETHERITE_PICKAXE);

        axes.add(Material.WOODEN_AXE);
        axes.add(Material.STONE_AXE);
        axes.add(Material.IRON_AXE);
        axes.add(Material.GOLDEN_AXE);
        axes.add(Material.DIAMOND_AXE);
        axes.add(Material.NETHERITE_AXE);

        shovels.add(Material.WOODEN_SHOVEL);
        shovels.add(Material.STONE_SHOVEL);
        shovels.add(Material.IRON_SHOVEL);
        shovels.add(Material.GOLDEN_SHOVEL);
        shovels.add(Material.DIAMOND_SHOVEL);
        shovels.add(Material.NETHERITE_SHOVEL);

        swords.add(Material.WOODEN_SWORD);
        swords.add(Material.STONE_SWORD);
        swords.add(Material.IRON_SWORD);
        swords.add(Material.GOLDEN_SWORD);
        swords.add(Material.DIAMOND_SWORD);
        swords.add(Material.NETHERITE_SWORD);
    }

    public boolean isPickaxe(ItemStack item) {
        return item != null && pickaxes.contains(item.getType());
    }

    public boolean isAxe(ItemStack item) {
        return item != null && axes.contains(item.getType());
    }

    public boolean isShovel(ItemStack item) {
        return item != null && shovels.contains(item.getType());
    }

    public boolean isSword(ItemStack item) {
        return item != null && swords.contains(item.getType());
    }

    public void shutdown() {
    }
}
