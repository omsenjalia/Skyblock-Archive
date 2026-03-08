package dev.skyblock.enchants;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
public abstract class BaseMeleeEnchant extends BaseEnchant {
    public BaseMeleeEnchant(int id, String name, int maxLevel) { super(id, name, maxLevel); }
    public abstract void onActivation(Player victim, Player attacker, EntityDamageByEntityEvent ev, int level);
    public abstract boolean isApplicableTo(Player holder, int level);
}
