package dev.skyblock.tiles;

import dev.skyblock.SkyblockCore;
import dev.skyblock.island.Island;
import dev.skyblock.user.User;
import org.bukkit.Location;
import org.bukkit.Material;
import org.bukkit.block.Block;
import org.bukkit.block.Chest;
import org.bukkit.enchantments.Enchantment;
import org.bukkit.entity.Player;
import org.bukkit.inventory.Inventory;
import org.bukkit.inventory.ItemStack;
import java.util.Collection;
import java.util.HashMap;
import java.util.Map;

public class AutoMinerTile extends BaseTile {
    public static final int MAX_LEVEL = 4;
    public static final int MAX_FORTUNE_LEVEL = 15;

    private int tickCounter = 0;
    private final TileData data;

    // Block material → mana value (matches PHP source exactly)
    private static final Map<Material, Integer> MANA_VALUES = new HashMap<>();
    static {
        MANA_VALUES.put(Material.COBBLESTONE, 0);
        MANA_VALUES.put(Material.COAL_ORE, 1);
        MANA_VALUES.put(Material.COPPER_ORE, 2);
        MANA_VALUES.put(Material.IRON_ORE, 3);
        MANA_VALUES.put(Material.LAPIS_ORE, 4);
        MANA_VALUES.put(Material.GOLD_ORE, 5);
        MANA_VALUES.put(Material.DIAMOND_ORE, 6);
        MANA_VALUES.put(Material.EMERALD_ORE, 7);
        MANA_VALUES.put(Material.NETHER_QUARTZ_ORE, 8);
        MANA_VALUES.put(Material.ANCIENT_DEBRIS, 9);
        MANA_VALUES.put(Material.DEEPSLATE_COAL_ORE, 10);
        MANA_VALUES.put(Material.DEEPSLATE_COPPER_ORE, 11);
        MANA_VALUES.put(Material.DEEPSLATE_IRON_ORE, 12);
        MANA_VALUES.put(Material.DEEPSLATE_LAPIS_ORE, 13);
        MANA_VALUES.put(Material.DEEPSLATE_GOLD_ORE, 14);
        MANA_VALUES.put(Material.DEEPSLATE_DIAMOND_ORE, 15);
        MANA_VALUES.put(Material.DEEPSLATE_EMERALD_ORE, 16);
        MANA_VALUES.put(Material.QUARTZ_BLOCK, 17);
        MANA_VALUES.put(Material.NETHERITE_BLOCK, 18);
    }

    public AutoMinerTile(Location location, TileData data) {
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

        // Block to mine = 2 below the AutoMiner
        Block target = location.getBlock().getRelative(0, -2, 0);
        // Chest = 1 above the AutoMiner
        Block aboveBlock = location.getBlock().getRelative(0, 1, 0);

        if (!MANA_VALUES.containsKey(target.getType())) return;
        if (!(aboveBlock.getState() instanceof Chest chestState)) return;

        Inventory inv = chestState.getInventory();
        if (inv.firstEmpty() == -1) return; // chest full, skip

        // Build a pickaxe with fortune if enabled
        ItemStack pickaxe = new ItemStack(Material.DIAMOND_PICKAXE);
        if (data.getFortuneEnabled() == 1) {
            int fl = Math.min(Math.max(data.getFortuneLevel(), 1), MAX_FORTUNE_LEVEL);
            pickaxe.addUnsafeEnchantment(Enchantment.FORTUNE, fl);
        }

        // Get drops and add to chest
        Collection<ItemStack> drops = target.getDrops(pickaxe);
        for (ItemStack drop : drops) {
            inv.addItem(drop);
        }

        // Award mana and island points to receiver
        String receiverName = island.getReceiver();
        Player receiver = plugin.getServer().getPlayerExact(receiverName);
        if (receiver == null) receiver = island.getRandomOnlineCoOwner();
        if (receiver != null) {
            User user = plugin.getUserManager().getOnlineUser(receiver.getUniqueId());
            if (user != null && island.isAnOwner(receiver.getName())) {
                int manaGain = MANA_VALUES.getOrDefault(target.getType(), 0);
                user.addMana(manaGain);
                island.setPoints(island.getPoints() + manaGain);
            }
        }

        // Remove the mined block
        target.setType(Material.AIR);
    }

    public static int getDelayTicks(int level) {
        return switch (level) {
            case 1 -> 15 * 20;
            case 2 -> 10 * 20;
            case 3 -> 8 * 20;
            case 4 -> 4 * 20;
            default -> 200;
        };
    }

    public TileData getData() { return data; }
}
