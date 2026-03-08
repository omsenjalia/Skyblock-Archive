package dev.skyblock.enchants;
import org.bukkit.entity.Player;
import org.bukkit.event.block.BlockBreakEvent;
public abstract class BaseBlockEnchant extends BaseEnchant {
    public BaseBlockEnchant(int id, String name, int maxLevel) { super(id, name, maxLevel); }
    public abstract void onActivation(Player player, BlockBreakEvent ev, int level);
    public abstract boolean isApplicableTo(Player player, int level);
}
