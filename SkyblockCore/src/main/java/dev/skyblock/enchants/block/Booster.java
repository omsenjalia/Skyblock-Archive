package dev.skyblock.enchants.block;

import dev.skyblock.enchants.BaseBlockEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.block.BlockBreakEvent;
import org.bukkit.potion.PotionEffect;
import org.bukkit.potion.PotionEffectType;
import java.util.concurrent.ThreadLocalRandom;

public class Booster extends BaseBlockEnchant {
    public static final int ID = 139;

    public Booster() {
        super(ID, "Booster", 5);
    }

    @Override
    public boolean isApplicableTo(Player player, int level) {
        return ThreadLocalRandom.current().nextInt(1, 26) == 1;
    }

    @Override
    public void onActivation(Player player, BlockBreakEvent ev, int level) {

    }
}
