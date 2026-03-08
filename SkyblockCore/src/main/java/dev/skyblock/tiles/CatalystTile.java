package dev.skyblock.tiles;

import dev.skyblock.SkyblockCore;
import dev.skyblock.island.Island;
import org.bukkit.Location;
import org.bukkit.Material;
import org.bukkit.block.Block;
import java.util.Map;
import java.util.concurrent.ThreadLocalRandom;

public class CatalystTile extends BaseTile {
    private static final int DELAY_TICKS = 200; // 10 seconds
    private int tickCounter = DELAY_TICKS;

    // Maps ore key names to Material — matches PHP source Catalyst ore list exactly
    private static final Map<String, Material> ORE_MAP = Map.ofEntries(
        Map.entry("coal",           Material.COAL_ORE),
        Map.entry("copper",         Material.COPPER_ORE),
        Map.entry("iron",           Material.IRON_ORE),
        Map.entry("lapis",          Material.LAPIS_ORE),
        Map.entry("gold",           Material.GOLD_ORE),
        Map.entry("diamond",        Material.DIAMOND_ORE),
        Map.entry("emerald",        Material.EMERALD_ORE),
        Map.entry("quartz",         Material.NETHER_QUARTZ_ORE),
        Map.entry("netherite",      Material.ANCIENT_DEBRIS),
        Map.entry("deep_coal",      Material.DEEPSLATE_COAL_ORE),
        Map.entry("deep_copper",    Material.DEEPSLATE_COPPER_ORE),
        Map.entry("deep_iron",      Material.DEEPSLATE_IRON_ORE),
        Map.entry("deep_lapis",     Material.DEEPSLATE_LAPIS_ORE),
        Map.entry("deep_gold",      Material.DEEPSLATE_GOLD_ORE),
        Map.entry("deep_diamond",   Material.DEEPSLATE_DIAMOND_ORE),
        Map.entry("deep_emerald",   Material.DEEPSLATE_EMERALD_ORE),
        Map.entry("deep_quartz",    Material.QUARTZ_BLOCK),
        Map.entry("deep_netherite", Material.NETHERITE_BLOCK)
    );

    public CatalystTile(Location location) {
        super(location);
    }

    @Override
    public void onUpdate() {
        if (tickCounter > 0) {
            tickCounter--;
            return;
        }
        tickCounter = DELAY_TICKS;

        SkyblockCore plugin = SkyblockCore.getInstance();
        String worldName = location.getWorld().getName();

        Island island = plugin.getIslandManager().getOnlineIslandByWorld(worldName).orElse(null);
        if (island == null) return;

        Block above = location.getBlock().getRelative(0, 1, 0);
        if (above.getType() != Material.AIR) return;

        Material oreToPlace = chooseOre(island.getOredatapref());
        above.setType(oreToPlace);
    }

    private Material chooseOre(Map<String, Integer> prefs) {
        if (prefs == null || prefs.isEmpty()) return Material.COBBLESTONE;

        int totalWeight = prefs.values().stream().mapToInt(Integer::intValue).sum();
        if (totalWeight <= 0) return Material.COBBLESTONE;

        int rand = ThreadLocalRandom.current().nextInt(1, totalWeight + 1);
        int cumulative = 0;
        for (Map.Entry<String, Integer> entry : prefs.entrySet()) {
            cumulative += entry.getValue();
            if (rand <= cumulative) {
                return ORE_MAP.getOrDefault(entry.getKey(), Material.COBBLESTONE);
            }
        }
        return Material.COBBLESTONE;
    }
}
