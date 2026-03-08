package dev.skyblock.enchants.armor;

import dev.skyblock.enchants.BaseArmorEnchant;
import org.bukkit.Material;
import org.bukkit.attribute.Attribute;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
import java.util.concurrent.ThreadLocalRandom;

public class Protector extends BaseArmorEnchant {
    public static final int ID = 146;

    public Protector() {
        super(ID, "Protector", 15);
    }

    @Override
    public boolean isApplicableTo(Player player, int level) {
        return ThreadLocalRandom.current().nextInt(1, 11) == 1;
    }

    @Override
    public void onActivation(Player victim, EntityDamageByEntityEvent ev, int level) {
        if (ev.getDamager() instanceof Player attacker) {
            Material hand = attacker.getInventory().getItemInMainHand().getType();
            if (hand.name().contains("SWORD")) {
                victim.sendMessage("§bProtector §aActivated!");
                attacker.sendMessage("§cStruck by §bProtector §cEnchant!");
                double heal = 0.25 * Math.min(8, Math.ceil(level / 2.0));
                double maxHealth = victim.getAttribute(Attribute.MAX_HEALTH).getValue();
                victim.setHealth(Math.min(maxHealth, victim.getHealth() + heal));
            }
        }
    }
}
