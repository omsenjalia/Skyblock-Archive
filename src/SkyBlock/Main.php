<?php

namespace SkyBlock;

use alvin0319\CustomItemLoader\block\BlockIds;
use alvin0319\CustomItemLoader\BlockMapper;
use alvin0319\CustomItemLoader\CustomItems;
use Common\Main as Common;
use Exception;
use Gandalf\Main as Gandalf;
use JsonException;
use PermsX\PermsAPI;
use pocketmine\block\{Block, BlockTypeIds, VanillaBlocks, WallSign};
use pocketmine\block\tile\{Chest, Container};
use pocketmine\console\ConsoleCommandSender;
use pocketmine\data\bedrock\block\BlockTypeNames;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\object\PrimedTNT;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\{Item, LegacyStringToItemParser, VanillaItems};
use pocketmine\lang\Language;
use pocketmine\math\{AxisAlignedBB, Vector3};
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\{CompoundTag, DoubleTag, FloatTag, ListTag, ShortTag};
use pocketmine\network\mcpe\protocol\AddActorPacket as AddEntityPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permissible;
use pocketmine\player\IPlayer;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\{Config, Random, TextFormat, TextFormat as TF};
use pocketmine\world\{generator\GeneratorManager, Position, World};
use pocketmine\world\particle\HugeExplodeSeedParticle;
use SkyBlock\block\BFactory;
use SkyBlock\chat\{ChatHandler, GangChatHandler};
use SkyBlock\command\CommandFactory;
use SkyBlock\command\Functions;
use SkyBlock\command\skyblock\SkyblockFactory;
use SkyBlock\db\Connector;
use SkyBlock\db\SQLite3;
use SkyBlock\enchants\EnchantFactory;
use SkyBlock\events\{PetRemoveEvent, PetSpawnEvent};
use SkyBlock\gang\GangManager;
use SkyBlock\generator\end\EndGenerator;
use SkyBlock\generator\nether\NetherGenerator;
use SkyBlock\generator\SkyBlockGeneratorManager;
use SkyBlock\island\IslandManager;
use SkyBlock\item\IFactory;
use SkyBlock\particle\ParticleManager;
use SkyBlock\perms\PermissionManager;
use SkyBlock\pets\BasePet;
use SkyBlock\pets\PetFactory;
use SkyBlock\skyblock\SkyBlockManager;
use SkyBlock\spawner\SpawnerFactory;
use SkyBlock\tasks\{BroadcastTask, DropItemsTask, DropPartyTask, EffectTask, EnvoyTask, MinuteTask, TimeCommand};
use SkyBlock\tiles\TileFactory;
use SkyBlock\UI\FormFunctions;
use SkyBlock\user\UserManager;
use SkyBlock\util\Util;
use SkyBlock\util\Values;
use SQLite3 as SQL;
use Staff\StaffAPI;

class Main extends PluginBase {

    public static string $joinMessage = "Catalysts have been changed. they now spawn blocks on top of them ONLY. They no longer need water.";

    private static Main $object;
    public string $sctitle = "";
    /** @var Arena */
    public Arena $arena;
    /** @var SkyblockFactory */
    public SkyblockFactory $sf;
    /**
     * @deprecated
     * @see Main::getStaffAPI()
     * */
    public StaffAPI $staffapi;
    public static StaffAPI $staffAPI;
    /** @var PermsAPI */
    public PermsAPI $permsapi;
    /** @var Gandalf */
    public Gandalf $gandalf;
    /** @var Common */
    public Common $commonapi;
    public array $rainbow = [], $rename = [], $trade_offers = [], $noDrop = [], $trades = [], $ic_tr_timer = [], $inv_full = [], $nether_invinc = [], $gangrename = [], $aucconfirm = [], $oregens = [], $bragauction = [], $donewar = [], $upd_touch = [];
    public array $using, $mined, $grew, $nofall, $notnt, $shrunk, $growremaining, $growcd, $shrinkremaining, $shrinkcd, $delete = [], $reset = [];
    public array $invitations = [], $autominer = [], $autoseller = [], $requests = [], $paymc = [], $payMana = [], $deflate = [], $bloom = [], $ginvitations = [], $chatsize = [], $resettime = [], $teleport = [], $updates = [];
    public SQL $db, $db4;
    public array $drain = [], $netherwarp = [], $scrolls = [], $spawners = [], $relics = [], $gkits = [], $envoys = [], $match = [], $sellchest = [], $icuc = [], $icuctime = [], $icucc = [], $icdc = [], $icdctime = [], $icdcc = [], $condensechest = [], $schest = [], $cchest = [], $interact = [];
    public array $deathmessages = [], $userIslandCache = [], $userHelperCache = [], $autosprint = [];
    public array $reply = [];
    public array $god = [];
    public static array $capes = [];
    public array $warps = [], $particles = [], $dropparty = [], $tutorial = [], $warzone = [], $wars = [], $enchants = [], $flycount = [];

    public array $ischatsize = [], $gchatsize = [];
    public bool $chatpack = false;
    /** @var Chair */
    private Chair $chair;

    //CEs
    public array $auctions = [], $pchests = [], $pay = [], $shopconfirm = [], $fireallconfirm = [], $kickalltimer = [], $fixalltimer = [];
    public int $kothnumber = 0;
    public int $clt = 60 * 8, $clearlagtime = 60 * 8; // clear lagg time, check commands.php as well
    const MAX_PLAYERS = 80;
    const MAX = 85;
    public int $season, $serverblocks;
    public array $allowed_cmds = [];
    public array $combat = [];
    /** @var ItemCloud[] */
    public array $clouds = [];
    public array $shops;
    public array $tap = [], $placeQueue = [], $keys;
    /** @var array */
    public array $randomItems = ['Items' => [306, 304, 313, 322, 278, 466]];
    public array $oreList = [];
    public array $buy = [];
    public $lightningPacket = null;
    public Config $items;
    /** @var Config */
    public Config $commands, $cfg, $count, $allowed, $bc, $shop, $join, $common, $arenaConf, $upd, $config;
    /** @var Kit[] $kits */
    public array $kits = [];
    public $msgs, $vote = 30;
    public array $players = [];
    public bool $vacant = true;
    public array $warreq = [];
    public array $war = [];
    public $warstart;
    public array $warplayers = [];
    public array $countdown = [], $killlog = [], $dontFollow = [];
    public string $server = "Skyblock";
    public bool $restarting = false, $loaded = false;
    public array $crops = [];
    public int $secs = 0;
    public array $tasks = [];
    public $status, $sbtype;
    public int $droppartyTimer = 0, $envoyTimer = 0;
    public array $playerPets = [];
    public array $pets = ["Bee", "Bat", "Hoglin", "Donkey", "Phantom", "EnderDragon", "Endermite", "Evoker", "Ghast", "Horse", "Llama", "MagmaCube", "Mooshroom", "Mule", "Ocelot", "Panda", "PolarBear", "Rabbit", "SkeletonHorse", "Slime", "SnowGolem", "Stray", "Vex", "Vindicator", "Witch", "Wither", "WitherSkeleton", "Wolf", "Fox", "ZombieHorse", "Chicken", "ZombieVillager", "Allay", "Axolotl", "ElderGuardian", "Frog", "Goat", "IronGolem", "Parrot", "SnowFox", "Turtle", "Blaze", "Cod", "Enderman", "Pig", "Pufferfish", "Salmon", "Sheep", "TropicalFish", "Warden", "Zombie", "GlowSquid", "Zoglin", "Cow"];
    public array $vaulted = [];
    protected array $enchantments = [];
    /** @var SkyBlockManager */
    private SkyBlockManager $skyBlockManager;
    /** @var IslandManager */
    private IslandManager $islandManager;
    /** @var Goal */
    private Goal $goalManager;
    /** @var Tag */
    private Tag $tagManager;
    /** @var GangManager */
    private GangManager $gangManager;
    /** @var UserManager */
    private UserManager $userManager;
    /** @var ParticleManager */
    private ParticleManager $particleManager;
    /** @var ChatHandler */
    private ChatHandler $chatHandler;
    /** @var GangChatHandler */
    private GangChatHandler $gangChatHandler;
    /** @var SQLITE3 */
    private SQLite3 $sqlite3;
    /** @var Functions */
    private Functions $functions;
    /** @var FormFunctions */
    private FormFunctions $formfunc;
    /** @var EvFunctions */
    private EvFunctions $evFunctions;
    /** @var PetProperties */
    public PetProperties $pProperties;
    /** @var EnchantFactory */
    public EnchantFactory $enchFactory;
    /** @var array */
    public array $os;
    public array $oreblocks = [];

    /**
     * @return Main
     */
    public static function getInstance() : Main {
        return self::$object;
    }

    public function onLoad() : void {
        $cmds = ["tell", "gamemode", "enchant", "effect", "time", "me", /*"give"*/];
        foreach ($cmds as $c) {
            $cmd = $this->getServer()->getCommandMap()->getCommand($c);
            if ($cmd !== null) $this->getServer()->getCommandMap()->unregister($cmd);
        }
        GeneratorManager::getInstance()->addGenerator(NetherGenerator::class, "nether", fn() => null, true);
        GeneratorManager::getInstance()->addGenerator(EndGenerator::class, "end", fn() => null, true);
        self::$object = $this;
    }

    /**
     * @param Player $player
     */
    public function transferToLobby(Player $player) : void {
        $player->transfer("142.44.142.194");
        $this->getLogger()->info("§a" . $player->getName() . " §eis transferring to §blobby");
    }

    /**
     * @param Player $player
     * @param string $server
     */
    public function transfer(Player $player, string $server) : void {
        $map = [
            "factions"  => 19136,
            "minigames" => 19133,
            "sb_red"    => 19134,
        ];
        $player->transfer("142.44.142.194", $map[$server]);
        $this->getLogger()->info("§a" . $player->getName() . " §eis transferring to §b" . $server);
    }

    public function onEnable() : void {
        $this->getServer()->getWhitelisted()->set(false);
        $this->getServer()->getWorldManager()->setAutoSave(true);
        $this->droppartyTimer = mt_rand(10, 44);
        $this->envoyTimer = mt_rand(3, 44);
        $this->initialize();
        PermissionManager::init();
        $this->setStaffApi();
        $this->setPermsApi();
        $this->setGandalf();
        $this->setCommon();
        $this->loadPositions();
        $this->loadAuctions();
        $this->loadTrades();
        $this->loadPrivateChests();
        $this->loadFlyCounter();
        $this->setOreList();
        $this->setCrops();
        $this->setOregens();
        $this->setSpawners();
        $this->setScrolls();
        $this->setCrateKeys();
        $this->setRelics();
        $this->setDeathMessages();
        $this->registerEntities();
        $this->registerItems();
        $this->registerTiles();
        $this->registerBlocks();
        $this->registerEnchantments();
        $this->setGoalManager();
        $this->setTagManager();
        $this->setSkyBlockGeneratorManager();
        $this->setSkyBlockManager();
        $this->setIslandManager();
        $this->setGangManager();
        $this->setUserManager();
        $this->setParticleManager();
        $this->getParticleManager()->setParticlesOnline();
        $this->setEvFunctions();
        $this->setFunctions();
        $this->setChatHandler();
        $this->setGangChatHandler();
        $this->setSqlite3();
        $this->getVotes();
        $this->registerCommands();
        $this->setCapes();
        $this->setEventListener();
        $this->checkPacks();
        $this->setMobArena();
        (new Connector())::init($this->config->get('database'));
        $this->loaded = true;
        $this->getLogger()->info(TextFormat::AQUA . "Skyblock by Infernus101 has been Enabled!");
        new Data();

    }

    public function setGandalf() : void {
        /** @var \Gandalf\Main|null $plugin */
        $plugin = $this->getServer()->getPluginManager()->getPlugin("Gandalf");
        if ($plugin !== null) {
            $this->gandalf = $plugin;
        }
    }

    public function setStaffApi() : void {
        /** @var \Staff\Main|null $plugin */
        $plugin = $this->getServer()->getPluginManager()->getPlugin("APIStaff");
        if ($plugin !== null) {
            $this->staffapi = $plugin->getApi();
            self::$staffAPI = $plugin->getApi();
        }
    }

    /**
     * @return StaffAPI
     */
    public static function getStaffAPI() : StaffAPI {
        return self::$staffAPI;
    }

    public function setPermsApi() : void {
        /** @var \PermsX\PermsX|null $plugin */
        $plugin = $this->getServer()->getPluginManager()->getPlugin("PermsX");
        if ($plugin !== null) {
            $this->permsapi = $plugin->getApi();
        }
    }

    public function setCommon() : void {
        /** @var \Common\Main|null $plugin */
        $plugin = $this->getServer()->getPluginManager()->getPlugin("Common");
        if ($plugin !== null) {
            $this->commonapi = $plugin;
        }
    }

    public function loadFlyCounter() : void {
        if (file_exists($this->getDataFolder() . "flycounter.sl")) {
            $this->flycount = unserialize(file_get_contents($this->getDataFolder() . "flycounter.sl"));
        }
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isTrusted(string $name) : bool {
        return in_array(strtolower($name), ["infern101", "dr derpwhiskers", "joshy3282", "c4lmpro"], true);
    }

    /**
     * @param int $count
     *
     * @return Item
     */
    public function getFireworkItem(int $count = 1) : Item {
        $fw = CustomItems::FIREWORK()->setCount($count);
        $fw->setCustomName("§r§a§lFireworks\n§r§fHold to use");
        return $fw;
    }

    public function isStringValid(string $string) : bool {
        $allowedchr = str_split(Values::ALLOWED);
        $messagearray = str_split($string);
        foreach ($messagearray as $word) {
            if (!in_array($word, $allowedchr, true)) return false;
        }
        return true;
    }

    public function saveFlyCounter() : void {
        foreach ($this->flycount as $key => $time) {
            if ((time() - $time) >= Values::FLY_TIME) unset($this->flycount[$key]);
        }
        file_put_contents($this->getDataFolder() . "flycounter.sl", serialize($this->flycount));
    }

    public function setTagManager() : void {
        $this->tagManager = new Tag($this);
    }

    /**
     * @return Tag
     */
    public function getTagManager() : Tag {
        return $this->tagManager;
    }

    public function setGoalManager() : void {
        $this->goalManager = new Goal($this);
    }

    /**
     * @return Goal
     */
    public function getGoalManager() : Goal {
        return $this->goalManager;
    }

    /**
     * @return EnchantFactory
     */
    public function getEnchantFactory() : EnchantFactory {
        return $this->enchFactory;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getPlayerOS(string $name) : string {
        return isset($this->os[$name]) ? Util::getOS($this->os[$name]) : "N/A";
    }

    public function setDeathMessages() : void {
        $this->deathmessages = [
            "§a{player1} §bgot rekt by §a{player2}",
            "§a{player1} §bgot tossed by §a{player2}",
            "§a{player1} §bwas oofed by §a{player2}",
            "§a{player1} §bgot nae naed on by §a{player2}",
            "§a{player1} §bgot clapped by §a{player2}",
            "§a{player1} §btook the L from §a{player2}",
            "§a{player1} §bgot roasted by §a{player2}",
            "§a{player1} §bwas smacked by §a{player2}",
            "§a{player1} §bwas bamboozled by §a{player2}",
            "§a{player1} §bgot spanked by §a{player2}",
            "§a{player1} §bgot railed by §a{player2}"
        ];
    }

    /**
     * @param string $victim
     * @param string $killer
     * @param bool   $empty
     */
    public function sendKillLog(string $victim = "", string $killer = "", bool $empty = false) : void {
        if ($victim != "" && $killer != "") {
            $msg = str_replace(["{player1}", "{player2}"], ["`" . $victim . "` :skull_crossbones:", "`" . $killer . "` :boom:"], $this->deathmessages[mt_rand(0, count($this->deathmessages) - 1)]) . " at " . date("M j, G:i:s T");
            $this->killlog[] = TF::clean($msg);
        }
        if (!$empty) {
            $sendat = 10;
            if (count($this->killlog) < $sendat) return;
        }
        if (!empty($this->killlog)) {
            $this->sendDiscordMessage(":crossed_swords:      Kill Logs      :crossed_swords:", implode("\n", $this->killlog) . "\n", 3);
            $this->killlog = [];
        }
    }

    public function loadPositions() : void {
        $arr = $this->common->getAll();
        $this->warps = $arr['warps'];
        $this->particles = $arr['particles'];
        $this->warzone = $arr['envoy'];
        $this->dropparty = $arr['dropparty'];
        $this->wars = $arr['war'];
        $this->sbtype = str_replace('\u00a7', '§', $this->common->get('name'));
        $this->sctitle = TextFormat::BOLD . "§3FT " . TextFormat::GREEN . "Sky" . TextFormat::YELLOW . "Block";
        //		$this->sctitle = "§l§bF§3T §dSB " . $this->sbtype;
        $this->season = $this->common->get('season');
    }

    /**
     * @param SBPlayer $player
     *
     * @return bool
     */
    public function isInCombat(Player $player) : bool {
        if (!isset($this->combat[$player->getName()])) return false;
        if ($this->combat[$player->getName()] >= time()) return true;
        else {
            unset($this->combat[$player->getName()]);
            return false;
        }
    }

    public function savePositions() : void {
        $this->common->set('vaulted', $this->vaulted);
        $this->common->set('warps', $this->warps);
        $this->common->set('particles', $this->particles);
        $this->common->set('envoy', $this->warzone);
        $this->common->set('dropparty', $this->dropparty);
        $this->common->set('war', $this->wars);
        $this->count->set('server-blocks', $this->serverblocks);
        try {
            $this->common->save();
            $this->count->save();
        } catch (JsonException) {
        }
    }

    public function initialize() : void {

        @mkdir($this->getDataFolder() . "kitcooldowns/");
        $this->saveResource("kothnumber.yml");
        $this->saveResource("updates.yml", true);
        $this->saveResource("pchest.json");
        $this->saveResource("tags.json");
        $this->saveResource("count.yml");
        $this->saveResource("common_settings.json");
        $this->saveResource("join.yml");
        $this->saveResource("Shops.yml");
        $this->saveResource("auctions.sl");
        $this->saveResource("trades.sl");
        $this->saveResource("config.yml");
        $this->saveResource("allowed.yml", true);
        $this->saveResource("shop.yml", true);
        $this->saveResource("sell.yml", true);
        $this->saveResource("kits.yml", true);
        $this->saveResource("commands.yml", true);
        $this->saveResource("broadcaster.yml", true);
        $this->saveResource("items.json", true);

        $this->loadItemCloud();
        $this->count = new Config($this->getDataFolder() . "count.yml", Config::YAML);
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->serverblocks = $this->count->get("server-blocks");
        $this->upd = new Config($this->getDataFolder() . "updates.yml", Config::YAML);
        $this->updates = $this->upd->getAll();
        $this->arenaConf = new Config($this->getDataFolder() . "arena.json", Config::JSON);
        $this->common = new Config($this->getDataFolder() . "common_settings.json", Config::JSON);
        $this->items = new Config($this->getDataFolder() . "items.json", Config::JSON);
        $conf = new Config($this->getDataFolder() . "kothnumber.yml", Config::YAML);
        $this->kothnumber = $conf->get("number");
        $all = $this->arenaConf->getAll();
        if (isset($all["spawns"]) && $all["c1"] && $all["c2"]) {
            $this->arena = new Arena($this, $all["spawns"], ["c1" => $all["c1"], "c2" => $all["c2"]]);
            $this->getLogger()->info("KOTH Arena Loaded Successfully");
        } else {
            $this->getLogger()->alert("No arena setup! Please set one up!");
        }
        $this->loadKits();
        $this->setTime();
        $this->fetchTutorial();
        $this->cfg = new Config($this->getDataFolder() . "sell.yml", Config::YAML);
        $this->allowed = new Config($this->getDataFolder() . "allowed.yml", Config::YAML);
        $this->commands = new Config($this->getDataFolder() . "commands.yml", Config::YAML);
        $this->bc = new Config($this->getDataFolder() . "broadcaster.yml", Config::YAML);
        $shops = new Config($this->getDataFolder() . "Shops.yml", Config::YAML);
        $this->shop = new Config($this->getDataFolder() . "shop.yml", Config::YAML);
        $this->join = new Config($this->getDataFolder() . "join.yml", Config::YAML);
        $this->initDb();
        $this->shops = $shops->getAll();
        $this->allowed_cmds = $this->allowed->get("allowed-commands");
        $msg = $this->join->getAll();
        $this->msgs = $msg["joinmessages"];
        $this->loadCommands();
        $this->loadGKits();
        $this->getScheduler()->scheduleDelayedRepeatingTask(new EffectTask($this), 5 * 20, 5 * 20);
        $this->getScheduler()->scheduleDelayedTask(new EnvoyTask($this), $this->envoyTimer * 60 * 20);
        /** Code for merchant spawner to spawn in at random times (not done) */
        //$this->getScheduler()->scheduleDelayedTask(new MerchantTask($this,new Merchant($this)), 20*15);
        $this->getScheduler()->scheduleDelayedRepeatingTask(new MinuteTask($this), 1200, 1200);
        $this->getScheduler()->scheduleRepeatingTask(new BroadcastTask(), 2 * 60 * 20);
        $this->getScheduler()->scheduleDelayedTask(new DropPartyTask($this), $this->droppartyTimer * 60 * 20);
        $this->getScheduler()->scheduleRepeatingTask(new DropItemsTask($this), 5 * 20);
    }

    public function fetchTutorial() : void {
        try {
            $op = @file_get_contents("http://play.fallentech.io/tutorial/" . $this->common->get('tutorial-folder', 'sb_latest') . "/tutorial.json");
            if ($op === false or $op === null or $op === "") return;
            $this->tutorial = json_decode($op, true);
        } catch (Exception) {
        }
    }

    public function loadItemCloud() : void {
        if (!is_file($this->getDataFolder() . "ItemCloud.dat")) {
            file_put_contents($this->getDataFolder() . "ItemCloud.dat", serialize([]));
        }
        $data = unserialize(file_get_contents($this->getDataFolder() . "ItemCloud.dat"));
        foreach ($data as $datam) {
            $this->clouds[$datam[1]] = new ItemCloud($datam[1], $datam[0]);
        }
    }

    public function loadKits() : void {
        $file = "kits.sl";
        $kitsData = yaml_parse_file($this->getDataFolder() . "kits.yml");
        $coolDowns = [];
        if (file_exists($this->getDataFolder() . "kitcooldowns/" . $file)) {
            $coolDowns = unserialize(file_get_contents($this->getDataFolder() . "kitcooldowns/" . $file));
        }
        foreach ($kitsData as $kitName => $kitData) {
            $data = [];
            if (!isset($coolDowns[$kitName])) $coolDowns[$kitName] = [];
            else $data = $coolDowns[$kitName];
            $this->kits[$kitName] = new Kit($this, $kitData, $kitName, $data);
        }
    }

    public function saveKits() : void {
        $coolDowns = [];
        foreach ($this->kits as $kit) {
            $coolDowns[$kit->getName()] = $kit->pending;
        }
        file_put_contents($this->getDataFolder() . "kitcooldowns/kits.sl", serialize($coolDowns));
    }

    public function setTime() : void {
        foreach (Values::SERVER_WORLDS as $worldName) {
            if (($world = $this->getServer()->getWorldManager()->getWorldByName($worldName)) !== null) {
                $world->setTime(1000);
                $world->stopTime();
                $world->setDifficulty(World::DIFFICULTY_HARD);
            }
        }
    }

    public function initDb() : void {
        $this->db = new SQL($this->getDataFolder() . "skyblock.db"); // TODO: update renameIsland & delIsland in db too
        $this->db->exec("CREATE TABLE IF NOT EXISTS island (name TEXT PRIMARY KEY, world TEXT COLLATE NOCASE);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS bank (name TEXT PRIMARY KEY, money INT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS expansion (name TEXT PRIMARY KEY, radius INT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS motd (name TEXT PRIMARY KEY, motd TEXT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS info (name TEXT PRIMARY KEY, owner TEXT COLLATE NOCASE, helpers TEXT, admins TEXT, coowners TEXT, receiver TEXT, perms JSON, spawner INT, oregen INT, autominer INT, autoseller INT, hopper INT, farm INT, vlimit INT, islanddata JSON);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS info2 (name TEXT PRIMARY KEY, creator TEXT COLLATE NOCASE, bans TEXT, mining INT, farming INT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS info4 (name TEXT PRIMARY KEY, miners TEXT, farmers TEXT, placers TEXT, builders TEXT, labourers TEXT, butchers TEXT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS info8 (name TEXT primary KEY, coal INT, copper INT, iron INT, lapis INT, gold INT, diamond INT, emerald INT, quartz INT, netherite INT, deep_coal INT, deep_copper INT, deep_iron INT, deep_lapis INT, deep_gold INT, deep_diamond INT, deep_emerald INT, deep_quartz INT, deep_netherite INT)");
        $this->db->exec("CREATE TABLE IF NOT EXISTS info8pref (name TEXT primary KEY, cobblestone INT, coal INT, copper INT, iron INT, lapis INT, gold INT, diamond INT, emerald INT, quartz INT, netherite INT, deep_coal INT, deep_copper INT, deep_iron INT, deep_lapis INT, deep_gold INT, deep_diamond INT, deep_emerald INT, deep_quartz INT, deep_netherite INT)");
        $this->db->exec("CREATE TABLE IF NOT EXISTS lock (name TEXT PRIMARY KEY, locked TEXT COLLATE NOCASE);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS level (name TEXT PRIMARY KEY, points INT, level INT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS home(ID INTEGER PRIMARY KEY, name TEXT, x INT, y INT, z INT, home TEXT);");

        $this->db->exec("CREATE TABLE IF NOT EXISTS helper (player TEXT PRIMARY KEY COLLATE NOCASE, count INT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS player (player TEXT PRIMARY KEY COLLATE NOCASE, money REAL, mobcoin INT, xp INT, xpbank INT, mana INT, blocks INT, kills INT, deaths INT, killstreak INT, chips INT, won INT, bounty INT, seltag INT, tags TEXT, wm TEXT, homes JSON, pref JSON, extradata JSON, quests JSON);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS kit (player TEXT PRIMARY KEY COLLATE NOCASE, achilles INT, theo INT, cosmo INT, arcadia INT, artemis INT, calisto INT);");

        $this->db->exec("CREATE TABLE IF NOT EXISTS gang (player TEXT PRIMARY KEY COLLATE NOCASE, gang TEXT, kills INT, deaths INT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS creator (gang TEXT PRIMARY KEY, leader TEXT COLLATE NOCASE, level INT, points INT, motd TEXT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS goals (player TEXT PRIMARY KEY COLLATE NOCASE, goal TEXT);"); //

        $this->db->exec("CREATE TABLE IF NOT EXISTS combat (player TEXT PRIMARY KEY COLLATE NOCASE, level INT, exp INT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS mining (player TEXT PRIMARY KEY COLLATE NOCASE, level INT, exp INT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS farming (player TEXT PRIMARY KEY COLLATE NOCASE, level INT, exp INT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS gambling (player TEXT PRIMARY KEY COLLATE NOCASE, level INT, exp INT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS rewards5 (player TEXT PRIMARY KEY COLLATE NOCASE, type TEXT, id INT, count INT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS rewards10 (player TEXT PRIMARY KEY COLLATE NOCASE, type TEXT, id INT, count INT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS rewards15 (player TEXT PRIMARY KEY COLLATE NOCASE, type TEXT, book TEXT, id INT, count INT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS rewards20 (player TEXT PRIMARY KEY COLLATE NOCASE, type TEXT, book TEXT, id INT, count INT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS rewards30 (player TEXT PRIMARY KEY COLLATE NOCASE, type TEXT, book TEXT, id INT, count INT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS rewards90 (player TEXT PRIMARY KEY COLLATE NOCASE, type TEXT, book TEXT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS rewardsmax (player TEXT PRIMARY KEY COLLATE NOCASE, type TEXT, book TEXT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS votes(server TEXT PRIMARY KEY, votes INT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS timings(player TEXT PRIMARY KEY, seconds INT);");
        $this->db->busyTimeout(5000);

        $this->db4 = new SQL($this->getDataFolder() . "pets.db");
        $this->db4->exec("CREATE TABLE IF NOT EXISTS pets (player TEXT PRIMARY KEY COLLATE NOCASE, name TEXT, unlocked TEXT, current TEXT);");
        $this->db4->busyTimeout(5000);
    }

    public function loadCommands() : void {
        $commands = $this->commands->get("Commands");
        foreach ($commands as $i) {
            $this->getScheduler()->scheduleRepeatingTask(new TimeCommand($this, $i["Command"]), $i["Time"] * 1200);
        }
    }

    public function loadGKits() : void {
        $this->gkits = [
            'achilles' => [],
            'theo'     => [],
            'cosmo'    => [],
            'arcadia'  => [],
            'artemis'  => [],
            'calisto'  => []
        ];
    }

    /**
     * @return FormFunctions
     */
    public function getFormFunctions() : FormFunctions {
        return $this->formfunc;
    }

    /**
     * @return Chair
     */
    public function getChair() : Chair {
        return $this->chair;
    }

    /**
     * @param SBPlayer $player
     */
    public function giveRewards(Player $player) : void {
        $i = 0;
        $count = $this->kothnumber++;
        $item = CustomItems::CARROT_ON_A_STICK();
        $item->setCustomName("§l§bKOTH §r§etrophy \n §a{$player->getName()} \n §eWinner of §bKOTH §e#§a$count");
        $tag[$i] = $item->nbtSerialize($i++);
        $item = VanillaItems::TURTLE_HELMET();
        $item->setCustomName("§l§eWinner of §bKOTH §e#§a$count");
        $tag[$i] = $item->nbtSerialize($i++);
        $count1 = mt_rand(1, 5);
        $item = CustomItems::CE_KEY();
        $item = $item->setCount($count1);
        $item->setCustomName("§r§l§7§k:§r§l§9CE§7§k:§r §l§9Key\n§r§fUse this key at /warp crates");
        $tag[$i] = $item->nbtSerialize($i++);
        $count2 = mt_rand(1, 5);
        $item = CustomItems::VE_KEY();
        $item = $item->setCount($count2);
        $item->setCustomName("§r§l§7§k:§r§l§cVE§7§k:§r §l§cKey\n§r§fUse this key at /warp crates");
        $tag[$i] = $item->nbtSerialize($i++);

        $cheque = $this->getCheque(mt_rand(50000, 500000));
        $tag[$i] = $cheque->nbtSerialize($i++);

        $kit = $this->getFunctions()->getRandomKit();
        $tag[$i] = $kit->nbtSerialize($i++);

        $types = ["common", "rare", "legendary", "exclusive"];
        $book = $this->getCEBook($types[mt_rand(0, count($types) - 1)]);
        $tag[$i] = $book->nbtSerialize($i++);

        $sword = $this->getFunctions()->opSword(mt_rand(4, 6));
        $tag[$i] = $sword->nbtSerialize($i++);

        $random = $this->getFunctions()->getEnvoyItem1();
        $tag[$i] = $random->nbtSerialize($i++);

        $user = $this->getUserManager()->getOnlineUser($player->getName());
        if ($user->getSetGang() !== '') {
            $this->getGangManager()->getOnlineGang($user->getGang())->setPoints(mt_rand(250, 500));
        }

        $ctag = new CompoundTag();
        $ctag->setTag(Container::TAG_ITEMS, new ListTag($tag, NBT::TAG_Compound));
        $chest = VanillaBlocks::CHEST()->asItem();
        $chest->setNamedTag($chest->getNamedTag());
        $chest->setCustomBlockData($ctag);
        $chest->setCustomName("§o§l§bKOTH §fWinner Kit\n§r§ePlace this chest\n§eand open it to get the rewards!");
        $player->getInventory()->setItem(32, $chest);
        $this->sendMessage($player, "§l§eClaimed §bKOTH §ewinner reward chest, check in your inventory!");
    }

    public function loadAuctions() : void {
        $data = new Config($this->getDataFolder() . 'auctions.sl', Config::SERIALIZED);
        foreach ($data->getAll() as $aucId => $aucData) {
            $this->auctions[$aucId] = $aucData;
        }
    }

    public function loadPrivateChests() : void {
        $data = new Config($this->getDataFolder() . 'pchests.json', Config::JSON);
        foreach ($data->getAll() as $pId => $pData) {
            $this->pchests[$pId] = $pData;
        }
    }

    /**
     * @param Block  $block
     * @param string $owner_name
     *
     * @return bool
     */
    public function isPrivateChest(Block $block, string $owner_name) : bool {
        $name = strtolower($owner_name);
        if ($block instanceof \pocketmine\block\Chest) {
            $ablock = Util::getFrontBlock($block);
            if ($ablock instanceof WallSign && $this->legitChest($block, $ablock)) {
                return isset($this->pchests[$name][$ablock->getPosition()->getX() . ":" . $ablock->getPosition()->getY() . ":" . $ablock->getPosition()->getZ() . ":" . $ablock->getPosition()->getWorld()->getDisplayName()]);
            }
        }
        return false;
    }

    /**
     * @param Block $chest
     * @param Block $sign
     *
     * @return bool
     */
    public function legitChest(Block $chest, Block $sign) : bool { // to fix the reflective chest bug
        return Util::getRearBlock($sign) === $chest;
    }

    /**
     * @param IPlayer $player
     *
     * @return string
     */
    public function getRank(IPlayer $player) : string {
        return $this->permsapi->getUserGroup($player->getName())->getName();
    }

    /**
     * @param Block  $block
     * @param string $name
     *
     * @return bool
     */
    public function isPrivateChestSign(Block $block, string $name) : bool {
        $bblock = Util::getRearBlock($block);
        return $bblock instanceof \pocketmine\block\Chest && $this->isPrivateChest($bblock, strtolower($name));
    }

    /**
     * @param Block  $block
     * @param string $name
     */
    public function destroyPrivateChest(Block $block, string $name) : void {
        $name = strtolower($name);
        if ($block instanceof WallSign) {
            unset($this->pchests[$name][$block->getPosition()->getX() . ":" . $block->getPosition()->getY() . ":" . $block->getPosition()->getZ() . ":" . $block->getPosition()->getWorld()->getDisplayName()]);
        }
    }

    /**
     * @param string $name
     */
    public function destroyAllPrivateChests(string $name) : void {
        unset($this->pchests[strtolower($name)]);
    }

    public function setOreList() : void {
        $list = [];
        $ores = ['Coal' => 20, 'Iron' => 13, 'Gold' => 10, 'Lapis' => 8, 'Netherrack' => 8, 'Emerald' => 5, 'Diamond' => 4, 'Quartz' => 2, 'Cobblestone' => 30];
        foreach ($ores as $ore => $chance) {
            for ($i = 0; $i < $chance; $i++) {
                $list[] = $ore;
            }
        }
        $this->oreList = $list;
    }

    /**
     * @return array
     */
    public function getCrops() : array {
        return $this->crops;
    }

    private function setOregens() : void {
        $this->oregens = [
            BlockTypeNames::COAL_ORE              => ['name' => 'Coal', 'string' => BlockTypeNames::BLACK_GLAZED_TERRACOTTA],
            BlockTypeNames::IRON_ORE              => ['name' => 'Iron', 'string' => BlockTypeNames::SILVER_GLAZED_TERRACOTTA],
            BlockTypeNames::LAPIS_ORE             => ['name' => 'Lapis', 'string' => BlockTypeNames::BLUE_GLAZED_TERRACOTTA],
            BlockTypeNames::GOLD_ORE              => ['name' => 'Gold', 'string' => BlockTypeNames::YELLOW_GLAZED_TERRACOTTA],
            BlockTypeNames::DIAMOND_ORE           => ['name' => 'Diamond', 'string' => BlockTypeNames::LIGHT_BLUE_GLAZED_TERRACOTTA],
            BlockTypeNames::EMERALD_ORE           => ['name' => 'Emerald', 'string' => BlockTypeNames::GREEN_GLAZED_TERRACOTTA],
            BlockTypeNames::NETHERRACK            => ['name' => 'Netherrack', 'string' => BlockTypeNames::BLACK_GLAZED_TERRACOTTA],
            BlockTypeNames::QUARTZ_ORE            => ['name' => 'Quartz', 'string' => BlockTypeNames::RED_GLAZED_TERRACOTTA],
            BlockTypeNames::DEEPSLATE_COAL_ORE    => ['name' => 'Deepslate Coal Ore', 'string' => BlockTypeNames::GRAY_GLAZED_TERRACOTTA],
            BlockTypeNames::DEEPSLATE_IRON_ORE    => ['name' => 'Deepslate Iron Ore', 'string' => BlockTypeNames::SILVER_GLAZED_TERRACOTTA],
            BlockTypeNames::DEEPSLATE_LAPIS_ORE   => ['name' => 'Deepslate Lapis Ore', 'string' => BlockTypeNames::BLUE_GLAZED_TERRACOTTA],
            BlockTypeNames::DEEPSLATE_GOLD_ORE    => ['name' => 'Deepslate Gold Ore', 'string' => BlockTypeNames::YELLOW_GLAZED_TERRACOTTA],
            BlockTypeNames::DEEPSLATE_DIAMOND_ORE => ['name' => 'Deepslate Diamond Ore', 'string' => BlockTypeNames::LIGHT_BLUE_GLAZED_TERRACOTTA],
            BlockTypeNames::DEEPSLATE_EMERALD_ORE => ['name' => 'Deepslate Emerald Ore', 'string' => BlockTypeNames::GREEN_GLAZED_TERRACOTTA],
            BlockTypeNames::ANCIENT_DEBRIS        => ['name' => 'Ancient Debris', 'string' => BlockTypeNames::BLACK_GLAZED_TERRACOTTA]
        ];
    }

    private function setCrops() : void {
        $this->crops = [
            BlockTypeIds::SUGARCANE    => ['level' => Data::$sugarcaneIslandLevel, 'name' => "SugarCane"],
            BlockTypeIds::BEETROOTS    => ['level' => Data::$beetrootIslandLevel, 'name' => "Beetroot"],
            BlockTypeIds::WHEAT        => ['level' => Data::$wheatIslandLevel, 'name' => "Wheat"],
            BlockTypeIds::CACTUS       => ['level' => Data::$cactusIslandLevel, 'name' => "Cactus"],
            BlockTypeIds::NETHER_WART  => ['level' => Data::$netherWartIslandLevel, 'name' => "Nether Wart"],
            BlockTypeIds::POTATOES     => ['level' => Data::$potatoIslandLevel, 'name' => "Potato"],
            BlockTypeIds::CARROTS      => ['level' => Data::$carrotIslandLevel, 'name' => "Carrot"],
            BlockTypeIds::PUMPKIN      => ['level' => Data::$pumpkinIslandLevel, 'name' => "Pumpkin"],
            BlockTypeIds::MELON        => ['level' => Data::$melonIslandLevel, 'name' => "Melon"],
            BlockTypeIds::MELON_STEM   => ['level' => Data::$melonIslandLevel, 'name' => "Melon"],
            BlockTypeIds::PUMPKIN_STEM => ['level' => Data::$pumpkinIslandLevel, 'name' => "Pumpkin"]
        ];
    }

    public function setSpawners() : void {
        $this->spawners = [
            'chicken'    => ['id' => EntityIds::CHICKEN, 'name' => '§r§fChicken', 'cost' => Data::$chickenSpawnerCost], // 80
            'pig'        => ['id' => EntityIds::PIG, 'name' => '§r§dPig', 'cost' => Data::$pigSpawnerCost], // 45 sell price
            'cow'        => ['id' => EntityIds::COW, 'name' => '§r§6Cow', 'cost' => Data::$cowSpawnerCost], // 60
            'sheep'      => ['id' => EntityIds::SHEEP, 'name' => '§r§eSheep', 'cost' => Data::$sheepSpawnerCost], // 150 + looting
            'squid'      => ['id' => EntityIds::SQUID, 'name' => '§r§bSquid', 'cost' => Data::$squidSpawnerCost], // 100
            'goat'       => ['id' => EntityIds::GOAT, 'name' => '§r§eGoat', 'cost' => Data::$goatSpawnerCost], // 150 + looting
            'glowsquid'  => ['id' => EntityIds::GLOW_SQUID, 'name' => '§r§eGlowSquid', 'cost' => Data::$glowSquidSpawnerCost], // 150 + looting
            'camel'      => ['id' => EntityIds::CAMEL, 'name' => '§r§eCamel', 'cost' => Data::$camelSpawnerCost], // 150 + looting
            'panda'      => ['id' => EntityIds::PANDA, 'name' => '§r§ePanda', 'cost' => Data::$pandaSpawnerCost], // 150 + looting
            'spider'     => ['id' => EntityIds::SPIDER, 'name' => '§r§0Spider', 'cost' => Data::$spiderSpawnerCost], // 120
            'pigman'     => ['id' => EntityIds::ZOMBIE_PIGMAN, 'name' => '§r§4Pigman', 'cost' => Data::$pigmanSpawnerCost], // 80 + looting
            'zombie'     => ['id' => EntityIds::ZOMBIE, 'name' => '§r§aZombie', 'cost' => Data::$zombieSpawnerCost], // 150
            'skeleton'   => ['id' => EntityIds::SKELETON, 'name' => '§r§7Skeleton', 'cost' => Data::$skeletonSpawnerCost], // 140
            'polarbear'  => ['id' => EntityIds::POLAR_BEAR, 'name' => '§r§ePolar Bear', 'cost' => Data::$polarBearSpawnerCost], // 150 + looting
            'creeper'    => ['id' => EntityIds::CREEPER, 'name' => '§r§eCreeper', 'cost' => Data::$creeperSpawnerCost], // 150 + looting
            'irongolem'  => ['id' => EntityIds::IRON_GOLEM, 'name' => '§r§fIron Golem', 'cost' => Data::$ironGolemSpawnerCost], // 180 min
            'silverfish' => ['id' => EntityIds::SILVERFISH, 'name' => '§r§eSilverfish', 'cost' => Data::$silverFishSpawnerCost], // 150 + looting
            'blaze'      => ['id' => EntityIds::BLAZE, 'name' => '§r§eBlaze', 'cost' => Data::$blazeSpawnerCost], // 150 + looting
        ];
    }

    public function setScrolls() : void {
        $this->scrolls = [
            'levelup'   => "§r§6 LevelUp Scroll \n §r§eUse this on a tool \n §ewith /levelup <ce> ",
            'enchanter' => "§r§6 Enchanter Scroll \n §r§eUse this on a book \n §ewith /enchanter to increase it's accuracy ",
            'god'       => "§r§6 GOD Scroll \n §r§eUse this on a tool's ce \n §ewith /maxer <ce> to max it to level 10 ",
            'inferno'   => "§r§6 Inferno Scroll \n §r§eUse this on a tool's vanilla enchants \n §ewith /inferno <enchant> to max it to level 10 ",
            'fixer'     => "§r§6 Fixer Scroll \n §r§eUse this on a tool to fix it \n §ewith /fixer! ",
            'vulcan'    => "§r§6 Vulcan Scroll \n §r§eUse this on a tool to remove ce \n §ewith /vulcan <ce> to get it as CE Book ",
            'carver'    => "§r§6 Carver Scroll \n §r§eUse this on a tool to remove ench \n §ewith /carver <enchant> to get it as orb ",
            'surge'     => "§r§6 Surge Scroll \n §r§eUse this on an item to increase \n §eits fix cap by 1 with /surge ",
            'renew'     => "§r§6 Renew Scroll \n §r§eUse this on an item to \n §ereset fix stats with /renew ",
        ];
    }

    public function setCrateKeys() : void {
        $this->keys = [
            'vote'      => ['item' => 341, 'name' => "§r§6Vote Key\n§r§fUse this key at /warp crates"],
            'common'    => ['item' => 378, 'name' => "§r§aCommon Key\n§r§fUse this key at /warp crates"],
            'rare'      => ['item' => 370, 'name' => "§r§bRare Key\n§r§fUse this key at /warp crates"],
            'legendary' => ['item' => 349, 'name' => "§r§e§lLegendary Key\n§r§fUse this key at /warp crates"],
            'mystic'    => ['item' => 335, 'name' => "§r§d§lMystic Key\n§r§fUse this key at /warp crates"]
        ];
    }

    public function setRelics() : void {
        $this->relics = [
            'common'    => ['item' => 220, 'name' => "§r§6Common Relic\n§r§fPlace it to claim it"],
            'rare'      => ['item' => 221, 'name' => "§r§aRare Relic\n§r§fPlace it to claim it"],
            'legendary' => ['item' => 223, 'name' => "§r§eLegendary Relic\n§r§fPlace it to claim it"],
            'mythic'    => ['item' => 229, 'name' => "§r§d§lMythic Relic\n§r§fPlace it to claim it"],
            'godly'     => ['item' => 234, 'name' => "§r§b§lGodly Relic\n§r§fPlace it to claim it"]
        ];
    }

    public function checkPacks() : void {
        foreach ($this->getServer()->getResourcePackManager()->getPackIdList() as $pack) {
            if ($this->getServer()->getResourcePackManager()->getPackById($pack)->getPackName() === "ftui") {
                $this->chatpack = true;
                return;
            }
        }
    }

    public function setMobArena() : void {
        /**keep this line commented out until finished or while testing*/
        //$this->mobArena = new MobArena($this);
    }

    public function registerEntities() : void {
        $pf = new PetFactory($this);
        $pf->init();
        $sf = new SpawnerFactory($this);
        $sf->init();
    }

    public function registerItems() : void {
        $ifactory = new IFactory($this);
        $ifactory->init();
    }

    public function registerTiles() : void {
        $tfactory = new TileFactory($this);
        $tfactory->init();
    }

    public function registerBlocks() : void {
        $bfactory = new BFactory($this);
        $bfactory->init();
    }

    public function registerEnchantments() : void {
        $this->enchFactory = new EnchantFactory($this);
        $this->enchFactory->init();
        $this->enchantments = $this->enchFactory->getEnchants();
    }

    /**
     * @return ParticleManager
     */
    public function getParticleManager() : ParticleManager {
        return $this->particleManager;
    }

    public function setParticleManager() : void {
        $this->particleManager = new ParticleManager($this);
    }

    public function setSqlite3() : void {
        $this->sqlite3 = new SQLite3($this);
    }

    public function getVotes() : void {
        $this->vote = $this->getDb()->getVotes();
    }

    /**
     * @return SQLite3
     */
    public function getDb() : SQLite3 {
        return $this->sqlite3;
    }

    public function registerCommands() : void {
        new CommandFactory($this);
    }

    public function setCapes() : void {
        $dir = $this->getDataFolder() . "capes/"; // for replace below
        foreach (glob($this->getDataFolder() . "capes/*.png") as $file) {
            $file = str_replace($dir, "", $file);
            $name = substr($file, 0, -4);
            self::$capes[$name] = EvFunctions::getCapeData($this->getDataFolder() . "capes/" . $file);
        }
    }

    /**
     * @return EvFunctions
     */
    public function getEvFunctions() : EvFunctions {
        return $this->evFunctions;
    }

    public function setEvFunctions() : void {
        $this->evFunctions = new EvFunctions($this);
    }

    /**
     * @return array
     */
    public function getVaulted() : array {
        return $this->vaulted;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function isVaulted(int $id) : bool {
        return in_array($id, $this->vaulted, true);
    }

    public function onDisable() : void {
        $this->getLogger()->info(TextFormat::RED . "Skyblock is stopping...");
        $this->restarting = true;
        if ($this->loaded) {
            $this->sendKillLog("", "", true);
            $this->saveConfigs();
            $this->delEnvoys();
            $this->getFunctions()->clearAllEntities();
            $this->getIslandManager()->update();
            $this->getUserManager()->update();
            $this->getGangManager()->update();
        }
        if (!empty($this->rename)) {
            foreach ($this->rename as $old => $new) {
                $this->getDb()->renameIsland($old, $new);
            }
        }
        if (!empty($this->gangrename)) {
            foreach ($this->gangrename as $old => $new) {
                $this->getDb()->renameGang($old, $new);
            }
        }
        if (($level = $this->getServer()->getWorldManager()->getWorldByName("end")) !== null) {
            $path = $level->getProvider()->getPath();
            $this->getServer()->getWorldManager()->unloadWorld($level);
            $this->islandManager->deleteLevel($path);
        } // todo delete this
        Connector::close();
        $this->getServer()->getWorldManager()->setAutoSave(true);
        $this->getLogger()->info(TextFormat::RED . "Skyblock has stopped!");
    }

    private function saveConfigs() : void {
        $this->saveAuctions();
        $this->saveTrades();
        $this->savePrivateChests();
        $this->saveFlyCounter();
        $this->setVotes();
        $this->savePositions();
        $this->saveItemCloud();
        $this->savePlayerShops();
        $this->saveKothNumber();
        $this->saveKits();
        //		$this->checkNReset();
    }

    public function saveAuctions() : void {
        unlink($this->getDataFolder() . 'auctions.sl'); //Avoiding duplication glitches.
        $data = new Config($this->getDataFolder() . 'auctions.sl', Config::SERIALIZED);
        foreach ($this->auctions as $aucId => $aucData) {
            $data->set($aucId, $aucData);
        }
        try {
            $data->save();
        } catch (JsonException) {
        }
    }

    /**
     * @param array $members
     *
     * @return Player[]
     */
    public function filterOnline(array $members) : array {
        return array_filter(array_map(function(string $member) {
            return $this->getServer()->getPlayerExact($member);
        }, $members
                            ), function($member) {
            return $member instanceof Player;
        }
        );
    }

    public function savePrivateChests() : void {
        unlink($this->getDataFolder() . 'pchests.json'); //Avoiding duplication glitches.
        $data = new Config($this->getDataFolder() . 'pchests.json', Config::JSON);
        foreach ($this->pchests as $pId => $pData) {
            $data->set($pId, $pData);
        }
        try {
            $data->save();
        } catch (JsonException) {
        }
    }

    public function delEnvoys() : void {
        foreach ($this->envoys as $id => $data) {
            $pos = new Position($data['x'], $data['y'], $data['z'], $this->getServer()->getWorldManager()->getWorldByName(Values::PVP_WORLD));
            $this->despawnEnvoy($id, $pos);
        }
    }

    /**
     * @param int      $i
     * @param Position $pos
     * @param bool     $force
     */
    public function despawnEnvoy(int $i, Position $pos, bool $force = true) : void {
        if (!$force) {
            $pos->getWorld()->addParticle($pos, new HugeExplodeSeedParticle());
        }
        $level = $pos->getWorld();
        $level->loadChunk($pos->getFloorX() >> 4, $pos->getFloorZ() >> 4);
        $level->setBlock($pos, VanillaBlocks::AIR());
        unset($this->envoys[$i]);
    }

    public function setVotes() : void {
        $this->getDb()->setVotes($this->vote);
    }

    /**
     * @return Functions
     */
    public function getFunctions() : Functions {
        return $this->functions;
    }

    public function setFunctions() : void {
        $this->formfunc = new FormFunctions($this);
        $this->functions = new Functions($this);
        $this->chair = new Chair($this);
    }

    /**
     * @return IslandManager
     */
    public function getIslandManager() : IslandManager {
        return $this->islandManager;
    }

    public function setIslandManager() : void {
        $this->islandManager = new IslandManager($this);
    }

    /**
     * @return UserManager
     */
    public function getUserManager() : UserManager {
        return $this->userManager;
    }

    public function setUserManager() : void {
        $this->userManager = new UserManager($this);
    }

    /**
     * @return GangManager
     */
    public function getGangManager() : GangManager {
        return $this->gangManager;
    }

    public function setGangManager() : void {
        $this->gangManager = new GangManager($this);
    }

    /**
     * @param string $name
     *
     * @return int|null
     */
    public function getEnchantIdByName(string $name) : ?int {
        $name = strtolower($name);
        foreach ($this->enchantments as $id => $data) {
            if (strtolower($data[0]) === $name) return $id;
        }
        return null;
    }

    public function saveItemCloud() : void {
        $save = [];
        foreach ($this->clouds as $cloud) {
            $save[] = $cloud->getAll();
        }
        file_put_contents($this->getDataFolder() . "ItemCloud.dat", serialize($save));
    }

    public function saveKothNumber() : void {
        $file = new Config($this->getDataFolder() . "kothnumber.yml", Config::YAML);
        $file->set("number", $this->kothnumber);
        try {
            $file->save();
        } catch (JsonException) {
        }
    }

    public function savePlayerShops() : void {
        $file = new Config($this->getDataFolder() . "Shops.yml", Config::YAML);
        $file->setAll($this->shops);
        try {
            $file->save();
        } catch (JsonException) {
        }
    }

    /**
     * @param SBPlayer $player
     */
    public function teleportToSpawn(Player $player) : void {
        $warp = $this->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation();
        $warp->getWorld()->loadChunk($warp->getFloorX() >> 4, $warp->getFloorZ() >> 4);
        //        try {
        //            $pos = $warp->getWorld()->getSafeSpawn($warp);
        //        } catch (JsonException){
        //            $pos = $this->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation();
        //        }
        $pos = $this->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation();
        $pos->add(0, 1, 0); // add 1 block above
        $player->teleport($pos, 0.0, 0.0);
        $player->setSpawn($pos);
    }

    /**
     * @param string $type
     * @param int    $count
     *
     * @return Item
     */
    public function getCrateKeys(string $type = 'vote', int $count = 1) : Item {
        switch ($type) {
            case "vote":
                $item = CustomItems::VOTE_KEY()->setCount($count);
                $item->setCustomName("§r§6Vote Key\n§r§fUse this key at /warp crates");
                return $item;
            case "common":
                $item = CustomItems::COMMON_KEY()->setCount($count);
                $item->setCustomName("§r§aCommon Key\n§r§fUse this key at /warp crates");
                return $item;
            case "rare":
                $item = CustomItems::RARE_KEY()->setCount($count);
                $item->setCustomName("§r§bRare Key\n§r§fUse this key at /warp crates");
                return $item;
            case "legendary":
                $item = CustomItems::LEGENDARY_KEY()->setCount($count);
                $item->setCustomName("§r§e§lLegendary Key\n§r§fUse this key at /warp crates");
                return $item;
            case "mystic":
                $item = CustomItems::MYSTIC_KEY()->setCount($count);
                $item->setCustomName("§r§d§lMystic Key\n§r§fUse this key at /warp crates");
                return $item;
            case "ce":
                $item = CustomItems::CE_KEY()->setCount($count);
                $item->setCustomName("§r§4§lCE Key\n§r§fUse this key at /warp crates");
                $item->setCustomName("§l§7§k§r§l§9CE§7§k:§r §l§9Key\n§r§fUse this key at /warp crates");
                return $item;
            case "ve":
                $item = CustomItems::VE_KEY()->setCount($count);
                $item->setCustomName("§r§4§lVE Key\n§r§fUse this key at /warp crates");
                $item->setCustomName("§l§7§k§r§l§cVE§7§k:§r §l§cKey\n§r§fUse this key at /warp crates");
                return $item;
        }
        $item = CustomItems::VOTE_KEY()->setCount($count);
        $item->setCustomName("§r§6Vote Key\n§r§fUse this key at /warp crates");
        return $item;
    }

    /**
     * @param string $type
     * @param int    $count
     *
     * @return Item
     */
    public function getCEBook(string $type = 'common', int $count = 1) : Item {
        $type = ucfirst(strtolower(TF::clean($type)));
        $book = VanillaItems::BOOK()->setCount($count);
        $book->setCustomName("§r§l §6$type Book \n §r§bTap a block to redeem a \n §6Custom Enchantment Book ");
        return $book;
    }

    public function setSkyBlockGeneratorManager() : void {
        new SkyBlockGeneratorManager();
    }

    public function setVote(int $votes) : void {
        $this->vote = $votes;
    }

    /**
     * @return int
     */
    public function getVote() : int {
        return $this->vote;
    }

    /**
     * @return SkyBlockManager
     */
    public function getSkyBlockManager() : SkyBlockManager {
        return $this->skyBlockManager;
    }

    public function setSkyBlockManager() : void {
        $this->skyBlockManager = new SkyBlockManager($this);
    }

    /**
     * @return array
     */
    public function getEnchantments() : array {
        return $this->enchantments;
    }

    /**
     * @return int
     */
    public function getRandomItem() : int {
        $rand = mt_rand(0, count($this->randomItems["Items"]) - 1);
        return $this->randomItems["Items"][$rand];
    }

    /**
     * @param string $type
     * @param int    $count
     *
     * @return Item
     */
    public function getScrolls(string $type = 'levelup', int $count = 1) : Item {
        $type = strtolower($type);
        $scroll = VanillaItems::PRISMARINE_SHARD()->setCount($count);
        $scroll->setCustomName($this->scrolls[$type]);
        return $scroll;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function getRandomPetByType(string $type = 'Common') : string {
        $pets = $this->getAllPets();
        $types = [];
        foreach ($pets as $p) {
            $properties = $this->getPetProperties()->getPropertiesFor($p);
            if (strtolower($properties['Type']) == strtolower($type)) {
                $types[] = $p;
            }
        }
        return $types[array_rand($types)];
    }

    /**
     * @return array
     */
    public function getAllPets() : array {
        return $this->pets;
    }

    /**
     * @return PetProperties
     */
    public function getPetProperties() : PetProperties {
        return $this->pProperties;
    }

    /**
     * @param int $money
     * @param int $count
     *
     * @return Item
     */
    public function getCheque(int $money = 1, int $count = 1) : Item {
        $item = VanillaItems::PAPER()->setCount($count);
        $item->setCustomName("§r§6FTech's §aCheque \n §bTap a block to redeem \n §eMoney: $money");
        return $item;
    }

    /**
     * @param string $type
     * @param int    $count
     *
     * @return Item
     */
    public function getRelic(string $type = 'common', int $count = 1) : Item {
        $type = strtolower($type);
        if ($type == 'random') $type = array_rand($this->relics);
        $relic = LegacyStringToItemParser::getInstance()->parse($this->relics[$type]['item'])->setCount($count);
        $relic->setCustomName($this->relics[$type]['name']);
        return $relic;
    }

    /**
     * @param string $type
     *
     * @return Item
     */
    public function getTrollItem(string $type = 'lol') : Item {
        $type = mb_strtoupper($type);
        $troll = VanillaItems::FEATHER();
        $troll->setCustomName("§r§o§e$type \n §r§fThis item is useless ");
        return $troll;
    }

    public function setEventListener() : void {
        new EventListener($this);
    }

    /**
     * @return ChatHandler
     */
    public function getChatHandler() : ChatHandler {
        return $this->chatHandler;
    }

    public function setChatHandler() : void {
        $this->chatHandler = new ChatHandler();
    }

    /**
     * @return GangChatHandler
     */
    public function getGangChatHandler() : GangChatHandler {
        return $this->gangChatHandler;
    }

    public function setGangChatHandler() : void {
        $this->gangChatHandler = new GangChatHandler();
    }

    /**
     * @param string $kit
     *
     * @return Kit|null
     */
    public function getKit(string $kit) : ?Kit {
        $lowerKeys = array_change_key_case($this->kits, CASE_LOWER);
        if (isset($lowerKeys[strtolower($kit)])) {
            return $lowerKeys[strtolower($kit)];
        }
        return null;
    }

    /**
     * @param string $symbol
     * @param string $message
     *
     * @return string
     */
    public function translateColors(string $symbol, string $message) : string {
        return str_replace($symbol, "§", $message);
    }

    /**
     * @param BasePet $pet
     */
    public function updateNameTag(BasePet $pet) : void {
        $i = match ($pet->getType()) {
            "CommonB" => "§7Common", // bad pet
            "Rare" => "§fRare", // good pet
            "Premium" => "§6§lPremium", // purchasable from store
            "Exclusive" => "§b§lExclusive", // giveaway winner
            "Prestige" => "§l§6Prestige", // buy from presshop
            "IslandChamp" => "§4§lIslandChampion", // pet won from being top island
            "Staff" => "§5§lStaff", // staff only pet (on request)
            default => "",
        };
        $pet->setNameTag("§6" . $pet->getPetName() . "\n§7§l[§r{$i}§l§7] §r" . TextFormat::YELLOW . $pet->getName());
    }

    /**
     * @param string   $entityName
     * @param SBPlayer $player
     * @param string   $name
     * @param bool     $isVisible
     *
     * @return BasePet|null
     */
    public function createPet(string $entityName, Player $player, string $name, bool $isVisible = true) : ?BasePet {
        $pets = $this->getPetsFrom($player);
        foreach ($pets as $pet) {
            $this->removePet($pet);
        }

        $nbt = CompoundTag::create();
        $nbt->setString("petOwner", $player->getName());
        $properties = $this->getPetProperties()->getPropertiesFor($entityName);
        $nbt->setFloat("scale", (float) $properties["Size"]);
        $nbt->setString("petName", $name);

        $class = $this->getPetClass($entityName);
        if ($class === null) {
            return null;
        }

        $entity = new $class($player->getLocation(), $nbt);

        if ($entity instanceof BasePet) {
            $entity->setNameTag($entityName . "Pet");
            $ev = new PetSpawnEvent($this, $entity);
            $ev->call();
            if ($ev->isCancelled()) {
                $this->removePet($entity);
                return null;
            }
            if (!$isVisible) {
                $entity->updateVisibility(false);
            } else {
                $entity->spawnToAll();
            }
            $this->playerPets[strtolower($player->getName())][strtolower($entity->getPetName())] = $entity;
            return $entity;
        }
        return null;
    }

    /**
     * Get the class of the relevant pet.
     */
    public function getPetClass(string $entityName) : ?string {
        foreach (PetFactory::PET_CLASSES as $pet => $petClass) {
            if (strtolower($pet) === strtolower($entityName)) {
                return $petClass;
            }
        }
        return null;
    }

    /**
     * @param SBPlayer $player
     *
     * @return BasePet[]
     */
    public function getPetsFrom(Player $player) : array {
        return $this->playerPets[strtolower($player->getName())] ?? [];
    }

    /**
     * @param SBPlayer $player
     */
    public function removePlayerPet(Player $player) : void {
        if ($this->isRidingAPet($player)) {
            $this->getRiddenPet($player)->throwRiderOff();
            $this->sendMessage($player, "§7Your pet was cleared.");
        }
        if ($user = $this->userManager->getOnlineUser($player->getName())) {
            if ($user->hasSetPet()) {
                $user->setPet();
                if (!empty($pet = $this->getPetsFrom($player))) {
                    foreach ($pet as $p) {
                        $this->removePet($p);
                    }
                }
            }
        }
    }

    /**
     * @param BasePet $pet
     * @param bool    $close
     */
    public function removePet(BasePet $pet, bool $close = true) : void {
        $ev = new PetRemoveEvent($this, $pet);
        $ev->call();
        if ($pet->isRidden()) {
            $pet->throwRiderOff();
        }
        if ($close && !$pet->isClosed()) {
            $pet->close();
        }
        unset($this->playerPets[strtolower($pet->getPetOwnerName())][strtolower($pet->getPetName())]);
    }

    /**
     * @param SBPlayer $player
     *
     * @return BasePet|null
     */
    public function getRiddenPet(Player $player) : ?BasePet {
        foreach ($this->getPetsFrom($player) as $pet) {
            if ($pet->isRidden()) {
                return $pet;
            }
        }
        return null;
    }

    /**
     * @param SBPlayer $player
     *
     * @return bool
     */
    public function isRidingAPet(Player $player) : bool {
        foreach ($this->getPetsFrom($player) as $pet) {
            if ($pet->isRidden()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Player $player
     * @param int    $damage
     */
    public function strikeLightning(Player $player, int $damage) : void {
        $location = $player->getLocation();
        $lightning = new AddEntityPacket();
        $lightning->actorUniqueId = Entity::nextRuntimeId();
        $lightning->actorRuntimeId = $lightning->actorUniqueId;
        $lightning->type = 'minecraft:lightning_bolt';
        $lightning->position = $location->asVector3();
        $lightning->motion = null;
        $lightning->pitch = $location->getPitch();
        $lightning->yaw = $location->getYaw();
        $lightning->headYaw = 0.0;
        $lightning->attributes = [];
        $lightning->metadata = [];
        $lightning->syncedProperties = new PropertySyncData([], []);
        $lightning->links = [];

        $thunder = new PlaySoundPacket();
        $thunder->soundName = 'ambient.weather.thunder';
        $thunder->x = $location->getX();
        $thunder->y = $location->getY();
        $thunder->z = $location->getZ();
        $thunder->volume = 1;
        $thunder->pitch = 1;
        $player->getNetworkSession()->sendDataPacket($lightning);
        $player->getNetworkSession()->sendDataPacket($thunder);
        foreach ($player->getViewers() as $viewer) {
            $viewer->getNetworkSession()->sendDataPacket($lightning);
            $viewer->getNetworkSession()->sendDataPacket($thunder);
        }
        $this->createTNT($location, $player->getWorld());
        foreach ($location->getWorld()->getNearbyEntities(new AxisAlignedBB($location->getFloorX() - ($radius = 5), $location->getFloorY() - $radius, $location->getFloorZ() - $radius, $location->getFloorX() + $radius, $location->getFloorY() + $radius, $location->getFloorZ() + $radius), $player) as $e) {
            if ($e instanceof Player && $e->getName() !== $player->getName()) {
                $e->attack(new EntityDamageEvent($e, EntityDamageEvent::CAUSE_MAGIC, $damage));
            }
        }
        //        $this->lightningPacket = null;
    }

    /**
     * @param Vector3 $pos
     * @param World   $level
     *
     * @return Entity|null
     */
    public function createTNT(Vector3 $pos, World $level) : Entity|null {
        $mot = (new Random())->nextSignedFloat() * M_PI * 2;

        $ctag = new CompoundTag();
        $ctag->setTag("Pos", new ListTag([
                                             new DoubleTag($pos->getFloorX() + 0.5),
                                             new DoubleTag($pos->getFloorY()),
                                             new DoubleTag($pos->getFloorZ() + 0.5)
                                         ]
        )
        );

        $ctag->setTag("Motion", new ListTag([
                                                new DoubleTag(-sin($mot) * 0.02),
                                                new DoubleTag(0.2),
                                                new DoubleTag(-cos($mot) * 0.02)
                                            ]
        )
        );

        $ctag->setTag("Rotation", new ListTag([
                                                  new FloatTag(0),
                                                  new FloatTag(0),
                                              ]
        )
        );

        $ctag->setTag("Fuse", new ShortTag(100));

        $entity = new PrimedTNT(EntityDataHelper::parseLocation($ctag, $level), $ctag);
        $entity->spawnToAll();

        return $entity;
    }

    /**
     * @param string $cmd
     */
    public function runCommand(string $cmd) : void {
        $this->getServer()->dispatchCommand(new ConsoleCommandSender($this->getServer(), new Language("eng")), $cmd);
    }

    /**
     * @param Permissible $player
     *
     * @return bool
     */
    public function hasOp(Permissible $player) : bool {
        return $player->hasPermission(DefaultPermissions::ROOT_OPERATOR);
    }

    /**
     * @param array  $players
     * @param string $message
     */
    public function warMessages(array $players, string $message) : void {
        foreach ($players as $helper) {
            if (($user = $this->getUserManager()->getOnlineUser($helper)) !== null) {
                $this->teleportToSpawn($user->getPlayer());
                $this->sendMessage($user->getPlayer(), $message);
            }
        }
    }

    /**
     * @param SBPlayer $sender
     * @param string   $message
     */
    public function sendMessage(Player $sender, string $message) : void {
        $sender->sendMessage(Values::FT_PREFIX . $message);
    }

    /**
     * @param int      $i
     * @param Position $pos
     */
    public function spawnEnvoy(int $i, Position $pos) : void {
        $x = $pos->x;
        $y = $pos->y;
        $z = $pos->z;
        $this->envoys[$i] = ['x' => $x, 'y' => $y, 'z' => $z];
        $level = $pos->getWorld();
        $level->loadChunk($pos->getFloorX() >> 4, $pos->getFloorZ() >> 4);
        //        /** @var Chest $chest */
        //        $tile = new Chest($level, $pos->asVector3());
        //        $level->addTile($tile);
        $level->setBlock($pos, VanillaBlocks::CHEST());
        $chest = $level->getTile($pos);
        if ($chest instanceof Chest) {
            $inventory = $chest->getInventory();
            $inventory->addItem($this->getFunctions()->getEnvoyItem1());     // SCROLLS or CRATE KEYS
            $inventory->addItem($this->getFunctions()->getEnvoyItem2());     // SWORD CES 1-4 level op sword
            $inventory->addItem($this->getFunctions()->getEnvoyItem3());     // ARMOR CES
            $inventory->addItem($this->getFunctions()->getEnvoyItem4());     // TAGS or KIT or CHANCE FOR MYSTIC KEY
            $inventory->addItem($this->getFunctions()->getEnvoyItem5());     // KIT or CE Book
        }
    }

    /**
     * @param int $n
     *
     * @return String
     */
    public static function shortenNumber(int $n) : string {
        if ($n < 1000) {
            // Anything less than a thousand
            $n_format = number_format($n);
        } else if ($n < 1000000) {
            // Anything less than a million
            $n_format = number_format($n / 1000, 1) . 'K';
        } else if ($n < 1000000000) {
            // Anything less than a billion
            $n_format = number_format($n / 1000000, 1) . 'M';
        } else {
            // At least a billion
            $n_format = number_format($n / 1000000000, 1) . 'B';
        }
        return $n_format;
    }

    public function saveTrades() : void {
        unlink($this->getDataFolder() . 'trades.sl'); //Avoiding duplication glitches.
        $data = new Config($this->getDataFolder() . 'trades.sl', Config::SERIALIZED);
        foreach ($this->trades as $tId => $tData) {
            $data->set($tId, $tData);
        }
        try {
            $data->save();
        } catch (JsonException) {
        }
    }

    public function loadTrades() : void {
        $data = new Config($this->getDataFolder() . 'trades.sl', Config::SERIALIZED);
        foreach ($data->getAll() as $tId => $tData) {
            $this->trades[$tId] = $tData;
        }
    }

    /**
     * @param bool $broadcast
     */
    public function checkVotes(bool $broadcast = true) : void {
        if ($this->vote !== 0) {
            if ($broadcast) {
                Server::getInstance()->broadcastMessage(TextFormat::GREEN . TextFormat::BOLD . "> §a$this->vote §r§emore votes required for a vote party! §bEveryone online gets crate keys in vote party! §6Vote by /vote");
            }
        } else {
            $this->vote = 30;
            Server::getInstance()->broadcastMessage(TextFormat::GREEN . TextFormat::BOLD . "> §eVote Party successful!");
            $item = $this->getFunctions()->getRandomKey();
            foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                if ($player->getInventory()->canAddItem($item)) {
                    $this->sendMessage($player, "§aClaimed vote party prize successfully!");
                    $player->getInventory()->addItem($item);
                } else {
                    $this->sendMessage($player, "§cCouldnt claim vote prize, inventory was full!");
                }
            }
        }
    }

    /**
     * @param string      $title
     * @param string      $msg
     * @param int         $type
     * @param string      $content
     * @param string|null $color
     */
    public function sendDiscordMessage(string $title, string $msg, int $type = 0, string $content = "", ?string $color = null) : void {
        if (!$this->getConfig()->get("send-discord", true)) return;
        $color = $color ?? "00FFFF";
        $webhook = "";
        $auc = [
            'Red'    => "https://ptb.discord.com/api/webhooks/",
            'Blue'   => "https://ptb.discord.com/api/webhooks/",
            'Green'  => "https://ptb.discord.com/api/webhooks/",
            'Red-OG' => "https://ptb.discord.com/api/webhooks/"
        ];
        $killlog = [
            'Red'    => "https://discord.com/api/webhooks/",
            'Blue'   => "https://discord.com/api/webhooks/",
            'Green'  => "https://discord.com/api/webhooks/",
            'Red-OG' => "https://discord.com/api/webhooks/"
        ];
        if ($type == 0) $webhook = "https://discord.com/api/webhooks/";
        elseif ($type == 1) $webhook = "https://discord.com/api/webhooks/";
        elseif ($type == 2) $webhook = $auc[TF::clean($this->sbtype)];
        elseif ($type == 3) $webhook = $killlog[TF::clean($this->sbtype)];
        elseif ($type === 4 or $type === 5) $webhook = "https://discord.com/api/webhooks/";
        elseif ($type === 6) $webhook = "https://discord.com/api/webhooks/";
        $msg .= "\nServer - {$this->server}-" . TF::clean($this->sbtype);
        $curlopts = [
            "embeds"  => [["title" => $title, "description" => $msg, "color" => hexdec($color)]],
            "content" => $content,
        ];
        $this->getServer()->getAsyncPool()->submitTask(new tasks\async\SendDiscord($webhook, serialize($curlopts)));
    }


    /**
     * @return bool
     */
    public function saveKOTH() : bool {
        $all = $this->arenaConf->getAll();
        if (isset($all["spawns"]) && $all["c1"] && $all["c2"]) {
            $this->arena = new Arena($this, $all["spawns"], ["c1" => $all["c1"], "c2" => $all["c2"]]);
            try {
                $this->arenaConf->save();
            } catch (JsonException) {
            }
            return true;
        }
        return false;
    }

    /**
     * @param SBPlayer $player
     * @param          $type
     */
    public function setPoint(Player $player, $type) : void {
        $pos = $player->getPosition();
        $save = $pos->getX() . ":" . $pos->getY() . ":" . $pos->getZ() . ":" . $pos->getWorld()->getDisplayName();
        $all = $this->arenaConf->getAll();
        if ($type === "spawn") {
            $all["spawns"][] = $save;
        } else {
            $all[$type] = $save;
        }
        $this->arenaConf->setAll($all);
        try {
            $this->arenaConf->save();
        } catch (JsonException) {
        }
    }

    /**
     * @return bool
     */
    public function startArena() : bool {
        $arena = $this->arena;
        if ($arena instanceof Arena) {
            $arena->preStart();
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function forceStop() : bool {
        $arena = $this->arena;
        if ($arena instanceof Arena) {
            $arena->resetGame();
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isRunning() : bool {
        $arena = $this->arena;
        if ($arena instanceof Arena) {
            if ($arena->isRunning()) return true;
        }
        return false;
    }

    /**
     * @param SBPlayer $player
     *
     * @return bool
     */
    public function sendToKoth(Player $player) : bool {
        $arena = $this->arena;
        if (!is_null($arena)) {
            if ($arena->isRunning()) {
                $arena->addPlayer($player);
                return true;
            }
        }
        return false;
    }

    /**
     * @param SBPlayer $player
     *
     * @return bool
     */
    public function removePlayer(Player $player) : bool {
        $arena = $this->arena;
        if ($arena instanceof Arena) {
            $arena->removePlayer($player);
            return true;
        }
        return false;
    }

    /**
     * @param SBPlayer $player
     *
     * @return bool
     */
    public function isInArena(Player $player) : bool {
        $arena = $this->arena;
        if ($arena instanceof Arena) {
            if ($arena->isInArena($player->getName())) {
                return true;
            } else    return false;
        }
        return false;
    }

}
