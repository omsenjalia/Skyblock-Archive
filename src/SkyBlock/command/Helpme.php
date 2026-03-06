<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Helpme extends BaseCommand {
    /**
     * Helpme constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'helpme');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0])) {
            $args[0] = 1;
        }
        if (!isset($args[1])) {
            $args[1] = 1;
        }
        if (!is_int((int) $args[1])) {
            return;
        }
        $args[1] = (int) $args[1];
        if ($args[1] < 1) {
            return;
        }
        $commands = [
            "ah"             => "Auction house help.",
            "pref"           => "Your Server Preferences.",
            "sethome"        => "Set a home in nether.",
            "delhome"        => "Delete a home.",
            "home"           => "Teleport to home.",
            "id"             => "Shows your player unique number/id.",
            "ps"             => "Get info about player using id or username.",
            "blocksbroken"   => "Total Ore Blocks Broken by Server",
            "warp"           => "Warp Menu.",
            "paymana"        => "Pay Mana to other players",
            "mymana"         => "Check your mana.",
            "pos"            => "Check your coords.",
            "topmana"        => "See Top Mana players list.",
            "seemana"        => "See a players mana.",
            "browse"         => "Browse Fallentech usernames.",
            "updates"        => "Checkout last server update.",
            "clearlagtime"   => "Time left before clear entities task.",
            "vaulted"        => "See Vaulted CEs.",
            "sclist"         => "Sign commands currently available.",
            "peace"          => "Play in peace mode, no messages.",
            "ext"            => "Extinguish yourself.",
            "disposal"       => "Delete held item.",
            "lastseen"       => "Check player's rank and last seen.",
            "report"         => "Report a player to staff on discord.",
            "headsell"       => "Sell player heads from your inventory",
            "block"          => "Block a player. You cant see their texts",
            "unblock"        => "Unblock a player.",
            "blocklist"      => "Check your block list.",
            "rtime"          => "Time left for server restart.",
            "condense"       => "Turn resources into blocks.",
            "seexp"          => "See XP of other players.",
            "xpbank"         => "XPBank Help.",
            "offlist"        => "List of all the players offenses count.",
            "gang"           => "Gangs help.",
            "rules"          => "Server rules.",
            "topxp"          => "See Top XP players list",
            "records"        => "See Fallentech mc records",
            "prestige"       => "Prestige Help",
            "info"           => "Server help.",
            "timeplayed"     => "Check your time played on server.",
            "servers"        => "Transfer to other servers.",
            "envoy"          => "Check envoy spawn time.",
            "dc"             => "Delete the Chest you're looking at.",
            "profile"        => "Check any player's profile.",
            "celist"         => "All custom enchants available list!",
            "buy"            => "Server shop menu",
            "koth"           => "KOTH help",
            "brag"           => "Brag to a player about your item in hand",
            "ceshop"         => "Buy custom enchant books for XP",
            "ceinfo"         => "See what a custom enchant does",
            "cleareffects"   => "Clear all your running effects",
            "clearinventory" => "Clear your inventory.",
            "combiner"       => "Combine cebooks with your tool/armor to custom enchant it",
            "merger"         => "Merge same books in inv to give better accuracy book.",
            "ench"           => "Combine enchantment orbs with your tool/armor to enchant it",
            "enchanter"      => "Merge enchanter scroll with your CEBook to increase accuracy",
            "levelup"        => "Merge levelup scroll with your CE to increase it's level",
            "inferno"        => "Merge inferno scroll with your vanilla enchant to increase it till level 10",
            "vulcan"         => "Merge Vulcan scroll with your tool to remove CE from it",
            "carver"         => "Merge Carver scroll with your tool to remove vanilla enchant from it",
            "fixer"          => "Merge fixer scroll with your tool to fix it",
            "hub"            => "Teleport to server spawn.",
            "scoreboard"     => "Switch scoreboard on/off",
            "vote"           => "Vote to get amazing rewards",
            "locate"         => "Locate a player on the Fallentech servers",
            "is"             => "SkyBlock help",
            "myxp"           => "Check your XP",
            "manashop"       => "Mana shop",
            "mobcoinshop"    => "Mob Coin shop",
            "mychips"        => "Check your casino chips",
            "pay"            => "Pay someone your money",
            "paymc"          => "Pay someone your mob coins",
            "match"          => "Join 1vs1 queue to duel",
            "myislands"      => "Teleport to one of your islands you're member of.",
            "topmoney"       => "See server top money players",
            "topmobcoin"     => "See server top mob coins players",
            "rename"         => "Rename your tool/armor",
            "staff"          => "See Staff List in game!",
            "seemoney"       => "Check a player's money",
            "seemobcoin"     => "Check a player's mob coins",
            "sellhand"       => "Sell your held item for money",
            "sellhandxp"     => "Sell your held item for XP",
            "spawner"        => "Spawner Shop Menu",
            "upgrade"        => "Upgrade Custom Blocks Shop Menu",
            "kit"            => "Claim your kit",
            "kill"           => "Commit suicide and clears inventory",
            "mcstats"        => "Check your mcmmo stats",
            "mctop"          => "See top mcmmo players",
            "tpa"            => "Send teleport request to a player",
            "tpahere"        => "Send teleport here request to a player",
            "tell"           => "Private message someone",
            "reply"          => "Reply someone who private messaged you",
            "itemcloud"      => "Itemcloud help",
            "topwins"        => "Check top casino winners",
            "spawn"          => "Teleport to Server spawn.",
            "ranks"          => "See all ranks and its order."
        ];
        if (is_int($args[0])) {
            if ($args[0] < 1) {
                return;
            }
            ksort($commands);
            $this->sendCommandsHelp($sender, $commands, "Commands", $args[0]);
            $this->sendMessage($sender, TextFormat::YELLOW . "Use /helpme <rank> to see a ranks commands!");
        } else {
            switch (strtolower($args[0])) {
                case "guest":
                    $this->sendMessage($sender, "§eUse /helpme <page> to see Guest's commands");
                    break;
                case "king":
                    $commands = [
                        "sellall"   => "Sell everything sellable from your inventory.",
                        "sellallxp" => "Sell everything sellable from your inventory for XP.",
                        "kit king"  => "Get King kit with full diamond armor and much more",
                    ];
                    $this->sendCommandsHelp($sender, $commands, "King", $args[1]);
                    $sender->sendMessage("§eKill Booster 2 included");
                    $sender->sendMessage("§eFull server join: §7Join even when server's full");
                    break;
                case "vip":
                    $commands = [
                        "fly"     => "Access to /fly",
                        "kit vip" => "Get VIP kit with full diamond armor and much more",
                    ];
                    $this->sendCommandsHelp($sender, $commands, "VIP", $args[1]);
                    $sender->sendMessage("§eParent: §7Get everything from /helpme King");
                    break;
                case "myth":
                    $commands = [
                        "fix"      => "Get /fix to fix your tool/armor",
                        "heal"     => "Heal yourself anytime anywhere",
                        "kit myth" => "Get Myth kit with full diamond armor and much more",
                    ];
                    $this->sendCommandsHelp($sender, $commands, "Myth", $args[1]);
                    $sender->sendMessage("§eParent: §7Get everything from /helpme VIP");
                    break;
                case "skylord":
                    $commands = [
                        "fix <player>" => "Fix any player's tool/armor",
                        "time"         => "Control server's time day/night",
                        "kit SkyLord"  => "Get SkyLord kit with full diamond armor and much more",
                    ];
                    $this->sendCommandsHelp($sender, $commands, "SkyLord", $args[1]);
                    $sender->sendMessage("§eKill Booster 3 included");
                    $sender->sendMessage("§eParent: §7Get everything from /helpme Myth");
                    break;
                case "skygod":
                    $commands = [
                        "enchant"    => "Enchant any player's or your held item till 6 levels! 5,000$ per level",
                        "fixall"     => "Fix all items in inventory! 5,000$ per item",
                        "feed"       => "Feed any player! 5,000$ per feed",
                        "kit SkyGOD" => "Get SkyGOD kit with full diamond armor and much more",
                    ];
                    $this->sendCommandsHelp($sender, $commands, "SkyGOD", $args[1]);
                    $sender->sendMessage("§eKill Booster 5 included");
                    $sender->sendMessage("§eParent: §7Get everything from /helpme SkyLord");
                    break;
                case "skyzeus":
                    $commands = [
                        "effect"      => "Give effects to yourself just for 2500$ per level!",
                        "vanish"      => "Hide from other players, be invisible",
                        "kit SkyZEUS" => "Get SkyZEUS kit with full diamond armor and much more",
                    ];
                    $this->sendCommandsHelp($sender, $commands, "SkyZEUS", $args[1]);
                    $sender->sendMessage("§eKill Booster 10 included");
                    $sender->sendMessage("§eParent: §7Get everything from /helpme SkyGOD");
                    break;
                case "skyelite":
                    $commands = [
                        "removece"     => "Remove ces from held item!",
                        "kit SkyELITE" => "Get SkyELITE kit with full diamond armor, better tools and much more",
                    ];
                    $this->sendCommandsHelp($sender, $commands, "SkyELITE", $args[1]);
                    $sender->sendMessage("§eKill Booster 12 included");
                    $sender->sendMessage("§eParent: §7Get everything from /helpme SkyZEUS");
                    break;
                case "skyhulk":
                    $commands = [
                        "removevanillaench" => "Remove vanilla enchant from held item!",
                        "chatsize"          => "Increase chat size!",
                        "kit SkyHULK"       => "Get SkyHULK kit with full diamond armor, better tools and much more",
                    ];
                    $this->sendCommandsHelp($sender, $commands, "SkyHULK", $args[1]);
                    $sender->sendMessage("§eKill Booster 15 included");
                    $sender->sendMessage("§eBigger chat by /chatsize");
                    $sender->sendMessage("§eParent: §7Get everything from /helpme SkyELITE");
                    break;
                case "skywarrior":
                    $commands = [
                        "condenseall"    => "Condense whole inventory, change ores to blocks!",
                        "claimallkits"   => "Claim all valid kits at once!",
                        "rainbow"        => "Send colorful chat!",
                        "kit SkyWARRIOR" => "Get SkyWARRIOR kit with full diamond armor, better tools and much more",
                    ];
                    $this->sendCommandsHelp($sender, $commands, "SkyWARRIOR", $args[1]);
                    $sender->sendMessage("§eKill Booster 15 included");
                    $sender->sendMessage("§e8 Auction House slots");
                    $sender->sendMessage("§eParent: §7Get everything from /helpme SkyHULK");
                    break;
                case "youtuber":
                case "streamer":
                    $commands = [
                        "chatsize"    => "Control chatsize!",
                        "feed"        => "Feed yourself!",
                        "heal"        => "Heal yourself!",
                        "fix"         => "Fix yourself or a player!",
                        "fixall"      => "Fix all items in inventory!",
                        "vanish"      => "Go invisible, also players cant /msg you",
                        "fly"         => "Fly on server!",
                        "sellall"     => "Sell everything sellable from your inventory!",
                        "sellallxp"   => "Sell everything sellable from your inventory for XP!",
                        "kit Partner" => "Get Partner kit with full diamond armor and much more",
                    ];
                    $this->sendCommandsHelp($sender, $commands, "Partner", $args[1]);
                    break;
                case "trainee":
                case "builder":
                case "helper":
                case "coowner":
                case "head-admin":
                case "admin":
                case "mod":
                case "owner":
                    $this->sendMessage($sender, "Confidential Information");
                    break;
                default:
                    $this->sendMessage($sender, "§4[Error]§c Rank not found! Use /ranks to see all ranks or use /helpme <page>!");
                    break;
            }

        }
    }

    private function sendCommandsHelp(Player $sender, array $commands, string $type, int $page = 1) : bool {
        $total = count($commands);
        $pages = ceil($total / 8);
        if ($pages < $page) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That page cannot be found. Last page is $pages");
            return false;
        }
        $endNumber = $page * 8;
        $startNumber = $endNumber - 7;
        $i = 1;
        $str = TextFormat::DARK_GREEN . "-----------" . TextFormat::AQUA . " [" . TextFormat::GREEN . "$type Help" . TextFormat::AQUA . "] " . TextFormat::RESET . TextFormat::DARK_GREEN . "-----------\n";
        foreach ($commands as $command => $description) {
            if ($i >= $startNumber and $i <= $endNumber)
                $str .= TextFormat::AQUA . "§e/" . TextFormat::GREEN . "§e{$command}: " . TextFormat::RESET . TextFormat::GRAY . $description . "\n";
            $i++;
        }
        $this->sendMessage($sender, $str . TextFormat::DARK_GREEN . "-----------------" . TextFormat::AQUA . " [" . TextFormat::GREEN . "$page/$pages" . TextFormat::AQUA . "] " . TextFormat::RESET . TextFormat::DARK_GREEN . "-----------------");
        return true;

    }
}