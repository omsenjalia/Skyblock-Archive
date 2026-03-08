package dev.skyblock.enchants.armor;

import dev.skyblock.enchants.BaseArmorEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
import org.bukkit.potion.PotionEffect;
import org.bukkit.potion.PotionEffectType;
import java.util.concurrent.ThreadLocalRandom;

public class Shielded extends BaseArmorEnchant {
    public static final int ID = 127;

    public Shielded() {
        super(ID, "Shielded", 15);
    }

    @Override
    public boolean isApplicableTo(Player player, int level) {
        return ThreadLocalRandom.current().nextInt(1, 26) == 1;
    }

    @Override
    public void onActivation(Player victim, EntityDamageByEntityEvent ev, int level) {
        victim.sendMessage("§bShielded §aActivated!");
        int amplifier = Math.min(8, (int) Math.ceil(level / 2.0));
        victim.addPotionEffect(new PotionEffect(PotionEffectType.RESISTANCE, level * 2 * 20, amplifier));
    }
}
