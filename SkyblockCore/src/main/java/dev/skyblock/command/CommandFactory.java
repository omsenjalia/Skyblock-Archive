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
        // Register more commands here
    }
}
