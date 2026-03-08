package dev.skyblock.enchants;

import dev.skyblock.SkyblockCore;
import dev.skyblock.enchants.sword.Lifesteal;
import dev.skyblock.enchants.sword.Blind;
import dev.skyblock.enchants.sword.DeathBringer;
import dev.skyblock.enchants.sword.Gooey;
import dev.skyblock.enchants.sword.Ram;
import dev.skyblock.enchants.sword.Poison;
import dev.skyblock.enchants.sword.DisarmProtection;
import dev.skyblock.enchants.sword.IceAspect;
import dev.skyblock.enchants.block.Explosion;
import dev.skyblock.enchants.sword.CripplingStrike;
import dev.skyblock.enchants.sword.Vampire;
import dev.skyblock.enchants.sword.DeepWounds;
import dev.skyblock.enchants.sword.Charge;
import dev.skyblock.enchants.sword.Aerial;
import dev.skyblock.enchants.sword.Wither;
import dev.skyblock.enchants.sword.Disarm;
import dev.skyblock.enchants.sword.Backstabber;
import dev.skyblock.enchants.sword.Brawler;
import dev.skyblock.enchants.sword.Wizardly;
import dev.skyblock.enchants.sword.Disorder;
import dev.skyblock.enchants.sword.DisorderProtection;
import dev.skyblock.enchants.sword.Thunderbolt;
import dev.skyblock.enchants.sword.Disarmor;
import dev.skyblock.enchants.sword.Detonate;
import dev.skyblock.enchants.sword.Witch;
import dev.skyblock.enchants.sword.Chisel;
import dev.skyblock.enchants.sword.OverPower;
import dev.skyblock.enchants.sword.MobSlayer;
import dev.skyblock.enchants.sword.Smasher;
import dev.skyblock.enchants.sword.Potshot;
import dev.skyblock.enchants.sword.Serpent;
import dev.skyblock.enchants.sword.SoulSnatcher;
import dev.skyblock.enchants.armor.Molten;
import dev.skyblock.enchants.armor.Enlighten;
import dev.skyblock.enchants.armor.Poisoned;
import dev.skyblock.enchants.armor.Frozen;
import dev.skyblock.enchants.armor.Shielded;
import dev.skyblock.enchants.armor.Cursed;
import dev.skyblock.enchants.armor.Endershift;
import dev.skyblock.enchants.armor.Berserker;
import dev.skyblock.enchants.armor.Gears;
import dev.skyblock.enchants.armor.Implants;
import dev.skyblock.enchants.armor.Virtuous;
import dev.skyblock.enchants.armor.Glowing;
import dev.skyblock.enchants.armor.DisarmorProtection;
import dev.skyblock.enchants.armor.Dispatch;
import dev.skyblock.enchants.armor.Tank;
import dev.skyblock.enchants.armor.Protector;
import dev.skyblock.enchants.armor.Bloom;
import dev.skyblock.enchants.armor.Antidote;
import dev.skyblock.enchants.armor.Deflate;
import dev.skyblock.enchants.armor.LifeShield;
import dev.skyblock.enchants.armor.Sharingan;
import dev.skyblock.enchants.armor.Inspirit;
import dev.skyblock.enchants.armor.DoubleJump;
import dev.skyblock.enchants.block.Smelting;
import dev.skyblock.enchants.block.Quickening;
import dev.skyblock.enchants.block.Woodcutter;
import dev.skyblock.enchants.block.Firma;
import dev.skyblock.enchants.block.Lucky;
import dev.skyblock.enchants.block.Booster;
import dev.skyblock.enchants.block.Blessing;
import dev.skyblock.enchants.block.Replanter;
import dev.skyblock.enchants.block.LuckOfTheSky;
import dev.skyblock.enchants.block.Tinkerer;
import dev.skyblock.enchants.block.Devour;
import dev.skyblock.enchants.block.Karma;
import dev.skyblock.enchants.block.Barter;
import dev.skyblock.enchants.block.Prosperity;
import dev.skyblock.enchants.block.Insurance;
import dev.skyblock.enchants.block.Expansioner;
import dev.skyblock.enchants.block.Alchemy;
import dev.skyblock.enchants.bow.Healing;
import dev.skyblock.enchants.bow.Paralyze;
import dev.skyblock.enchants.bow.Piercing;
import dev.skyblock.enchants.bow.Shuffle;
import dev.skyblock.enchants.bow.Launcher;
import dev.skyblock.enchants.touch.Demolisher;
import dev.skyblock.enchants.touch.Scythe;
import org.bukkit.inventory.ItemStack;
import java.util.HashMap;
import java.util.Map;

public class EnchantManager {
    private final SkyblockCore plugin;
    private final Map<Integer, BaseEnchant> enchants = new HashMap<>();

    public EnchantManager(SkyblockCore plugin) {
        this.plugin = plugin;
        registerAll();
    }

    private void registerAll() {
        // Sword
        register(new Lifesteal());
        register(new Blind());
        register(new DeathBringer());
        register(new Gooey());
        register(new Ram());
        register(new Poison());
        register(new DisarmProtection());
        register(new IceAspect());
        register(new Explosion());     // note: this is a sword enchant in original
        register(new CripplingStrike());
        register(new Vampire());
        register(new DeepWounds());
        register(new Charge());
        register(new Aerial());
        register(new Wither());
        register(new Disarm());
        register(new Backstabber());
        register(new Brawler());
        register(new Wizardly());
        register(new Disorder());
        register(new DisorderProtection());
        register(new Thunderbolt());
        register(new Disarmor());
        register(new Detonate());
        register(new Witch());
        register(new Chisel());
        register(new OverPower());
        register(new MobSlayer());
        register(new Smasher());
        register(new Potshot());
        register(new Serpent());
        register(new SoulSnatcher());

        // Armor
        register(new Molten());
        register(new Enlighten());
        register(new Poisoned());
        register(new Frozen());
        register(new Shielded());
        register(new Cursed());
        register(new Endershift());
        register(new Berserker());
        register(new Gears());
        register(new Implants());
        register(new Virtuous());
        register(new Glowing());
        // register(new DisorderProtection());  // already registered under sword
        register(new DisarmorProtection());
        register(new Dispatch());
        register(new Tank());
        register(new Protector());
        register(new Bloom());
        register(new Antidote());
        register(new Deflate());
        register(new LifeShield());
        register(new Sharingan());
        register(new Inspirit());
        register(new DoubleJump());

        // Block
        register(new Smelting());
        register(new Quickening());
        register(new Woodcutter());
        register(new Firma());
        register(new Lucky());
        register(new Booster());
        register(new Blessing());
        register(new Replanter());
        register(new LuckOfTheSky());
        register(new Tinkerer());
        register(new Devour());
        register(new Karma());
        register(new Barter());
        register(new Prosperity());
        register(new Insurance());
        register(new Expansioner());
        register(new Alchemy());

        // Bow
        register(new Healing());
        register(new Paralyze());
        register(new Piercing());
        register(new Shuffle());
        register(new Launcher());

        // Touch
        register(new Demolisher());
        register(new Scythe());

        plugin.getLogger().info("Registered " + enchants.size() + " custom enchants.");
    }

    private void register(BaseEnchant enchant) {
        if (enchants.containsKey(enchant.getId())) {
            plugin.getLogger().warning("Duplicate enchant ID " + enchant.getId() +
                " for " + enchant.getName() + " (already registered as " +
                enchants.get(enchant.getId()).getName() + ") — skipping.");
            return;
        }
        enchants.put(enchant.getId(), enchant);
    }

    public void shutdown() {
    }

    public int getEnchantLevel(ItemStack item, int id) {
        BaseEnchant enchant = enchants.get(id);
        return enchant != null ? enchant.getLevel(item) : 0;
    }
}
