package dev.skyblock.enchants.sword;
import dev.skyblock.enchants.BaseMeleeEnchant;
import org.bukkit.attribute.Attribute;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
public class Vampire extends BaseMeleeEnchant {
    public static final int ID = 109;
    public Vampire() { super(ID, "Vampire", 15); }
    @Override
    public boolean isApplicableTo(Player holder, int level) { return java.util.concurrent.ThreadLocalRandom.current().nextInt(1, 26) == 1; }
    @Override
    public void onActivation(Player victim, Player attacker, EntityDamageByEntityEvent ev, int level) {
        attacker.sendMessage("§bVampire §aActivated!");
        double heal = (ev.getFinalDamage() / 2.0) * ((level - 1) / 100.0);
        double maxHealth = attacker.getAttribute(Attribute.MAX_HEALTH).getValue();
        attacker.setHealth(Math.min(maxHealth, attacker.getHealth() + heal));
    }
}
