<?php


namespace SkyBlock\enchants;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use SkyBlock\enchants\armor\Antidote;
use SkyBlock\enchants\armor\Berserker;
use SkyBlock\enchants\armor\Bloom;
use SkyBlock\enchants\armor\Cursed;
use SkyBlock\enchants\armor\Deflate;
use SkyBlock\enchants\armor\DisarmorProtection;
use SkyBlock\enchants\armor\Dispatch;
use SkyBlock\enchants\armor\DoubleJump;
use SkyBlock\enchants\armor\effect\Bolt;
use SkyBlock\enchants\armor\effect\Flashlight;
use SkyBlock\enchants\armor\effect\Horde;
use SkyBlock\enchants\armor\effect\Inferno;
use SkyBlock\enchants\armor\effect\Infuriate;
use SkyBlock\enchants\armor\effect\Jumper;
use SkyBlock\enchants\armor\effect\Snorkel;
use SkyBlock\enchants\armor\Endershift;
use SkyBlock\enchants\armor\Enlighten;
use SkyBlock\enchants\armor\Frozen;
use SkyBlock\enchants\armor\Gears;
use SkyBlock\enchants\armor\Glowing;
use SkyBlock\enchants\armor\Implants;
use SkyBlock\enchants\armor\Inspirit;
use SkyBlock\enchants\armor\LifeShield;
use SkyBlock\enchants\armor\Molten;
use SkyBlock\enchants\armor\Poisoned;
use SkyBlock\enchants\armor\Protector;
use SkyBlock\enchants\armor\Sharingan;
use SkyBlock\enchants\armor\Shielded;
use SkyBlock\enchants\armor\Tank;
use SkyBlock\enchants\armor\Virtuous;
use SkyBlock\enchants\block\Barter;
use SkyBlock\enchants\block\Blessing;
use SkyBlock\enchants\block\Booster;
use SkyBlock\enchants\block\Devour;
use SkyBlock\enchants\block\Explosion;
use SkyBlock\enchants\block\Firma;
use SkyBlock\enchants\block\Insurance;
use SkyBlock\enchants\block\Karma;
use SkyBlock\enchants\block\LuckOfTheSky;
use SkyBlock\enchants\block\Lucky;
use SkyBlock\enchants\block\Prosperity;
use SkyBlock\enchants\block\Quickening;
use SkyBlock\enchants\block\Replanter;
use SkyBlock\enchants\block\Smelting;
use SkyBlock\enchants\block\Tinkerer;
use SkyBlock\enchants\block\Woodcutter;
use SkyBlock\enchants\bow\player\Healing;
use SkyBlock\enchants\bow\player\Launcher;
use SkyBlock\enchants\bow\player\Paralyze;
use SkyBlock\enchants\bow\player\Piercing;
use SkyBlock\enchants\bow\player\Shuffle;
use SkyBlock\enchants\bow\shoot\Molotov;
use SkyBlock\enchants\bow\shoot\Volley;
use SkyBlock\enchants\sword\Aerial;
use SkyBlock\enchants\sword\Backstabber;
use SkyBlock\enchants\sword\Blind;
use SkyBlock\enchants\sword\Brawler;
use SkyBlock\enchants\sword\Charge;
use SkyBlock\enchants\sword\Chisel;
use SkyBlock\enchants\sword\CripplingStrike;
use SkyBlock\enchants\sword\DeathBringer;
use SkyBlock\enchants\sword\DeepWounds;
use SkyBlock\enchants\sword\Detonate;
use SkyBlock\enchants\sword\Disarm;
use SkyBlock\enchants\sword\Disarmor;
use SkyBlock\enchants\sword\DisarmProtection;
use SkyBlock\enchants\sword\Disorder;
use SkyBlock\enchants\sword\DisorderProtection;
use SkyBlock\enchants\sword\Gooey;
use SkyBlock\enchants\sword\IceAspect;
use SkyBlock\enchants\sword\Lifesteal;
use SkyBlock\enchants\sword\MobSlayer;
use SkyBlock\enchants\sword\OverPower;
use SkyBlock\enchants\sword\Poison;
use SkyBlock\enchants\sword\Potshot;
use SkyBlock\enchants\sword\Ram;
use SkyBlock\enchants\sword\Serpent;
use SkyBlock\enchants\sword\Smasher;
use SkyBlock\enchants\sword\SoulSnatcher;
use SkyBlock\enchants\sword\Thunderbolt;
use SkyBlock\enchants\sword\Vampire;
use SkyBlock\enchants\sword\Witch;
use SkyBlock\enchants\sword\Wither;
use SkyBlock\enchants\sword\Wizardly;
use SkyBlock\enchants\touch\Scythe;
use SkyBlock\enchants\vanilla\VanillaEnchantFactory;
use SkyBlock\Main;

class EnchantFactory {
    /** @var Main */
    private Main $plugin;
    /** @var array */
    private array $enchants = [];

    /**
     * EnchantFactory constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->setEnchants();
        $this->setVanillaEnchants();
        $this->setVaulted();
    }

    public function setVanillaEnchants() : void {
        $ef = new VanillaEnchantFactory($this->plugin);
        $ef->init();
    }

    public function getMaxCELevel() : int {
        return 10;
    }

    public function setVaulted() : void {
        $this->plugin->vaulted = $this->plugin->common->get('vaulted'); // CE ids in /vaulted <ce>
    }

    public function init() : void {
        foreach ($this->enchants as $id => $data) {
            $enchant = new $data[6]($this->plugin, $data[0], 3, 10);
            EnchantmentIdMap::getInstance()->register($id, $enchant);
            StringToEnchantmentParser::getInstance()->register($data[0], fn() => $enchant);
        }
        $this->plugin->getServer()->getLogger()->info("§f=> §eRegistered §a" . count($this->enchants) . " §ecustom enchants! §f<=");
    }

    /**
     * @return array
     */
    public function getEnchants() : array {
        return $this->enchants;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function getTypeEnchants(string $type = "common") : array {
        $type = strtolower($type);
        $enchants = [];
        foreach ($this->enchants as $data) {
            if (strtolower($data[4]) == $type) {
                $enchants[] = $data[0];
            }
        }
        return $enchants;
    }

    /**
     * @param string $name
     *
     * @return int|null
     */
    public function getIdByEnchantName(string $name) : ?int {
        foreach ($this->enchants as $id => $data) {
            if (strtolower($name) == strtolower($data[0])) {
                return $id;
            }
        }
        return null;
    }

    /**
     * @param string $name
     *
     * @return array|null
     */
    public function getEnchantmentByName(string $name) : ?array {
        foreach ($this->enchants as $data) {
            if (strtolower($name) === strtolower($data[0])) {
                return $data;
            }
        }
        return null;
    }

    /**
     * @return array
     */
    public function getUniqueTypes() : array {
        $types = [];
        foreach ($this->enchants as $data) {
            if (!in_array($data[4], $types, true)) {
                $types[] = $data[4];
            }
        }
        return $types;
    }

    public function setEnchants() : void {
        $this->enchants = [
            100 => ["Lifesteal", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Rare', 'Sword', 'Takes life from enemy and adds it to you! §eCategory', Lifesteal::class], // CEs
            101 => ["Blind", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Rare', 'Sword', 'Gives Blindness to enemy when activated', Blind::class],
            102 => ["Deathbringer", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Rare', 'Sword', 'Increases Damage per hit by 1.2 when activated', DeathBringer::class],
            103 => ["Gooey", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_AXE, 'Rare', 'Axe', 'Freezes your enemy in place!', Gooey::class],
            104 => ["Poison", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Common', 'Sword', 'Gives Poison to enemy when activated', Poison::class],
            105 => ["DisarmProtection", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Legendary', 'Sword', 'Saves the weapon from getting Disarmed, level doesnt matter', DisarmProtection::class],
            106 => ["IceAspect", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Common', 'Sword', 'Gives Weakness and blindness to enemy when activated', IceAspect::class],
            107 => ["Explosion", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_PICKAXE, 'Legendary', 'Pickaxe', 'Mines ores connected to that ore when activated', Explosion::class],
            108 => ["CripplingStrike", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Common', 'Sword', 'Gives Hunger and weakness to enemy when activated', CripplingStrike::class],
            109 => ["Vampire", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Common', 'Sword', 'Increases your health when activated', Vampire::class],
            110 => ["DeepWounds", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Rare', 'Sword', 'Gives Nausea to enemy when activated', DeepWounds::class],
            111 => ["Charge", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Rare', 'Sword', 'Get Strength when activated', Charge::class],
            112 => ["Aerial", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Rare', 'Sword', 'Increases damage if in air', Aerial::class],
            113 => ["Wither", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Common', 'Sword', 'Gives Mining Fatigue and Nausea to enemy when activated', Wither::class],
            114 => ["Disarm", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Legendary', 'Sword', 'Drops the held item of enemy on ground, if the item doesnt have Disarm protection', Disarm::class],
            115 => ["Smelting", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_PICKAXE, 'Rare', 'Pickaxe', 'Automatically smelts drops when activated', Smelting::class],
            116 => ["Quickening", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_PICKAXE, 'Common', 'Pickaxe', 'Gives Haste when activated', Quickening::class],
            117 => ["Paralyze", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_BOW, 'Legendary', 'Bow', 'Gives Weakness to enemy and Blinds enemy when activated!', Paralyze::class],
            118 => ["Molotov", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_BOW, 'Rare', 'Bow', 'Set enemy on fire when activated!', Molotov::class],
            119 => ["Volley", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_BOW, 'Rare', 'Bow', 'Gives you health and strength when activated!', Volley::class],
            120 => ["Piercing", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_BOW, 'Common', 'Bow', 'Increases damage by 2 when activated!', Piercing::class],
            121 => ["Shuffle", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_BOW, 'Rare', 'Bow', 'Switch position with enemy when activated!', Shuffle::class],
            122 => ["Healing", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_BOW, 'Rare', 'Bow', 'Increases health of target by 1.5 when activated! Works always', Healing::class],
            123 => ["Molten", BaseEnchantment::TYPE_ARMOR_INVENTORY, BaseEnchantment::ITEM_TYPE_ARMOR, 'Rare', 'Armor', 'Sets damager on fire when activated', Molten::class],
            124 => ["Enlighten", BaseEnchantment::TYPE_ARMOR_INVENTORY, BaseEnchantment::ITEM_TYPE_ARMOR, 'Rare', 'Armor', 'Increase your health when activated', Enlighten::class],
            125 => ["Poisoned", BaseEnchantment::TYPE_ARMOR_INVENTORY, BaseEnchantment::ITEM_TYPE_ARMOR, 'Rare', 'Armor', 'Gives Poison to damager when activated', Poisoned::class],
            126 => ["Frozen", BaseEnchantment::TYPE_ARMOR_INVENTORY, BaseEnchantment::ITEM_TYPE_ARMOR, 'Common', 'Armor', 'Gives Mining Fatigue and Blindness to damager when activated', Frozen::class],
            127 => ["Shielded", BaseEnchantment::TYPE_ARMOR_INVENTORY, BaseEnchantment::ITEM_TYPE_ARMOR, 'Common', 'Armor', 'Gives Resistance to you when activated', Shielded::class],
            128 => ["Cursed", BaseEnchantment::TYPE_ARMOR_INVENTORY, BaseEnchantment::ITEM_TYPE_ARMOR, 'Common', 'Armor', 'Gives Wither to damager when activated', Cursed::class],
            129 => ["Endershift", BaseEnchantment::TYPE_ARMOR_INVENTORY, BaseEnchantment::ITEM_TYPE_ARMOR, 'Common', 'Armor', 'Gives Speed and extra health to you when activated', Endershift::class],
            130 => ["Berserker", BaseEnchantment::TYPE_ARMOR_INVENTORY, BaseEnchantment::ITEM_TYPE_ARMOR, 'Common', 'Armor', 'Gives Strength to you when low on health if activated', Berserker::class],
            131 => ["Gears", BaseEnchantment::TYPE_ARMOR_INVENTORY, BaseEnchantment::ITEM_TYPE_ARMOR, 'Legendary', 'Armor', 'Gives Speed to you when you get hit', Gears::class],
            133 => ["Implants", BaseEnchantment::TYPE_ARMOR_INVENTORY, BaseEnchantment::ITEM_TYPE_ARMOR, 'Legendary', 'Armor', 'Feeds you when you get hit', Implants::class],
            134 => ["Glowing", BaseEnchantment::TYPE_ARMOR_INVENTORY, BaseEnchantment::ITEM_TYPE_ARMOR, 'Legendary', 'Armor', 'Gives Night Vision to you when you get hit', Glowing::class],
            135 => ["DisorderProtection", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Legendary', 'Sword', 'Saves from Disorder CE', DisorderProtection::class],
            136 => ["Woodcutter", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_AXE, 'Legendary', 'Axe', 'Mines logs connected to that log when broken', Woodcutter::class],
            137 => ["Firma", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_AXE, 'Legendary', 'Axe', 'Mines melons/pumpkins connected to that melon/pumpkin when activated', Firma::class],
            138 => ["Lucky", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_PICKAXE, 'Legendary', 'Pickaxe', 'Changes the tier of the block if activated! Chance depends on luck!', Lucky::class],
            139 => ["Booster", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_PICKAXE, 'Common', 'Pickaxe', 'Gives speed when activated!', Booster::class],
            140 => ["Virtuous", BaseEnchantment::TYPE_ARMOR_INVENTORY, BaseEnchantment::ITEM_TYPE_ARMOR, 'Rare', 'Armor', 'Removes bad effects when activated! Number of effects removed depends on level!', Virtuous::class],
            141 => ["DisarmorProtection", BaseEnchantment::TYPE_ARMOR_INVENTORY, BaseEnchantment::ITEM_TYPE_ARMOR, 'Rare', 'Armor', 'Protects the armor item from getting disarmored!', DisarmorProtection::class],
            142 => ["Witch", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Rare', 'Sword', 'Removes good health effects from enemy when activated! Number of effects removed depends on level!', Witch::class],
            143 => ["Backstabber", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Legendary', 'Sword', 'Deal more damage to enemy from behind!', Backstabber::class],
            144 => ["Dispatch", BaseEnchantment::TYPE_CHESTPLATE, BaseEnchantment::ITEM_TYPE_CHESTPLATE, 'Legendary', 'Chestplate', 'Explodes a tnt after you die!', Dispatch::class],
            145 => ["Tank", BaseEnchantment::TYPE_ARMOR_INVENTORY, BaseEnchantment::ITEM_TYPE_ARMOR, 'Legendary', 'Armor', 'Lessens the knockback! Works always. The highest level of Tank from your armor set is applied', Tank::class],
            146 => ["Protector", BaseEnchantment::TYPE_ARMOR_INVENTORY, BaseEnchantment::ITEM_TYPE_ARMOR, 'Legendary', 'Armor', 'Lessens sword damage by enemy on you!', Protector::class],
            147 => ["Bloom", BaseEnchantment::TYPE_ARMOR_INVENTORY, BaseEnchantment::ITEM_TYPE_ARMOR, 'Legendary', 'Armor', 'Grow in size at sneak! Must be wearing full Bloom armor. Only works on Islands', Bloom::class],
            149 => ["Antidote", BaseEnchantment::TYPE_CHESTPLATE, BaseEnchantment::ITEM_TYPE_CHESTPLATE, 'Rare', 'Chestplate', 'Immunity to poison, poison wont be added by any CE! Works always', Antidote::class],
            150 => ["Snorkel", BaseEnchantment::TYPE_HELMET, BaseEnchantment::ITEM_TYPE_HELMET, 'Legendary', 'Helmet', 'Unlimited water breathing! Works always', Snorkel::class],
            151 => ["Flashlight", BaseEnchantment::TYPE_HELMET, BaseEnchantment::ITEM_TYPE_HELMET, 'Legendary', 'Helmet', 'Unlimited nightvision! Works always', Flashlight::class],
            152 => ["Infuriate", BaseEnchantment::TYPE_CHESTPLATE, BaseEnchantment::ITEM_TYPE_CHESTPLATE, 'Legendary', 'Chestplate', 'Unlimited strength! Works always', Infuriate::class],
            153 => ["Bolt", BaseEnchantment::TYPE_LEGGINGS, BaseEnchantment::ITEM_TYPE_LEGGINGS, 'Legendary', 'Leggings', 'Unlimited speed! Works always', Bolt::class],
            154 => ["Horde", BaseEnchantment::TYPE_CHESTPLATE, BaseEnchantment::ITEM_TYPE_CHESTPLATE, 'Legendary', 'Chestplate', 'Unlimited resistance! Works always', Horde::class],
            155 => ["Inferno", BaseEnchantment::TYPE_LEGGINGS, BaseEnchantment::ITEM_TYPE_LEGGINGS, 'Legendary', 'Leggings', 'Unlimited fire resistance! Works always', Inferno::class],
            156 => ["Deflate", BaseEnchantment::TYPE_ARMOR_INVENTORY, BaseEnchantment::ITEM_TYPE_ARMOR, 'Legendary', 'Armor', 'Shrink in size at sneak! Must be wearing full Deflate armor. Only works on Islands', Deflate::class],
            157 => ["Scythe", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_HOE, 'Legendary', 'Hoe', 'Trims grass into farmland connected to that grass when activated!', Scythe::class],
            158 => ["LuckOfTheSky", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_TOOLS, 'Legendary', 'Tool', 'Increases Chance to get Relics while mining ores depending on CE Level', LuckOfTheSky::class],
            159 => ["OverPower", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Legendary', 'Sword', 'Does extra damage on mobs, Level 10 does double damage! Works always!', OverPower::class],
            160 => ["Jumper", BaseEnchantment::TYPE_LEGGINGS, BaseEnchantment::ITEM_TYPE_LEGGINGS, 'Legendary', 'Leggings', 'Unlimited jump! Works only at islands!', Jumper::class],
            161 => ["Launcher", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_BOW, 'Legendary', 'Bow', 'Knocks back enemy into air when activated!', Launcher::class],
            175 => ["Serpent", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Exclusive', 'Sword', 'Knocks back, sets enemy on fire, gives Wither to enemy with a huge explosion when activated! Disarm Protection included', Serpent::class], // EXCLUSIVES
            176 => ["Brawler", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Exclusive', 'Sword', 'Knocks back, sets enemy on fire, gives hunger and weakness to enemy with flames when activated! Disarm Protection included', Brawler::class],
            177 => ["Wizardly", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Exclusive', 'Sword', 'Knocks back, makes you invisible, gives nausea to enemy with a huge explosion when activated! Disarm Protection included', Wizardly::class],
            178 => ["Disorder", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Exclusive', 'Sword', 'Shuffle the sword and an item in enemys inventory, and gives Wither to enemy! Disarm Protection included', Disorder::class],
            179 => ["Thunderbolt", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Exclusive', 'Sword', 'Strikes lightning damaging everyone within 5 block radius, and gives Nausea to enemy! Disarm Protection included', Thunderbolt::class],
            180 => ["Disarmor", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Exclusive', 'Sword', 'Drops one armor item of enemy on ground if theres no disarmor protection on it! Disarm Protection included', Disarmor::class],
            181 => ["Detonate", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Exclusive', 'Sword', 'Spawns tnt on hit when activated and gives Nausea to enemy! Disarm Protection included', Detonate::class],
            //            182 => ["Demolisher", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_PICKAXE, 'Exclusive', 'Pickaxe', 'Breaks Bedrock, works everytime', Demolisher::class],
            183 => ["Barter", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_TOOLS, 'Exclusive', 'Tool', 'Auto Sell sellable items for Money$, This overrides Tinkerer CE', Barter::class],
            184 => ["Tinkerer", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_TOOLS, 'Exclusive', 'Tool', 'Auto Sell sellable items for XP', Tinkerer::class],
            185 => ["Devour", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_TOOLS, 'Exclusive', 'Tool', 'Auto Feeds you when you are Mining', Devour::class],
            186 => ["LifeShield", BaseEnchantment::TYPE_CHESTPLATE, BaseEnchantment::ITEM_TYPE_CHESTPLATE, 'Exclusive', 'Chestplate', 'Blocks LifeSteal any level Enchant when activated', LifeShield::class],
            187 => ["Karma", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_TOOLS, 'Exclusive', 'Tool', 'Get more Mana while mining ores or farming, works everytime', Karma::class],
            188 => ["Sharingan", BaseEnchantment::TYPE_HELMET, BaseEnchantment::ITEM_TYPE_HELMET, 'Exclusive', 'Helmet', 'Immune to Blindness effect, works everytime', Sharingan::class],
            189 => ["Chisel", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Exclusive', 'Sword', 'Get more MobCoins/XP while killing mobs, works everytime', Chisel::class],
            190 => ["MobSlayer", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Exclusive', 'Sword', 'Kills multiple spawner mobs when activated', MobSlayer::class],
            191 => ["Inspirit", BaseEnchantment::TYPE_HELMET, BaseEnchantment::ITEM_TYPE_HELMET, 'Exclusive', 'Helmet', 'Never get Nausea, works everytime', Inspirit::class],
            192 => ["Insurance", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_PICKAXE, 'Exclusive', 'Pickaxe', 'Auto fixes Broken Pickaxe if Fixer scroll exists in inventory, works everytime', Insurance::class],
            193 => ["DoubleJump", BaseEnchantment::TYPE_LEGGINGS, BaseEnchantment::ITEM_TYPE_LEGGINGS, 'Legendary', 'Leggings', 'Allows the user to double jump', DoubleJump::class],
            195 => ["SoulSnatcher", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, "Exclusive", "Sword", "Extremely rare chance to instantly kill your enemy!", SoulSnatcher::class],
            196 => ["Smasher", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_AXE, 'Exclusive', 'Axe', 'Does extra damage to enemy armor!', Smasher::class],
            197 => ["Ram", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_AXE, 'Rare', 'Axe', 'Increase knockback!', Ram::class],
            198 => ["Potshot", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_AXE, 'Exclusive', 'Axe', 'Throws everyone around you in the sky!', Potshot::class],
            //			199 => ["Alchemy", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_PICKAXE, "Exclusive", "Pickaxe", "Auto sell sellable items for Mana. This overrides Tinkerer and Barter", Alchemy::class],
            //			200 => ["Expansioner", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_TOOLS, "Exclusive", "Tool", "Get more island points while mining ores or farming, works everytime", Expansioner::class],
            201 => ["Replanter", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_HOE, "Legendary", "Tool", "Automatically replants fully grown crops if you have the items in your inventory!", Replanter::class],
            202 => ["Blessing", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_HOE, "Exclusive", "Tool", "Gain extra drops when destroying fully grown crops!", Blessing::class],
            203 => ["Prosperity", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_TOOLS, "Ancient", "Tool", "Increase the changes of getting a rarer relic when mining/farming!", Prosperity::class],
        ];
    }
}
