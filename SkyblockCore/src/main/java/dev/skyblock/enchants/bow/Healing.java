package dev.skyblock.enchants.bow;
import dev.skyblock.enchants.BaseBowEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
public class Healing extends BaseBowEnchant {
    public static final int ID = 122;
    public Healing() { super(ID, "Healing", 5); }
    @Override
    public void onHitPlayer(Player shooter, Player hit, EntityDamageByEntityEvent ev, int level) {

    }
}
