package dev.skyblock.shop;

import org.bukkit.inventory.Inventory;
import org.bukkit.inventory.InventoryHolder;

public class ShopHolder implements InventoryHolder {
    private final ShopMenu menu;
    private Inventory inventory;

    public enum ShopMenu {
        MAIN, BLOCKS, ITEMS, RESOURCES, FOOD, FARM, FARM_SAPLINGS,
        DYES, BREWING, FURNITURE, EQUIPMENTS
    }

    public ShopHolder(ShopMenu menu) { this.menu = menu; }
    public ShopMenu getMenu() { return menu; }

    @Override public Inventory getInventory() { return inventory; }
    public void setInventory(Inventory inv) { this.inventory = inv; }
}
