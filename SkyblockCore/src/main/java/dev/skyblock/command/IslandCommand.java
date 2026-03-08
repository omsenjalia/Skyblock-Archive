package dev.skyblock.command;

import dev.skyblock.SkyblockCore;
import dev.skyblock.island.Island;
import dev.skyblock.user.User;
import dev.skyblock.util.Format;
import org.bukkit.Bukkit;
import org.bukkit.Location;
import org.bukkit.World;
import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.bukkit.entity.Player;
import org.jetbrains.annotations.NotNull;

import java.util.ArrayList;
import java.util.List;
import java.util.Optional;

public class IslandCommand implements CommandExecutor {
    private final SkyblockCore plugin;

    public IslandCommand(SkyblockCore plugin) {
        this.plugin = plugin;
    }

    @Override
    public boolean onCommand(@NotNull CommandSender sender, @NotNull Command command, @NotNull String label, @NotNull String[] args) {
        if (!(sender instanceof Player player)) {
            sender.sendMessage("This command can only be used by players.");
            return true;
        }

        User user = plugin.getUserManager().getOnlineUser(player.getUniqueId());
        if (user == null) return true;

        if (args.length == 0) {
            player.sendMessage("§e/is create <name> §7- Create an island");
            player.sendMessage("§e/is go §7- Teleport to your island");
            player.sendMessage("§e/is top §7- Show top islands");
            player.sendMessage("§e/is help §7- Show help");
            return true;
        }

        switch (args[0].toLowerCase()) {
            case "create":
                if (args.length < 2) {
                    player.sendMessage("§cUsage: /is create <name>");
                    return true;
                }
                String name = args[1];
                if (!user.getIsland().isEmpty()) {
                    player.sendMessage("§cYou already have an island!");
                    return true;
                }
                Island island = plugin.getIslandManager().createIsland(player, name);
                user.setIsland(name);
                player.sendMessage("§aIsland §e" + name + " §acreated!");
                break;
            case "go": {
                if (user.getIsland().isEmpty()) {
                    player.sendMessage("§cYou don't have an island!");
                    return true;
                }
                Optional<Island> opt = plugin.getIslandManager().getIslandByName(user.getIsland());
                if (opt.isEmpty()) {
                    player.sendMessage("§cYour island could not be found. Try relogging.");
                    return true;
                }
                Island isl = opt.get();
                String worldName = isl.getId();
                org.bukkit.World world = org.bukkit.Bukkit.getWorld(worldName);
                if (world == null) {
                    org.bukkit.WorldCreator creator = new org.bukkit.WorldCreator(worldName);
                    creator.generator(new dev.skyblock.island.VoidChunkGenerator());
                    world = creator.createWorld();
                }
                Location home = isl.getHome("default", world);
                if (home == null) home = world.getSpawnLocation();
                player.teleport(home);
                player.sendMessage("§aTeleported to your island!");
                break;
            }
            case "info": {
                if (user.getIsland().isEmpty()) { player.sendMessage("§cYou don't have an island!"); return true; }
                Island isl = plugin.getIslandManager().getIslandByName(user.getIsland()).orElse(null);
                if (isl == null) { player.sendMessage("§cIsland not found."); return true; }
                player.sendMessage("§3§l--- Island Info ---");
                player.sendMessage("§fName: §e" + isl.getName());
                player.sendMessage("§fOwner: §e" + isl.getReceiver());
                player.sendMessage("§fLevel: §e" + isl.getLevel());
                player.sendMessage("§fPoints: §e" + isl.getPoints());
                player.sendMessage("§fBalance: §a$" + Format.formatMoney(isl.getMoney()));
                player.sendMessage("§fRadius: §e" + isl.getRadius());
                List<String> members = new ArrayList<>();
                members.addAll(isl.getCoowners());
                members.addAll(isl.getHelpers());
                player.sendMessage("§fMembers: §e" + (members.isEmpty() ? "None" : String.join(", ", members)));
                player.sendMessage("§fLocked: §e" + isl.getLocked());
                break;
            }
            case "lock": {
                if (user.getIsland().isEmpty()) { player.sendMessage("§cYou don't have an island!"); return true; }
                Island isl = plugin.getIslandManager().getIslandByName(user.getIsland()).orElse(null);
                if (isl == null) return true;
                isl.setLocked("true");
                plugin.getIslandManager().saveIsland(isl);
                player.sendMessage("§aIsland locked.");
                break;
            }
            case "unlock": {
                if (user.getIsland().isEmpty()) { player.sendMessage("§cYou don't have an island!"); return true; }
                Island isl = plugin.getIslandManager().getIslandByName(user.getIsland()).orElse(null);
                if (isl == null) return true;
                isl.setLocked("false");
                plugin.getIslandManager().saveIsland(isl);
                player.sendMessage("§aIsland unlocked.");
                break;
            }
            case "invite": {
                if (args.length < 2) { player.sendMessage("§cUsage: /is invite <player>"); return true; }
                if (user.getIsland().isEmpty()) { player.sendMessage("§cYou don't have an island!"); return true; }
                Island isl = plugin.getIslandManager().getIslandByName(user.getIsland()).orElse(null);
                if (isl == null) return true;
                if (!isl.getReceiver().equalsIgnoreCase(player.getName())) { player.sendMessage("§cOnly the island owner can invite players."); return true; }
                Player target = Bukkit.getPlayerExact(args[1]);
                if (target == null) { player.sendMessage("§cPlayer not found or not online."); return true; }
                User targetUser = plugin.getUserManager().getOnlineUser(target.getUniqueId());
                if (targetUser == null) return true;
                if (!targetUser.getIsland().isEmpty()) { player.sendMessage("§cThat player already has an island."); return true; }
                isl.getCoowners().add(target.getName().toLowerCase());
                targetUser.setIsland(isl.getName());
                plugin.getIslandManager().saveIsland(isl);
                target.sendMessage("§aYou have been invited to §e" + player.getName() + "§a's island!");
                player.sendMessage("§a" + target.getName() + " has been added to your island.");
                break;
            }
            case "kick": {
                if (args.length < 2) { player.sendMessage("§cUsage: /is kick <player>"); return true; }
                if (user.getIsland().isEmpty()) { player.sendMessage("§cYou don't have an island!"); return true; }
                Island isl = plugin.getIslandManager().getIslandByName(user.getIsland()).orElse(null);
                if (isl == null) return true;
                if (!isl.getReceiver().equalsIgnoreCase(player.getName())) { player.sendMessage("§cOnly the island owner can kick players."); return true; }
                String targetName = args[1].toLowerCase();
                if (!isl.getCoowners().contains(targetName)) { player.sendMessage("§cThat player is not a member of your island."); return true; }
                isl.getCoowners().remove(targetName);
                plugin.getIslandManager().saveIsland(isl);
                // Clear their island reference if online
                Player target = Bukkit.getPlayerExact(args[1]);
                if (target != null) {
                    User targetUser = plugin.getUserManager().getOnlineUser(target.getUniqueId());
                    if (targetUser != null) targetUser.setIsland("");
                    target.sendMessage("§cYou have been kicked from §e" + player.getName() + "§c's island.");
                }
                player.sendMessage("§a" + args[1] + " has been kicked from your island.");
                break;
            }
            case "members": {
                if (user.getIsland().isEmpty()) { player.sendMessage("§cYou don't have an island!"); return true; }
                Island isl = plugin.getIslandManager().getIslandByName(user.getIsland()).orElse(null);
                if (isl == null) return true;
                player.sendMessage("§3§l--- Island Members ---");
                player.sendMessage("§fOwner: §e" + isl.getReceiver());
                if (isl.getCoowners().isEmpty()) {
                    player.sendMessage("§fMembers: §7None");
                } else {
                    player.sendMessage("§fMembers: §e" + String.join(", ", isl.getCoowners()));
                }
                break;
            }
            case "sethome": {
                if (user.getIsland().isEmpty()) { player.sendMessage("§cYou don't have an island!"); return true; }
                Island isl = plugin.getIslandManager().getIslandByName(user.getIsland()).orElse(null);
                if (isl == null) return true;
                if (!isl.isAnOwner(player.getName())) { player.sendMessage("§cOnly island owners can set the home."); return true; }
                isl.setHome("default", player.getLocation());
                plugin.getIslandManager().saveIsland(isl);
                player.sendMessage("§aIsland home set!");
                break;
            }
            case "delete": {
                if (user.getIsland().isEmpty()) { player.sendMessage("§cYou don't have an island!"); return true; }
                Island isl = plugin.getIslandManager().getIslandByName(user.getIsland()).orElse(null);
                if (isl == null) return true;
                if (!isl.getReceiver().equalsIgnoreCase(player.getName())) { player.sendMessage("§cOnly the island owner can delete the island."); return true; }

                // Kick all members
                for (String memberName : new ArrayList<>(isl.getCoowners())) {
                    Player member = Bukkit.getPlayerExact(memberName);
                    if (member != null) {
                        User memberUser = plugin.getUserManager().getOnlineUser(member.getUniqueId());
                        if (memberUser != null) memberUser.setIsland("");
                        member.sendMessage("§cThe island you were a member of has been deleted.");
                        // Teleport to spawn
                        World spawnWorld = Bukkit.getWorld("world");
                        if (spawnWorld != null) member.teleport(spawnWorld.getSpawnLocation());
                    }
                }

                // Teleport player to spawn first
                World spawnWorld = Bukkit.getWorld("world");
                if (spawnWorld != null) player.teleport(spawnWorld.getSpawnLocation());

                user.setIsland("");
                plugin.getIslandManager().deleteIsland(isl);
                player.sendMessage("§aYour island has been deleted.");
                break;
            }
            case "top": {
                plugin.getDatabaseManager().queryAsync(
                    "SELECT l.name, l.level, l.points, i.owner FROM level l JOIN info i ON l.name = i.name ORDER BY l.points DESC LIMIT 10",
                    rs -> {
                        List<String> lines = new ArrayList<>();
                        lines.add("§6§l--- Island Top ---");
                        try {
                            int rank = 1;
                            while (rs.next()) {
                                lines.add("§e" + rank + ". §f" + rs.getString("name") +
                                          " §7(Owner: " + rs.getString("owner") + ")" +
                                          " §aLevel: " + rs.getInt("level") +
                                          " §bPoints: " + rs.getInt("points"));
                                rank++;
                            }
                            if (rank == 1) lines.add("§7No islands found.");
                        } catch (Exception e) { e.printStackTrace(); }
                        Bukkit.getScheduler().runTask(plugin, () -> {
                            for (String line : lines) player.sendMessage(line);
                        });
                    }
                );
                break;
            }
            case "setbiome": {
                if (args.length < 2) { player.sendMessage("§cUsage: /is setbiome <biome>"); return true; }
                if (user.getIsland().isEmpty()) { player.sendMessage("§cYou don't have an island!"); return true; }
                Island isl = plugin.getIslandManager().getIslandByName(user.getIsland()).orElse(null);
                if (isl == null) return true;
                if (!isl.isAnOwner(player.getName())) { player.sendMessage("§cOnly island owners can change the biome."); return true; }
                org.bukkit.block.Biome biome;
                try {
                    biome = org.bukkit.block.Biome.valueOf(args[1].toUpperCase());
                } catch (IllegalArgumentException e) {
                    player.sendMessage("§cInvalid biome. Try: PLAINS, DESERT, FOREST, SNOWY_PLAINS, JUNGLE, SAVANNA");
                    return true;
                }
                World islandWorld = Bukkit.getWorld(isl.getId());
                if (islandWorld == null) { player.sendMessage("§cIsland world not loaded."); return true; }
                // Set biome for all chunks in island radius
                int radius = isl.getRadius();
                Location center = islandWorld.getSpawnLocation();
                for (int x = (int)center.getX() - radius; x <= (int)center.getX() + radius; x += 4) {
                    for (int z = (int)center.getZ() - radius; z <= (int)center.getZ() + radius; z += 4) {
                        for (int y = islandWorld.getMinHeight(); y < islandWorld.getMaxHeight(); y += 4) {
                            islandWorld.setBiome(x, y, z, biome);
                        }
                    }
                }
                player.sendMessage("§aIsland biome set to §e" + biome.name().toLowerCase() + "§a!");
                break;
            }
            default:
                player.sendMessage("§cUnknown subcommand.");
                break;
        }

        return true;
    }
}
