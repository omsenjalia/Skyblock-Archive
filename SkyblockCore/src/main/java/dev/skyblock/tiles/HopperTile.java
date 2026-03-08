package dev.skyblock.tiles;

import dev.skyblock.SkyblockCore;
import dev.skyblock.island.Island;
import org.bukkit.Location;
import org.bukkit.Material;
import org.bukkit.block.Block;
import org.bukkit.block.Chest;
import org.bukkit.inventory.Inventory;
import org.bukkit.inventory.ItemStack;

public class HopperTile extends BaseTile {
    private static final int DELAY_TICKS = 8; // standard hopper speed
    private int tickCounter = DELAY_TICKS;

    public HopperTile(Location location) {
        super(location);
    }

    @Override
    public void onUpdate() {
        if (tickCounter > 0) { tickCounter--; return; }
        tickCounter = DELAY_TICKS;

        SkyblockCore plugin = SkyblockCore.getInstance();
        Island island = plugin.getIslandManager()
            .getOnlineIslandByWorld(location.getWorld().getName()).orElse(null);
        if (island == null) return;

        // Check hopper upgrade limit
        if (island.getHopperUpgrade() <= 0) return;

        // Pull items from block above into chest below
        Block above = location.getBlock().getRelative(0, 1, 0);
        Block below = location.getBlock().getRelative(0, -1, 0);

        if (!(above.getState() instanceof Chest sourceChest)) return;
        if (!(below.getState() instanceof Chest destChest)) return;

        Inventory src = sourceChest.getInventory();
        Inventory dst = destChest.getInventory();
        if (dst.firstEmpty() == -1) return;

        for (ItemStack item : src.getContents()) {
            if (item != null && item.getType() != Material.AIR) {
                ItemStack transfer = item.clone();
                transfer.setAmount(1);
                dst.addItem(transfer);
                item.setAmount(item.getAmount() - 1);
                break;
            }
        }
    }
}
