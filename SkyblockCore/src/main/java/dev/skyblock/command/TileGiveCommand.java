package dev.skyblock.command;

import dev.skyblock.tiles.TileItemFactory;
import org.bukkit.Material;
import org.bukkit.command.Command;
import org.bukkit.command.CommandExecutor;
import org.bukkit.command.CommandSender;
import org.bukkit.entity.Player;
import org.bukkit.inventory.ItemStack;

public class TileGiveCommand implements CommandExecutor {
    @Override
    public boolean onCommand(CommandSender sender, Command command, String label, String[] args) {
        if (!(sender instanceof Player player)) { sender.sendMessage("Players only."); return true; }
        if (!player.isOp()) { player.sendMessage("§cNo permission."); return true; }

        if (args.length < 1) {
            player.sendMessage("§eUsage: /tilegive <type> [level] [ore]");
            player.sendMessage("§eTypes: oregen, autoseller, autominer, catalyst");
            player.sendMessage("§eOres: diamond, emerald, iron, gold, lapis, coal, quartz, ancient_debris");
            return true;
        }

        ItemStack item = switch (args[0].toLowerCase()) {
            case "oregen" -> {
                Material ore = Material.IRON_ORE;
                if (args.length >= 3) {
                    ore = switch (args[2].toLowerCase()) {
                        case "diamond"        -> Material.DIAMOND_ORE;
                        case "emerald"        -> Material.EMERALD_ORE;
                        case "gold"           -> Material.GOLD_ORE;
                        case "lapis"          -> Material.LAPIS_ORE;
                        case "coal"           -> Material.COAL_ORE;
                        case "quartz"         -> Material.NETHER_QUARTZ_ORE;
                        case "ancient_debris" -> Material.ANCIENT_DEBRIS;
                        default               -> Material.IRON_ORE;
                    };
                }
                int level = args.length >= 2 ? parseInt(args[1], 1) : 1;
                yield TileItemFactory.createOreGen(ore, level);
            }
            case "autoseller" -> TileItemFactory.createAutoSeller(parseInt(args.length >= 2 ? args[1] : "1", 1), 0);
            case "autominer"  -> TileItemFactory.createAutoMiner(parseInt(args.length >= 2 ? args[1] : "1", 1), 0, 1);
            case "catalyst"   -> TileItemFactory.createCatalyst();
            default -> { player.sendMessage("§cUnknown type."); yield null; }
        };

        if (item != null) {
            player.getInventory().addItem(item);
            player.sendMessage("§aGiven " + args[0] + "!");
        }
        return true;
    }

    private int parseInt(String s, int def) {
        try { return Integer.parseInt(s); } catch (NumberFormatException e) { return def; }
    }
}
