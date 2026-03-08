package dev.skyblock.enchants;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
public abstract class BaseBowEnchant extends BaseEnchant {
    public BaseBowEnchant(int id, String name, int maxLevel) { super(id, name, maxLevel); }
    public abstract void onHitPlayer(Player shooter, Player hit, EntityDamageByEntityEvent ev, int level);
    public boolean isApplicableTo(Player shooter, int level) { return true; }
}
