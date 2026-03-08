package dev.skyblock.enchants;
import org.bukkit.entity.Player;
import org.bukkit.event.player.PlayerInteractEvent;
public abstract class BaseTouchEnchant extends BaseEnchant {
    public BaseTouchEnchant(int id, String name, int maxLevel) { super(id, name, maxLevel); }
    public abstract void onActivation(Player player, PlayerInteractEvent ev, int level);
}
