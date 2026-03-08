package dev.skyblock.enchants.sword;
import dev.skyblock.SkyblockCore;
import dev.skyblock.enchants.BaseMeleeEnchant;
import org.bukkit.NamespacedKey;
import org.bukkit.attribute.Attribute;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
import org.bukkit.inventory.ItemStack;
import org.bukkit.persistence.PersistentDataType;
import org.bukkit.potion.PotionEffectType;
import java.util.concurrent.ThreadLocalRandom;
public class Lifesteal extends BaseMeleeEnchant {
    public static final int ID = 100;
    public static final int LIFESHIELD_ID = 186;
    public Lifesteal() { super(ID, "Lifesteal", 15); }
    @Override
    public boolean isApplicableTo(Player holder, int level) {
        int bound = level >= 15 ? 20 : (25 - level);
        return ThreadLocalRandom.current().nextInt(1, bound + 1) == 1;
    }
    @Override
    public void onActivation(Player victim, Player attacker, EntityDamageByEntityEvent ev, int level) {
        attacker.sendMessage("§bLifeSteal §aActivated!");
        int lifeShieldLevel = 0;
        ItemStack chestplate = victim.getInventory().getChestplate();
        if (chestplate != null && chestplate.hasItemMeta()) {
            NamespacedKey key = new NamespacedKey(SkyblockCore.getInstance(), "enchant_" + LIFESHIELD_ID);
            lifeShieldLevel = chestplate.getItemMeta().getPersistentDataContainer().getOrDefault(key, PersistentDataType.INTEGER, 0);
        }
        if (lifeShieldLevel > 0) {
            int blockBound = lifeShieldLevel > 10 ? 3 : 6;
            if (ThreadLocalRandom.current().nextInt(1, blockBound + 1) == 1) {
                attacker.sendMessage("§bLifeSteal §cDeactivated by LifeShield Enchant!");
                return;
            }
        }
        victim.removePotionEffect(PotionEffectType.REGENERATION);
        victim.removePotionEffect(PotionEffectType.HEALTH_BOOST);
        victim.sendMessage("§cStruck by §bLifeSteal §cEnchant! Health Effects removed");
        double healthTaken = level > 9 ? 8 : Math.ceil(level / 2.0);
        victim.setHealth(Math.max(0, victim.getHealth() - healthTaken));
        double maxHealth = attacker.getAttribute(Attribute.MAX_HEALTH).getValue();
        attacker.setHealth(Math.min(maxHealth, attacker.getHealth() + healthTaken));
    }
}
