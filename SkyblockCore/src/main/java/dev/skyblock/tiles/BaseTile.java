package dev.skyblock.tiles;

import org.bukkit.Location;

public abstract class BaseTile {
    protected final Location location;

    public BaseTile(Location location) {
        this.location = location;
    }

    public abstract void onUpdate();
    public Location getLocation() { return location; }
}
