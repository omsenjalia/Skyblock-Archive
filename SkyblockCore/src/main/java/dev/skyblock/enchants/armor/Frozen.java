package dev.skyblock.enchants.armor;

import dev.skyblock.enchants.BaseArmorEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
import org.bukkit.potion.PotionEffect;
import org.bukkit.potion.PotionEffectType;
import java.util.concurrent.ThreadLocalRandom;

public class Frozen extends BaseArmorEnchant {
    public static final int ID = 126;

    public Frozen() {
        super(ID, "Frozen", 15);
    }

    @Override
    public boolean isApplicableTo(Player player, int level) {
        return ThreadLocalRandom.current().nextInt(1, 26) == 1;
    }

    @Override
    public void onActivation(Player victim, EntityDamageByEntityEvent ev, int level) {
        if (ev.getDamager() instanceof Player attacker) {
            victim.sendMessage("§bFrozen §aActivated!");
            attacker.sendMessage("§cStruck by §bFrozen §cEnchant!");

            int amplifier = level > 12 ? 2 : (level > 6 ? 1 : 0);
            attacker.addPotionEffect(new PotionEffect(PotionEffectType.SLOWNESS, 20 * 3, amplifier));
            attacker.addPotionEffect(new PotionEffect(PotionEffectType.BLINDNESS, (int) (Math.ceil(level / 2.0) * 20), 1));
        }
    }
}
