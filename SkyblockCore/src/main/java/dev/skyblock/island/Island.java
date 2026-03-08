package dev.skyblock.island;
import org.bukkit.Bukkit;
import org.bukkit.Location;
import org.bukkit.World;
import org.bukkit.entity.Player;
import java.util.*;
import java.util.concurrent.ThreadLocalRandom;

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
    private int spawnerUpgrade, oregenUpgrade, autominerUpgrade, autosellerUpgrade, hopperUpgrade, farmUpgrade, vlimitUpgrade, miningUpgrade, farmingUpgrade;
    private String receiver, locked = "false";
    private Map<String, double[]> homes = new HashMap<>();
    private Map<String, List<String>> roles = new HashMap<>();
    private Map<String, Integer> oredata = new HashMap<>(), oredatapref = new HashMap<>();
    public Island(String id, String name, String owner) {
        this.id = id; this.name = name; this.owner = owner.toLowerCase(); this.creator = owner.toLowerCase();
        this.receiver = owner.toLowerCase(); this.level = 1; this.points = 0; this.money = 0; this.radius = 10;
        this.motd = "Welcome to " + name;
        for (String job : Arrays.asList("miners", "farmers", "placers", "builders", "labourers", "butchers")) jobs.put(job, new ArrayList<>());
    }
    public String getId() { return id; }
    public String getName() { return name; }
    public void setName(String name) { this.name = name; }
    public String getOwner() { return owner; }
    public void setOwner(String owner) { this.owner = owner.toLowerCase(); }
    public String getCreator() { return creator; }
    public void setCreator(String creator) { this.creator = creator.toLowerCase(); }
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
    public Map<String, List<String>> getJobs() { return jobs; }
    public int getSpawnerUpgrade() { return spawnerUpgrade; }
    public void setSpawnerUpgrade(int u) { this.spawnerUpgrade = u; }
    public int getOregenUpgrade() { return oregenUpgrade; }
    public void setOregenUpgrade(int u) { this.oregenUpgrade = u; }
    public int getAutominerUpgrade() { return autominerUpgrade; }
    public void setAutominerUpgrade(int u) { this.autominerUpgrade = u; }
    public int getAutosellerUpgrade() { return autosellerUpgrade; }
    public void setAutosellerUpgrade(int u) { this.autosellerUpgrade = u; }
    public int getHopperUpgrade() { return hopperUpgrade; }
    public void setHopperUpgrade(int u) { this.hopperUpgrade = u; }
    public int getFarmUpgrade() { return farmUpgrade; }
    public void setFarmUpgrade(int u) { this.farmUpgrade = u; }
    public int getVlimitUpgrade() { return vlimitUpgrade; }
    public void setVlimitUpgrade(int u) { this.vlimitUpgrade = u; }
    public int getMiningUpgrade() { return miningUpgrade; }
    public void setMiningUpgrade(int u) { this.miningUpgrade = u; }
    public int getFarmingUpgrade() { return farmingUpgrade; }
    public void setFarmingUpgrade(int u) { this.farmingUpgrade = u; }
    public String getReceiver() { return receiver; }
    public void setReceiver(String r) { this.receiver = r; }
    public String getLocked() { return locked; }
    public void setLocked(String l) { this.locked = l; }
    public Map<String, double[]> getHomes() { return homes; }
    public Map<String, Integer> getOredata() { return oredata; }
    public Map<String, Integer> getOredatapref() { return oredatapref; }

    public Player getRandomOnlineCoOwner() {
        List<Player> onlineCoOwners = new ArrayList<>();
        for (String coowner : coowners) {
            Player p = Bukkit.getPlayerExact(coowner);
            if (p != null && p.isOnline()) {
                onlineCoOwners.add(p);
            }
        }
        if (onlineCoOwners.isEmpty()) return null;
        return onlineCoOwners.get(ThreadLocalRandom.current().nextInt(onlineCoOwners.size()));
    }

    public boolean isOwner(String player) { return owner.equalsIgnoreCase(player); }
    public boolean isCoOwner(String player) { return coowners.contains(player.toLowerCase()); }
    public boolean isMember(String player) { return isOwner(player) || helpers.contains(player.toLowerCase()) || coowners.contains(player.toLowerCase()) || admins.contains(player.toLowerCase()); }
    public boolean isAnOwner(String player) { return isOwner(player) || isCoOwner(player); }
    public void setHome(String name, Location loc) { homes.put(name.toLowerCase(), new double[]{loc.getX(), loc.getY(), loc.getZ()}); }
    public Location getHome(String name, World world) {
        double[] c = homes.get(name.toLowerCase());
        return c == null ? null : new Location(world, c[0], c[1], c[2]);
    }
}
