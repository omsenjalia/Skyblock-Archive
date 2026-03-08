package dev.skyblock.enchants.armor;
import dev.skyblock.enchants.BaseArmorEnchant;
import org.bukkit.attribute.Attribute;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
public class Enlighten extends BaseArmorEnchant {
    public static final int ID = 124;
    public Enlighten() { super(ID, "Enlighten", 15); }
    @Override
    public boolean isApplicableTo(Player player, int level) { return java.util.concurrent.ThreadLocalRandom.current().nextInt(1, 26) == 1; }
    @Override
    public void onActivation(Player victim, EntityDamageByEntityEvent ev, int level) {
        victim.sendMessage("§bEnlighten §aActivated!");
        double heal = level / 40.0;
        double maxHealth = victim.getAttribute(Attribute.MAX_HEALTH).getValue();
        victim.setHealth(Math.min(maxHealth, victim.getHealth() + heal));
    }
}
