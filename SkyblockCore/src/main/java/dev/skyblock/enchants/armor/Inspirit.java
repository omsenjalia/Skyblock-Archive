package dev.skyblock.enchants.armor;

import dev.skyblock.enchants.BaseArmorEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
import org.bukkit.potion.PotionEffect;
import org.bukkit.potion.PotionEffectType;
import java.util.concurrent.ThreadLocalRandom;

public class Inspirit extends BaseArmorEnchant {
    public static final int ID = 191;

    public Inspirit() {
        super(ID, "Inspirit", 5);
    }

    @Override
    public boolean isApplicableTo(Player player, int level) {
        return ThreadLocalRandom.current().nextInt(1, 26) == 1;
    }

    @Override
    public void onActivation(Player player, EntityDamageByEntityEvent ev, int level) {

    }
}
