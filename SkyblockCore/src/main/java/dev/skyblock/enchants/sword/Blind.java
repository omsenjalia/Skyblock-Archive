package dev.skyblock.enchants.sword;

import dev.skyblock.enchants.BaseMeleeEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
import org.bukkit.potion.PotionEffect;
import org.bukkit.potion.PotionEffectType;
import java.util.concurrent.ThreadLocalRandom;

public class Blind extends BaseMeleeEnchant {
    public static final int ID = 101;

    public Blind() {
        super(ID, "Blind", 5);
    }

    @Override
    public boolean isApplicableTo(Player holder, int level) {
        return ThreadLocalRandom.current().nextInt(1, 26) == 1;
    }

    @Override
    public void onActivation(Player victim, Player attacker, EntityDamageByEntityEvent ev, int level) {

    }
}
