package dev.skyblock.enchants;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
public abstract class BaseArmorEnchant extends BaseEnchant {
    public BaseArmorEnchant(int id, String name, int maxLevel) { super(id, name, maxLevel); }
    public abstract void onActivation(Player player, EntityDamageByEntityEvent ev, int level);
    public abstract boolean isApplicableTo(Player player, int level);
}
