package dev.skyblock.enchants.block;
import dev.skyblock.enchants.BaseBlockEnchant;
import org.bukkit.Material;
import org.bukkit.entity.Player;
import org.bukkit.event.block.BlockBreakEvent;
import org.bukkit.inventory.ItemStack;
import java.util.ArrayList;
import java.util.List;
public class Smelting extends BaseBlockEnchant {
    public static final int ID = 115;
    public Smelting() { super(ID, "Smelting", 5); }
    @Override
    public boolean isApplicableTo(Player player, int level) { return player.getInventory().getItemInMainHand().getType().name().contains("PICKAXE"); }
    @Override
    public void onActivation(Player player, BlockBreakEvent ev, int level) {
        List<ItemStack> drops = new ArrayList<>();
        for (ItemStack drop : ev.getBlock().getDrops(player.getInventory().getItemInMainHand())) {
            drops.add(convertItem(drop));
        }
        ev.setDropItems(false);
        for (ItemStack drop : drops) ev.getBlock().getWorld().dropItemNaturally(ev.getBlock().getLocation(), drop);
    }
    private ItemStack convertItem(ItemStack item) {
        Material type = item.getType();
        Material result = match(type);
        if (result != type) { ItemStack ni = item.clone(); ni.setType(result); return ni; }
        return item;
    }
    private Material match(Material t) {
        return switch(t) {
            case RAW_COPPER -> Material.COPPER_INGOT;
            case RAW_IRON -> Material.IRON_INGOT;
            case RAW_GOLD -> Material.GOLD_INGOT;
            case ANCIENT_DEBRIS -> Material.NETHERITE_SCRAP;
            case COBBLESTONE -> Material.STONE;
            default -> t;
        };
    }
}
