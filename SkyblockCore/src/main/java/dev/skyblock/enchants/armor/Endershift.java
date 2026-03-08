package dev.skyblock.enchants.armor;
import dev.skyblock.enchants.BaseArmorEnchant;
import org.bukkit.attribute.Attribute;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
import org.bukkit.potion.PotionEffect;
import org.bukkit.potion.PotionEffectType;
public class Endershift extends BaseArmorEnchant {
    public static final int ID = 129;
    public Endershift() { super(ID, "Endershift", 15); }
    @Override
    public boolean isApplicableTo(Player player, int level) { return java.util.concurrent.ThreadLocalRandom.current().nextInt(1, 26) == 1; }
    @Override
    public void onActivation(Player victim, EntityDamageByEntityEvent ev, int level) {
        if (victim.getHealth() < (victim.getMaxHealth() / 2.0)) {
            victim.sendMessage("§bEndershift §aActivated!");
            int speedAmp = level > 12 ? 4 : (level > 9 ? 3 : (level > 6 ? 2 : (level > 3 ? 1 : 0)));
            victim.addPotionEffect(new PotionEffect(PotionEffectType.SPEED, level * 20, speedAmp));
            double heal = 0.20 * level;
            double maxHealth = victim.getAttribute(Attribute.MAX_HEALTH).getValue();
            victim.setHealth(Math.min(maxHealth, victim.getHealth() + heal));
        }
    }
}
