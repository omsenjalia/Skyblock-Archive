package dev.skyblock.enchants.bow;
import dev.skyblock.enchants.BaseBowEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
public class Piercing extends BaseBowEnchant {
    public static final int ID = 120;
    public Piercing() { super(ID, "Piercing", 5); }
    @Override
    public void onHitPlayer(Player shooter, Player hit, EntityDamageByEntityEvent ev, int level) {

    }
}
