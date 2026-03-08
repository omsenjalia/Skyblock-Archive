package dev.skyblock.enchants.touch;
import dev.skyblock.enchants.BaseTouchEnchant;
import org.bukkit.entity.Player;
import org.bukkit.event.player.PlayerInteractEvent;
public class Demolisher extends BaseTouchEnchant {
    public static final int ID = 182;
    public Demolisher() { super(ID, "Demolisher", 5); }
    @Override
    public void onActivation(Player player, PlayerInteractEvent ev, int level) {

    }
}
