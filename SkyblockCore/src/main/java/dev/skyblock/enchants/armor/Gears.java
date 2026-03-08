package dev.skyblock.enchants.armor;

import dev.skyblock.enchants.BaseArmorEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
import org.bukkit.potion.PotionEffect;
import org.bukkit.potion.PotionEffectType;
import java.util.concurrent.ThreadLocalRandom;

public class Gears extends BaseArmorEnchant {
    public static final int ID = 131;

    public Gears() {
        super(ID, "Gears", 15);
    }

    @Override
    public boolean isApplicableTo(Player player, int level) {
        return true;
    }

    @Override
    public void onActivation(Player victim, EntityDamageByEntityEvent ev, int level) {
        if (!victim.hasPotionEffect(PotionEffectType.SPEED)) {
            int amplifier = ThreadLocalRandom.current().nextInt(0, (int) Math.ceil(level / 4.0));
            victim.addPotionEffect(new PotionEffect(PotionEffectType.SPEED, level * 2 * 20, amplifier));
        }
    }
}
