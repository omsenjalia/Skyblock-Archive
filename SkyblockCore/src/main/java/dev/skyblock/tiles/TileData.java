package dev.skyblock.tiles;

public class TileData {
    private int level;
    private int type; // 0=money, 1=xp — used by AutoSeller
    private int fortuneEnabled; // 0 or 1 — used by AutoMiner
    private int fortuneLevel;   // 1-15 — used by AutoMiner

    public TileData(int level) {
        this.level = level;
        this.type = 0;
        this.fortuneEnabled = 0;
        this.fortuneLevel = 1;
    }

    public int getLevel() { return level; }
    public void setLevel(int level) { this.level = level; }
    public int getType() { return type; }
    public void setType(int type) { this.type = type; }
    public int getFortuneEnabled() { return fortuneEnabled; }
    public void setFortuneEnabled(int fortuneEnabled) { this.fortuneEnabled = fortuneEnabled; }
    public int getFortuneLevel() { return fortuneLevel; }
    public void setFortuneLevel(int fortuneLevel) { this.fortuneLevel = fortuneLevel; }
}
