package dev.skyblock.command;

import dev.skyblock.SkyblockCore;
import dev.skyblock.gang.Gang;
import dev.skyblock.user.User;
import org.bukkit.Bukkit;
import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.bukkit.entity.Player;
import org.jetbrains.annotations.NotNull;

import java.util.ArrayList;
import java.util.List;

public class GangCommand implements CommandExecutor {
    private final SkyblockCore plugin;

    public GangCommand(SkyblockCore plugin) {
        this.plugin = plugin;
    }

    @Override
    public boolean onCommand(@NotNull CommandSender sender, @NotNull Command command, @NotNull String label, @NotNull String[] args) {
        if (!(sender instanceof Player player)) return true;
        User user = plugin.getUserManager().getOnlineUser(player.getUniqueId());
        if (user == null) return true;

        if (args.length == 0) {
            player.sendMessage("§e/gang create|disband|invite|accept|kick|leave|chat|info");
            return true;
        }

        switch (args[0].toLowerCase()) {
            case "create": {
                if (args.length < 2) {
                    player.sendMessage("§eUsage: /gang create <name>");
                    return true;
                }
                if (!user.getGang().isEmpty()) {
                    player.sendMessage("§cYou are already in a gang!");
                    return true;
                }
                String name = args[1];
                if (name.length() > 16) {
                    player.sendMessage("§cGang name too long (max 16).");
                    return true;
                }
                plugin.getGangManager().createGang(player, name);
                user.setGang(name);
                player.sendMessage("§aGang §e" + name + " §acreated!");
                break;
            }
            case "disband": {
                if (user.getGang().isEmpty()) {
                    player.sendMessage("§cYou are not in a gang.");
                    return true;
                }
                Gang gang = plugin.getGangManager().getGang(user.getGang()).orElse(null);
                if (gang == null || !gang.getLeader().equalsIgnoreCase(player.getName())) {
                    player.sendMessage("§cOnly the gang leader can disband.");
                    return true;
                }
                // Clear gang for all online members
                for (String member : gang.getMembers()) {
                    Player mp = Bukkit.getPlayerExact(member);
                    if (mp != null) {
                        User mu = plugin.getUserManager().getOnlineUser(mp.getUniqueId());
                        if (mu != null) mu.setGang("");
                        mp.sendMessage("§cYour gang has been disbanded.");
                    }
                }
                plugin.getGangManager().deleteGang(gang);
                break;
            }
            case "invite": {
                if (args.length < 2) {
                    player.sendMessage("§eUsage: /gang invite <player>");
                    return true;
                }
                if (user.getGang().isEmpty()) {
                    player.sendMessage("§cYou are not in a gang.");
                    return true;
                }
                Gang gang = plugin.getGangManager().getGang(user.getGang()).orElse(null);
                if (gang == null || !gang.getLeader().equalsIgnoreCase(player.getName())) {
                    player.sendMessage("§cOnly the leader can invite.");
                    return true;
                }
                Player target = Bukkit.getPlayer(args[1]);
                if (target == null) {
                    player.sendMessage("§cPlayer not found.");
                    return true;
                }
                User targetUser = plugin.getUserManager().getOnlineUser(target.getUniqueId());
                if (targetUser == null || !targetUser.getGang().isEmpty()) {
                    player.sendMessage("§cThat player is already in a gang.");
                    return true;
                }
                target.sendMessage("§e" + player.getName() + " §ainvited you to gang §e" + gang.getName() + "§a. Type §e/gang accept " + gang.getName() + " §ato join.");
                player.sendMessage("§aInvite sent to §e" + target.getName());
                break;
            }
            case "accept": {
                if (args.length < 2) {
                    player.sendMessage("§eUsage: /gang accept <name>");
                    return true;
                }
                if (!user.getGang().isEmpty()) {
                    player.sendMessage("§cYou are already in a gang.");
                    return true;
                }
                Gang gang = plugin.getGangManager().getGang(args[1]).orElse(null);
                if (gang == null) {
                    player.sendMessage("§cGang not found.");
                    return true;
                }
                gang.addMember(player.getName());
                user.setGang(gang.getName());
                plugin.getGangManager().saveGang(gang);
                for (String member : gang.getMembers()) {
                    Player mp = Bukkit.getPlayerExact(member);
                    if (mp != null) mp.sendMessage("§e" + player.getName() + " §ajoined the gang!");
                }
                break;
            }
            case "kick": {
                if (args.length < 2) {
                    player.sendMessage("§eUsage: /gang kick <player>");
                    return true;
                }
                if (user.getGang().isEmpty()) {
                    player.sendMessage("§cYou are not in a gang.");
                    return true;
                }
                Gang gang = plugin.getGangManager().getGang(user.getGang()).orElse(null);
                if (gang == null || !gang.getLeader().equalsIgnoreCase(player.getName())) {
                    player.sendMessage("§cOnly the leader can kick.");
                    return true;
                }
                String targetName = args[1];
                if (!gang.getMembers().contains(targetName.toLowerCase())) {
                    player.sendMessage("§cThat player is not in your gang.");
                    return true;
                }
                gang.removeMember(targetName);
                plugin.getGangManager().saveGang(gang);
                Player target = Bukkit.getPlayerExact(targetName);
                if (target != null) {
                    User mu = plugin.getUserManager().getOnlineUser(target.getUniqueId());
                    if (mu != null) mu.setGang("");
                    target.sendMessage("§cYou have been kicked from the gang.");
                }
                player.sendMessage("§aKicked §e" + targetName + " §afrom the gang.");
                break;
            }
            case "leave": {
                if (user.getGang().isEmpty()) {
                    player.sendMessage("§cYou are not in a gang.");
                    return true;
                }
                Gang gang = plugin.getGangManager().getGang(user.getGang()).orElse(null);
                if (gang != null) {
                    if (gang.getLeader().equalsIgnoreCase(player.getName())) {
                        player.sendMessage("§cYou are the leader. Use /gang disband instead.");
                        return true;
                    }
                    gang.removeMember(player.getName());
                    plugin.getGangManager().saveGang(gang);
                    for (String member : gang.getMembers()) {
                        Player mp = Bukkit.getPlayerExact(member);
                        if (mp != null) mp.sendMessage("§e" + player.getName() + " §cleft the gang.");
                    }
                }
                user.setGang("");
                player.sendMessage("§aYou left the gang.");
                break;
            }
            case "chat":
            case "c": {
                if (user.getGang().isEmpty()) {
                    player.sendMessage("§cYou are not in a gang.");
                    return true;
                }
                if (args.length < 2) {
                    player.sendMessage("§eUsage: /gang chat <message>");
                    return true;
                }
                Gang gang = plugin.getGangManager().getGang(user.getGang()).orElse(null);
                if (gang == null) return true;
                String msg = String.join(" ", java.util.Arrays.copyOfRange(args, 1, args.length));
                String formatted = "§d[Gang] §e" + player.getName() + "§7: §f" + msg;
                for (String member : gang.getMembers()) {
                    Player mp = Bukkit.getPlayerExact(member);
                    if (mp != null) mp.sendMessage(formatted);
                }
                break;
            }
            case "info": {
                if (user.getGang().isEmpty()) {
                    player.sendMessage("§cYou are not in a gang.");
                    return true;
                }
                Gang gang = plugin.getGangManager().getGang(user.getGang()).orElse(null);
                if (gang == null) {
                    player.sendMessage("§cGang not found.");
                    return true;
                }
                player.sendMessage("§6--- Gang Info ---");
                player.sendMessage("§eName: §7" + gang.getName());
                player.sendMessage("§eLeader: §7" + gang.getLeader());
                player.sendMessage("§eMembers: §7" + String.join(", ", gang.getMembers()));
                break;
            }
            default:
                player.sendMessage("§e/gang create|disband|invite|accept|kick|leave|chat|info");
        }
        return true;
    }
}
