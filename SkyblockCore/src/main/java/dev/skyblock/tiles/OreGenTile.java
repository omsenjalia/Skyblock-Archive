package dev.skyblock.tiles;

import org.bukkit.Location;
import org.bukkit.Material;
import org.bukkit.block.Block;

public class OreGenTile extends BaseTile {
    private static final int DELAY_TICKS = 200; // 10 seconds
    private int tickCounter = DELAY_TICKS;
    private final Material oreType;

    public OreGenTile(Location location, Material oreType) {
        super(location);
        this.oreType = oreType;
    }

    @Override
    public void onUpdate() {
        if (tickCounter > 0) { tickCounter--; return; }
        tickCounter = DELAY_TICKS;

        Block above = location.getBlock().getRelative(0, 1, 0);
        if (above.getType() == Material.AIR) {
            above.setType(oreType);
        }
    }

    public Material getOreType() { return oreType; }
}
