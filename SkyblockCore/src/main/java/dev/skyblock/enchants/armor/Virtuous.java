package dev.skyblock.enchants.armor;

import dev.skyblock.enchants.BaseArmorEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
import org.bukkit.potion.PotionEffect;
import java.util.concurrent.ThreadLocalRandom;

public class Virtuous extends BaseArmorEnchant {
    public static final int ID = 133;

    public Virtuous() {
        super(ID, "Virtuous", 15);
    }

    @Override
    public boolean isApplicableTo(Player player, int level) {
        return ThreadLocalRandom.current().nextInt(1, 26) == 1;
    }

    @Override
    public void onActivation(Player victim, EntityDamageByEntityEvent ev, int level) {
        victim.sendMessage("§bVirtuous §aActivated!");
        int i = 0;
        int limit = (int) Math.ceil(Math.ceil(level / 2.0) / 2.0);
        for (PotionEffect effect : victim.getActivePotionEffects()) {
            if (i >= limit) break;
            // Best effort to determine if bad, many are obviously bad
            if (isBadEffect(effect)) {
                victim.removePotionEffect(effect.getType());
                i++;
            }
        }
    }

    private boolean isBadEffect(PotionEffect effect) {
        String name = effect.getType().getName();
        return name.contains("POISON") || name.contains("WITHER") || name.contains("SLOW") || name.contains("BLIND") || name.contains("CONFUSION") || name.contains("WEAKNESS") || name.contains("HUNGER");
    }
}
