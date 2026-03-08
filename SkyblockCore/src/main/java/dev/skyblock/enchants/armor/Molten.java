package dev.skyblock.enchants.armor;

import dev.skyblock.enchants.BaseArmorEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
import java.util.concurrent.ThreadLocalRandom;

public class Molten extends BaseArmorEnchant {
    public static final int ID = 123;

    public Molten() {
        super(ID, "Molten", 15);
    }

    @Override
    public boolean isApplicableTo(Player player, int level) {
        return ThreadLocalRandom.current().nextInt(1, 26) == 1;
    }

    @Override
    public void onActivation(Player victim, EntityDamageByEntityEvent ev, int level) {
        if (ev.getDamager() instanceof Player attacker) {
            victim.sendMessage("§bMolten §aActivated!");
            attacker.sendMessage("§cStruck by §bMolten §cEnchant!");
            attacker.setFireTicks(level * 20);
        }
    }
}
