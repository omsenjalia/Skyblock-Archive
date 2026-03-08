package dev.skyblock.enchants.touch;
import dev.skyblock.enchants.BaseTouchEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.player.PlayerInteractEvent;
public class Scythe extends BaseTouchEnchant {
    public static final int ID = 157;
    public Scythe() { super(ID, "Scythe", 5); }
    @Override
    public void onActivation(Player player, PlayerInteractEvent ev, int level) {

    }
}
