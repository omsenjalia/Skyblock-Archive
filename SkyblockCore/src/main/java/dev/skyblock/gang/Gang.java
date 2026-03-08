package dev.skyblock.gang;

import java.util.*;

public class Gang {
    private final String name;
    private String leader;
    private String motd;
    private int level;
    private int points;
    private final List<String> members;
    private final Map<String, Integer> kills;
    private final Map<String, Integer> deaths;
    private final Set<String> onlineMembers = new HashSet<>();

    public Gang(String name, String leader) {
        this.name = name;
        this.leader = leader.toLowerCase();
        this.motd = "Welcome to " + name;
        this.level = 1;
        this.points = 0;
        this.members = new ArrayList<>();
        this.members.add(this.leader);
        this.kills = new HashMap<>();
        this.deaths = new HashMap<>();
    }

    public String getName() { return name; }
    public String getLeader() { return leader; }
    public void setLeader(String leader) { this.leader = leader.toLowerCase(); }
    public String getMotd() { return motd; }
    public void setMotd(String motd) { this.motd = motd; }
    public int getLevel() { return level; }
    public void setLevel(int level) { this.level = level; }
    public int getPoints() { return points; }
    public void setPoints(int points) { this.points = points; }

    public List<String> getMembers() { return members; }
    public void addMember(String player) {
        if (!members.contains(player.toLowerCase())) {
            members.add(player.toLowerCase());
        }
    }
    public void removeMember(String player) {
        members.remove(player.toLowerCase());
    }

    public boolean isLeader(String player) { return leader.equalsIgnoreCase(player); }
    public boolean isMember(String player) { return members.contains(player.toLowerCase()); }

    public Set<String> getOnlineMembers() { return onlineMembers; }
}
