package dev.skyblock.shop;

import org.bukkit.Material;
import org.bukkit.entity.Player;
import org.bukkit.event.EventHandler;
import org.bukkit.event.Listener;
import org.bukkit.event.inventory.InventoryClickEvent;
import org.bukkit.event.inventory.InventoryCloseEvent;
import org.bukkit.inventory.ItemStack;

public class ShopListener implements Listener {
    private final ShopManager shopManager;

    public ShopListener(ShopManager shopManager) {
        this.shopManager = shopManager;
    }

    @EventHandler
    public void onInventoryClick(InventoryClickEvent event) {
        if (!(event.getWhoClicked() instanceof Player player)) return;
        if (!(event.getInventory().getHolder() instanceof ShopHolder holder)) return;

        event.setCancelled(true);
        ItemStack clicked = event.getCurrentItem();
        if (clicked == null || clicked.getType() == Material.AIR) return;
        if (clicked.getType() == Material.GRAY_STAINED_GLASS_PANE) return;

        // Back button
        if (clicked.getType() == Material.RED_STAINED_GLASS_PANE) {
            shopManager.handleBack(player, holder.getMenu());
            return;
        }

        // Handle navigation or purchase based on current menu
        shopManager.handleClick(player, holder.getMenu(), clicked.getType());
    }

    @EventHandler
    public void onInventoryClose(InventoryCloseEvent event) {
        // nothing needed, stateless menus
    }
}
