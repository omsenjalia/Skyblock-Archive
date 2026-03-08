package dev.skyblock.user;
import org.bukkit.Bukkit;
import org.bukkit.entity.Player;
import java.util.*;
public class User {
    private final String username;
    private final UUID uuid;
    private double money;
    private int mobcoin, xp, xpbank, mana, blocks, kills, deaths, killstreak, chips, won, bounty, seltag;
    private String island = "", gang = "", pet = "", petname = "";
    private List<String> tags = new ArrayList<>();
    private Map<String, Integer> kits = new HashMap<>();
    private Map<String, Object> base = new HashMap<>();
    private long seconds;
    public User(UUID uuid, String username) { this.uuid = uuid; this.username = username; }
    public String getUsername() { return username; }
    public UUID getUuid() { return uuid; }
    public Player getPlayer() { return Bukkit.getPlayer(uuid); }
    public double getMoney() { return money; }
    public void setMoney(double m) { this.money = m; }
    public void addMoney(double a) { this.money += a; }
    public int getMobcoin() { return mobcoin; }
    public void setMobcoin(int m) { this.mobcoin = m; }
    public void addMobcoin(int a) { this.mobcoin += a; }
    public int getXp() { return xp; }
    public void setXp(int x) { this.xp = x; }
    public void addXp(int a) { this.xp += a; }
    public int getXpbank() { return xpbank; }
    public void setXpbank(int x) { this.xpbank = x; }
    public int getMana() { return mana; }
    public void setMana(int m) { this.mana = m; }
    public void addMana(int a) { this.mana += a; }
    public int getBlocks() { return blocks; }
    public void setBlocks(int b) { this.blocks = b; }
    public void addBlocks(int a) { this.blocks += a; }
    public int getKills() { return kills; }
    public void setKills(int k) { this.kills = k; }
    public void addKill() { this.kills++; }
    public int getDeaths() { return deaths; }
    public void setDeaths(int d) { this.deaths = d; }
    public void addDeath() { this.deaths++; }
    public int getKillstreak() { return killstreak; }
    public void setKillstreak(int ks) { this.killstreak = ks; }
    public String getIsland() { return island; }
    public void setIsland(String i) { this.island = i; }
    public String getGang() { return gang; }
    public void setGang(String g) { this.gang = g; }
    public int getChips() { return chips; }
    public void setChips(int c) { this.chips = c; }
    public int getBounty() { return bounty; }
    public void setBounty(int b) { this.bounty = b; }
}
