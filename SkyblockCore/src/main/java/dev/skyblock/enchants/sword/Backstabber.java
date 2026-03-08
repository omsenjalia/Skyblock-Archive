package dev.skyblock.enchants.sword;
import dev.skyblock.enchants.BaseMeleeEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
public class Backstabber extends BaseMeleeEnchant {
    public static final int ID = 143;
    public Backstabber() { super(ID, "Backstabber", 15); }
    @Override
    public boolean isApplicableTo(Player holder, int level) { return true; }
    @Override
    public void onActivation(Player victim, Player attacker, EntityDamageByEntityEvent ev, int level) {
        if (victim.getLocation().getDirection().dot(attacker.getLocation().getDirection()) > 0) {
            attacker.sendMessage("§bBackstabber §aActivated!");
            ev.setDamage(ev.getDamage() + ((level - 1) / 100.0) * 4);
        }
    }
}
