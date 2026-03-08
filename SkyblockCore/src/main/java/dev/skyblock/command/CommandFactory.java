package dev.skyblock.command;

import dev.skyblock.SkyblockCore;

public class CommandFactory {
    private final SkyblockCore plugin;

    public CommandFactory(SkyblockCore plugin) {
        this.plugin = plugin;
        registerCommands();
    }

    private void registerCommands() {
        plugin.getCommand("is").setExecutor(new IslandCommand(plugin));
        plugin.getCommand("gang").setExecutor(new GangCommand(plugin));
        plugin.getCommand("shop").setExecutor(new ShopCommand(plugin));
        plugin.getCommand("sell").setExecutor(new SellCommand(plugin));
        plugin.getCommand("pay").setExecutor(new PayCommand(plugin));
        plugin.getCommand("bal").setExecutor(new BalCommand(plugin));
        plugin.getCommand("baltop").setExecutor(new BaltopCommand(plugin));
        plugin.getCommand("warp").setExecutor(new WarpCommand(plugin));
        plugin.getCommand("home").setExecutor(new HomeCommand(plugin));
        plugin.getCommand("sethome").setExecutor(new HomeCommand(plugin));
        plugin.getCommand("enchant").setExecutor(new EnchantCommand(plugin));
        plugin.getCommand("ce").setExecutor(new CustomEnchantCommand(plugin));
        plugin.getCommand("spawn").setExecutor(new SpawnCommand());
        plugin.getCommand("tilegive").setExecutor(new TileGiveCommand());
    }
}
