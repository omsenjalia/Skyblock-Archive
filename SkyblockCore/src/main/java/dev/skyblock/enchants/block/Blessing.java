package dev.skyblock.enchants.block;

import dev.skyblock.enchants.BaseBlockEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.block.BlockBreakEvent;
import org.bukkit.potion.PotionEffect;
import org.bukkit.potion.PotionEffectType;
import java.util.concurrent.ThreadLocalRandom;

public class Blessing extends BaseBlockEnchant {
    public static final int ID = 202;

    public Blessing() {
        super(ID, "Blessing", 5);
    }

    @Override
    public boolean isApplicableTo(Player player, int level) {
        return ThreadLocalRandom.current().nextInt(1, 26) == 1;
    }

    @Override
    public void onActivation(Player player, BlockBreakEvent ev, int level) {

    }
}
