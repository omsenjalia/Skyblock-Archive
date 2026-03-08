package dev.skyblock.enchants.sword;
import dev.skyblock.enchants.BaseMeleeEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
public class Thunderbolt extends BaseMeleeEnchant {
    public static final int ID = 179;
    public Thunderbolt() { super(ID, "Thunderbolt", 15); }
    @Override
    public boolean isApplicableTo(Player holder, int level) { return java.util.concurrent.ThreadLocalRandom.current().nextInt(1, 26) == 1; }
    @Override
    public void onActivation(Player victim, Player attacker, EntityDamageByEntityEvent ev, int level) {
        attacker.sendMessage("§bThunderbolt §aActivated!");
        victim.getWorld().strikeLightning(victim.getLocation());
        ev.setDamage(ev.getDamage() + level);
    }
}
