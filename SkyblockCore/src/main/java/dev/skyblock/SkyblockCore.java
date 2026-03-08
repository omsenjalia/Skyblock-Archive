package dev.skyblock;

import dev.skyblock.command.CommandFactory;
import dev.skyblock.db.DatabaseManager;
import dev.skyblock.enchants.EnchantManager;
import dev.skyblock.events.MainEventListener;
import dev.skyblock.gang.GangManager;
import dev.skyblock.island.IslandManager;
import dev.skyblock.item.ItemManager;
import dev.skyblock.pets.PetManager;
import dev.skyblock.scoreboard.ScoreboardManager;
import dev.skyblock.tiles.TileManager;
import dev.skyblock.user.UserManager;
import org.bukkit.plugin.java.JavaPlugin;
import org.bukkit.Bukkit;
import net.kyori.adventure.text.minimessage.MiniMessage;

public class SkyblockCore extends JavaPlugin {

    private static SkyblockCore instance;

    private DatabaseManager databaseManager;
    private UserManager userManager;
    private IslandManager islandManager;
    private GangManager gangManager;
    private EnchantManager enchantManager;
    private ItemManager itemManager;
    private TileManager tileManager;
    private PetManager petManager;
    private ScoreboardManager scoreboardManager;

    public static final String JOIN_MESSAGE = "Catalysts have been changed. they now spawn blocks on top of them ONLY. They no longer need water.";

    @Override
    public void onEnable() {
        instance = this;

        saveDefaultConfig();

        // Initialize Managers
        databaseManager = new DatabaseManager(this);
        userManager = new UserManager(this);
        islandManager = new IslandManager(this);
        gangManager = new GangManager(this);
        enchantManager = new EnchantManager(this);
        itemManager = new ItemManager(this);
        tileManager = new TileManager(this);
        petManager = new PetManager(this);
        scoreboardManager = new ScoreboardManager(this);

        // Register Command Factory
        new CommandFactory(this);

        // Register Listeners
        Bukkit.getPluginManager().registerEvents(new MainEventListener(this), this);

        getLogger().info("SkyblockCore by Infernus101 has been Enabled!");
        Bukkit.broadcast(MiniMessage.miniMessage().deserialize("<green><bold>> <yellow>" + JOIN_MESSAGE));
    }

    @Override
    public void onDisable() {
        if (petManager != null) petManager.shutdown();
        if (tileManager != null) tileManager.shutdown();
        if (enchantManager != null) enchantManager.shutdown();
        if (gangManager != null) gangManager.shutdown();
        if (islandManager != null) islandManager.shutdown();
        if (userManager != null) userManager.shutdown();
        if (databaseManager != null) databaseManager.shutdown();

        getLogger().info("SkyblockCore has been disabled!");
    }

    public static SkyblockCore getInstance() {
        return instance;
    }

    public DatabaseManager getDatabaseManager() {
        return databaseManager;
    }

    public UserManager getUserManager() {
        return userManager;
    }

    public IslandManager getIslandManager() {
        return islandManager;
    }

    public GangManager getGangManager() {
        return gangManager;
    }

    public EnchantManager getEnchantManager() {
        return enchantManager;
    }

    public ItemManager getItemManager() {
        return itemManager;
    }

    public TileManager getTileManager() {
        return tileManager;
    }

    public PetManager getPetManager() {
        return petManager;
    }

    public ScoreboardManager getScoreboardManager() {
        return scoreboardManager;
    }
}
