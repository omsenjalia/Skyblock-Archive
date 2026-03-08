package dev.skyblock.enchants.sword;
import dev.skyblock.enchants.BaseMeleeEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
public class DeathBringer extends BaseMeleeEnchant {
    public static final int ID = 102;
    public DeathBringer() { super(ID, "DeathBringer", 15); }
    @Override
    public boolean isApplicableTo(Player holder, int level) { return java.util.concurrent.ThreadLocalRandom.current().nextInt(1, 26) == 1; }
    @Override
    public void onActivation(Player victim, Player attacker, EntityDamageByEntityEvent ev, int level) {
        if (victim.getHealth() < 6) {
            attacker.sendMessage("§bDeathBringer §aActivated!");
            ev.setDamage(ev.getDamage() * 2);
        }
    }
}
