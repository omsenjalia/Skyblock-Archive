package dev.skyblock.enchants.sword;
import dev.skyblock.SkyblockCore;
import dev.skyblock.enchants.BaseMeleeEnchant;
import dev.skyblock.user.User;
import org.bukkit.entity.Player;
import org.bukkit.event.entity.EntityDamageByEntityEvent;
public class SoulSnatcher extends BaseMeleeEnchant {
    public static final int ID = 122;
    public SoulSnatcher() { super(ID, "SoulSnatcher", 15); }
    @Override
    public boolean isApplicableTo(Player holder, int level) { return java.util.concurrent.ThreadLocalRandom.current().nextInt(1, 26) == 1; }
    @Override
    public void onActivation(Player victim, Player attacker, EntityDamageByEntityEvent ev, int level) {
        User aUser = SkyblockCore.getInstance().getUserManager().getOnlineUser(attacker.getUniqueId());
        User vUser = SkyblockCore.getInstance().getUserManager().getOnlineUser(victim.getUniqueId());
        if (aUser != null && vUser != null) {
            int xp = Math.min(vUser.getXp(), level * 5);
            vUser.setXp(vUser.getXp() - xp);
            aUser.setXp(aUser.getXp() + xp);
            attacker.sendMessage("§bSoulSnatcher §aActivated! Snatched " + xp + " XP");
        }
    }
}
