package dev.skyblock.enchants.sword;
import dev.skyblock.enchants.BaseMeleeEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
public class OverPower extends BaseMeleeEnchant {
    public static final int ID = 124;
    public OverPower() { super(ID, "OverPower", 15); }
    @Override
    public boolean isApplicableTo(Player holder, int level) { return java.util.concurrent.ThreadLocalRandom.current().nextInt(1, 26) == 1; }
    @Override
    public void onActivation(Player victim, Player attacker, EntityDamageByEntityEvent ev, int level) {
        attacker.sendMessage("§bOverPower §aActivated!");
        ev.setDamage(ev.getDamage() * (1.1 + (level * 0.1)));
    }
}
