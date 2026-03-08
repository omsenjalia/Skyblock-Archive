package dev.skyblock.enchants.armor;

import dev.skyblock.enchants.BaseArmorEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
import org.bukkit.potion.PotionEffect;
import org.bukkit.potion.PotionEffectType;
import java.util.concurrent.ThreadLocalRandom;

public class Poisoned extends BaseArmorEnchant {
    public static final int ID = 125;

    public Poisoned() {
        super(ID, "Poisoned", 15);
    }

    @Override
    public boolean isApplicableTo(Player player, int level) {
        return ThreadLocalRandom.current().nextInt(1, 26) == 1;
    }

    @Override
    public void onActivation(Player victim, EntityDamageByEntityEvent ev, int level) {
        if (ev.getDamager() instanceof Player attacker) {
            attacker.removePotionEffect(PotionEffectType.REGENERATION);
            attacker.removePotionEffect(PotionEffectType.HEALTH_BOOST);
            victim.sendMessage("§bPoisoned §aActivated!");
            attacker.sendMessage("§cStruck by §bPoisoned §cEnchant! Health Effects removed");
            int amplifier = Math.min(5, (int) Math.ceil(level / 3.0));
            attacker.addPotionEffect(new PotionEffect(PotionEffectType.POISON, level * 2 * 20, amplifier));
        }
    }
}
