package dev.skyblock.enchants.armor;

import dev.skyblock.enchants.BaseArmorEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
import java.util.concurrent.ThreadLocalRandom;

public class LifeShield extends BaseArmorEnchant {
    public static final int ID = 186;

    public LifeShield() {
        super(ID, "LifeShield", 5);
    }

    @Override
    public boolean isApplicableTo(Player player, int level) {
        return ThreadLocalRandom.current().nextInt(1, 26) == 1;
    }

    @Override
    public void onActivation(Player player, EntityDamageByEntityEvent ev, int level) {
        player.sendMessage("§bLifeShield §aActivated!");
        if (ev.getDamager() instanceof Player attacker) {
            attacker.sendMessage("§cStruck by §bLifeShield §cEnchant!");
        }
        // Reduce damage by a factor based on level
        double reduction = Math.min(0.5, level * 0.05);
        ev.setDamage(ev.getDamage() * (1.0 - reduction));
    }
}
