package dev.skyblock.enchants.bow;
import dev.skyblock.enchants.BaseBowEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
public class Launcher extends BaseBowEnchant {
    public static final int ID = 161;
    public Launcher() { super(ID, "Launcher", 5); }
    @Override
    public void onHitPlayer(Player shooter, Player hit, EntityDamageByEntityEvent ev, int level) {

    }
}
