package dev.skyblock.tiles;

import dev.skyblock.SkyblockCore;
import dev.skyblock.island.Island;
import dev.skyblock.user.User;
import dev.skyblock.util.SellPrices;
import org.bukkit.Location;
import org.bukkit.Material;
import org.bukkit.block.Block;
import org.bukkit.block.Chest;
import org.bukkit.entity.Player;
import org.bukkit.inventory.Inventory;
import org.bukkit.inventory.ItemStack;

public class AutoSellerTile extends BaseTile {
    public static final int MAX_LEVEL = 8;
    public static final int TYPE_MONEY = 0;
    public static final int TYPE_XP = 1;

    private int tickCounter = 0;
    private final TileData data;

    public AutoSellerTile(Location location, TileData data) {
        super(location);
        this.data = data;
        this.tickCounter = getDelayTicks(data.getLevel());
    }

    @Override
    public void onUpdate() {
        if (tickCounter > 0) {
            tickCounter--;
            return;
        }
        tickCounter = getDelayTicks(data.getLevel());

        SkyblockCore plugin = SkyblockCore.getInstance();
        String worldName = location.getWorld().getName();

        Island island = plugin.getIslandManager().getOnlineIslandByWorld(worldName).orElse(null);
        if (island == null) return;

        // Get chest directly below this block
        Block below = location.getBlock().getRelative(0, -1, 0);
        if (!(below.getState() instanceof Chest chestState)) return;

        Inventory inv = chestState.getInventory();

        // Find receiver — island owner if online, otherwise a co-owner
        String receiverName = island.getReceiver();
        Player receiver = plugin.getServer().getPlayerExact(receiverName);
        if (receiver == null) {
            receiver = island.getRandomOnlineCoOwner();
            if (receiver == null) return;
            receiverName = receiver.getName();
        }

        User user = plugin.getUserManager().getOnlineUser(receiver.getUniqueId());
        if (user == null || !island.isAnOwner(receiverName)) return;

        // Sell each item in chest
        for (ItemStack item : inv.getContents()) {
            if (item == null || item.getType() == Material.AIR) continue;
            double price = SellPrices.getPrice(item.getType());
            if (price <= 0) continue;

            // Amount sold per cycle = item count * level multiplier
            int sellCount = Math.min(item.getAmount(), data.getLevel());
            double earnings = price * sellCount;

            if (data.getType() == TYPE_MONEY) {
                user.addMoney(earnings);
            } else {
                // XP type — add xp to user
                user.addXp((int) earnings);
            }

            String type = data.getType() == TYPE_MONEY ? "$" : "XP";
            receiver.sendActionBar(net.kyori.adventure.text.Component.text(
                "§l§6>> §eAuto Sold for §6" + dev.skyblock.util.Format.formatMoney(earnings) + type + " §6<<"
            ));

            // Remove sold items
            item.setAmount(item.getAmount() - sellCount);
            if (item.getAmount() <= 0) {
                inv.remove(item);
            }

            // Only sell one item type per cycle to match original behaviour
            break;
        }
    }

    // Delay in ticks per level — matches PHP getDelayByLevel exactly
    public static int getDelayTicks(int level) {
        return switch (level) {
            case 1 -> 15 * 20;
            case 2 -> 10 * 20;
            case 3 -> 8 * 20;
            case 4 -> 6 * 20;
            case 5 -> 4 * 20;
            case 6 -> 2 * 20;
            case 7 -> 30;
            case 8 -> 20;
            default -> 200;
        };
    }

    public TileData getData() { return data; }
}
