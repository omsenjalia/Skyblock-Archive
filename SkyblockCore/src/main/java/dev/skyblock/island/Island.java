package dev.skyblock.island;

import java.util.*;

public class Island {
    private final String id;
    private String name;
    private String owner;
    private String creator;
    private int level;
    private int points;
    private double money;
    private int radius;
    private String motd;

    private List<String> helpers = new ArrayList<>();
    private List<String> admins = new ArrayList<>();
    private List<String> coowners = new ArrayList<>();
    private List<String> bans = new ArrayList<>();

    private Map<String, List<String>> jobs = new HashMap<>();

    private int spawnerUpgrade;
    private int oregenUpgrade;
    private int autominerUpgrade;
    private int autosellerUpgrade;
    private int hopperUpgrade;
    private int farmUpgrade;
    private int vlimitUpgrade;

    public Island(String id, String name, String owner) {
        this.id = id;
        this.name = name;
        this.owner = owner.toLowerCase();
        this.creator = owner.toLowerCase();
        this.level = 1;
        this.points = 0;
        this.money = 0;
        this.radius = 10;
        this.motd = "Welcome to " + name;

        // Initialize job lists
        for (String job : Arrays.asList("miners", "farmers", "placers", "builders", "labourers", "butchers")) {
            jobs.put(job, new ArrayList<>());
        }
    }

    // Getters and Setters
    public String getId() { return id; }
    public String getName() { return name; }
    public void setName(String name) { this.name = name; }
    public String getOwner() { return owner; }
    public void setOwner(String owner) { this.owner = owner.toLowerCase(); }
    public int getLevel() { return level; }
    public void setLevel(int level) { this.level = level; }
    public int getPoints() { return points; }
    public void setPoints(int points) { this.points = points; }
    public double getMoney() { return money; }
    public void setMoney(double money) { this.money = money; }
    public int getRadius() { return radius; }
    public void setRadius(int radius) { this.radius = radius; }
    public String getMotd() { return motd; }
    public void setMotd(String motd) { this.motd = motd; }

    public List<String> getHelpers() { return helpers; }
    public List<String> getAdmins() { return admins; }
    public List<String> getCoowners() { return coowners; }
    public List<String> getBans() { return bans; }

    public boolean isOwner(String player) { return owner.equalsIgnoreCase(player); }
    public boolean isCoOwner(String player) { return coowners.contains(player.toLowerCase()); }
    public boolean isAdmin(String player) { return isOwner(player) || admins.contains(player.toLowerCase()); }
    public boolean isHelper(String player) { return helpers.contains(player.toLowerCase()); }
    public boolean isMember(String player) { return isOwner(player) || helpers.contains(player.toLowerCase()); }

    public void addMember(String player) {
        if (!helpers.contains(player.toLowerCase())) {
            helpers.add(player.toLowerCase());
        }
    }

    public void removeMember(String player) {
        player = player.toLowerCase();
        helpers.remove(player);
        admins.remove(player);
        coowners.remove(player);
    }

    // Add more methods for upgrades, jobs etc.
}
