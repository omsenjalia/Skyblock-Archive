package dev.skyblock.enchants.sword;
import dev.skyblock.SkyblockCore;
import dev.skyblock.enchants.BaseMeleeEnchant;
import dev.skyblock.user.User;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
public class Wizardly extends BaseMeleeEnchant {
    public static final int ID = 177;
    public Wizardly() { super(ID, "Wizardly", 15); }
    @Override
    public boolean isApplicableTo(Player holder, int level) { return java.util.concurrent.ThreadLocalRandom.current().nextInt(1, 26) == 1; }
    @Override
    public void onActivation(Player victim, Player attacker, EntityDamageByEntityEvent ev, int level) {
        User vUser = SkyblockCore.getInstance().getUserManager().getOnlineUser(victim.getUniqueId());
        if (vUser != null) {
            int mana = Math.min(vUser.getMana(), level * 2);
            vUser.addMana(-mana);
            attacker.sendMessage("§bWizardly §aActivated! Consumed " + mana + " mana from victim.");
        }
    }
}
