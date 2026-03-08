package dev.skyblock.user;

import org.bukkit.Bukkit;
import org.bukkit.entity.Player;

import java.util.*;

public class User {
    private final String username;
    private final UUID uuid;
    private double money;
    private int mobcoin;
    private int xp;
    private int xpbank;
    private int mana;
    private int blocks;
    private int kills;
    private int deaths;
    private int streak;
    private int chips;
    private int won;
    private int bounty;
    private String island;
    private String gang;
    private int seltag;
    private List<String> tags;
    private String pet;
    private String petname;
    private Map<String, Integer> kits;
    private Map<String, Object> base; // For level/exp
    private long seconds;

    public User(UUID uuid, String username) {
        this.uuid = uuid;
        this.username = username;
        this.tags = new ArrayList<>();
        this.kits = new HashMap<>();
        this.base = new HashMap<>();
        this.island = "";
        this.gang = "";
        this.pet = "";
        this.petname = "";
        this.seltag = -1;
    }

    public String getUsername() { return username; }
    public UUID getUuid() { return uuid; }
    public Player getPlayer() { return Bukkit.getPlayer(uuid); }

    public double getMoney() { return money; }
    public void setMoney(double money) { this.money = money; }
    public void addMoney(double amount) { this.money += amount; }

    public int getMobcoin() { return mobcoin; }
    public void setMobcoin(int mobcoin) { this.mobcoin = mobcoin; }
    public void addMobcoin(int amount) { this.mobcoin += amount; }

    public int getMana() { return mana; }
    public void setMana(int mana) { this.mana = mana; }
    public void addMana(int amount) { this.mana += amount; }

    public int getBlocks() { return blocks; }
    public void setBlocks(int blocks) { this.blocks = blocks; }
    public void addBlocks(int amount) { this.blocks += amount; }

    public int getKills() { return kills; }
    public void setKills(int kills) { this.kills = kills; }
    public void addKill() { this.kills++; }

    public int getDeaths() { return deaths; }
    public void setDeaths(int deaths) { this.deaths = deaths; }
    public void addDeath() { this.deaths++; }

    public String getIsland() { return island; }
    public void setIsland(String island) { this.island = island; }

    public String getGang() { return gang; }
    public void setGang(String gang) { this.gang = gang; }

    // Add other getters/setters as needed
}
