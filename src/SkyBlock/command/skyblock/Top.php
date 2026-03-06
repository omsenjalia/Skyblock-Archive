<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use SkyBlock\Main;
use SkyBlock\user\User;

class Top extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'top', "Shows all top islands by level or money");
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!isset($args[1])) {
            $this->sendMessage($sender, "§cUsage: /is top <level | money> <page>");
            return;
        }
        if ($args[1] == 'level') {
            if (!isset($args[2])) {
                $args[2] = 1;
            }
            if (isset($args[2]) and !is_int((int) $args[2]) or $args[2] < 1) {
                $this->sendMessage($sender, "§4[Error]§e Please enter a valid page number!");
                return;
            }
            $args[2] = (int) $args[2];
            $array = $this->plugin->db->prepare("SELECT count(*) as count FROM level ORDER BY level DESC;")->execute();
            $array = $array->fetchArray(SQLITE3_ASSOC);
            $total = $array['count'];
            $pages = ceil($total / 10);
            $page = $args[2];
            if ($pages < $page) {
                $this->sendMessage($sender, "§4[Error]§e That page cannot be found.\nLast page = $pages");
                return;
            }
            $startnum = ($page - 1) * 10;
            $str = TF::GREEN . "Top islands by level list -\n";
            $str .= TF::YELLOW . '[+]' . TF::WHITE . str_repeat('=', 10) . TF::AQUA . '[ ' . $page . '/' . $pages . ' ]' . TF::WHITE . str_repeat('=', 10) . TF::YELLOW . '[+]' . "\n";
            $array = $this->plugin->db->prepare("SELECT name, level, points FROM level ORDER BY level DESC LIMIT $startnum, 10;")->execute();
            while ($result = $array->fetchArray(SQLITE3_ASSOC)) {
                $islandName = $result['name'];
                $level = $result['level'];
                $points = $result['points'];
                $npoints = $level * 150;
                ++$startnum;
                if (($online = $this->im->getOnlineIsland($islandName)) === null) {
                    $str .= $startnum . ". §fIsland: §a$islandName §e=> §fLevel: §d$level §fPoints: §6{$points}§f/§6{$npoints}\n";
                } else {
                    $str .= $startnum . ". §fIsland: §a$islandName §e=> §fLevel: §d{$online->getLevel()} §fPoints: §6{$online->getPoints()}§f/§6{$online->getPointsNeeded()}\n";
                }
            }
            $sender->sendMessage($str . TF::YELLOW . '[+]' . TF::WHITE . str_repeat('=', 26) . TF::YELLOW . '[+]' . "\n=> Pages will be reloaded after restart! <=\n" . "§e=> §6For more info on an island, use /is info <island>! §e<=");
            return;
        }
        if ($args[1] == 'money') {
            if (!isset($args[2])) {
                $args[2] = 1;
            }
            if (isset($args[2]) and !is_int((int) $args[2]) or $args[2] < 1) {
                $this->sendMessage($sender, "§4[Error]§e Please enter a valid page number!");
                return;
            }
            $args[2] = (int) $args[2];
            $array = $this->plugin->db->prepare("SELECT count(*) as count FROM bank ORDER BY money DESC;")->execute();
            $array = $array->fetchArray(SQLITE3_ASSOC);
            $total = $array['count'];
            $pages = ceil($total / 10);
            $page = $args[2];
            if ($pages < $page) {
                $this->sendMessage($sender, "§4[Error]§e That page cannot be found.\nLast page = $pages");
                return;
            }
            $startnum = ($page - 1) * 10;
            $str = TF::GREEN . "Top islands by money list -\n";
            $str .= TF::YELLOW . '[+]' . TF::WHITE . str_repeat('=', 10) . TF::AQUA . '[ ' . $page . '/' . $pages . ' ]' . TF::WHITE . str_repeat('=', 10) . TF::YELLOW . '[+]' . "\n";
            $array = $this->plugin->db->prepare("SELECT name, money FROM bank ORDER BY money DESC LIMIT $startnum, 10;")->execute();
            while ($result = $array->fetchArray(SQLITE3_ASSOC)) {
                $islandName = $result['name'];
                $money = $result['money'];
                ++$startnum;
                if (($online = $this->im->getOnlineIsland($islandName)) === null) {
                    $str .= $startnum . ". §fIsland: §a$islandName §e=> §fMoney: §6$money$ \n";
                } else {
                    $str .= $startnum . ". §fIsland: §a$islandName §e=> §fMoney: §6{$online->getMoney()}$ \n";
                }
            }
            $sender->sendMessage($str . TF::YELLOW . '[+]' . TF::WHITE . str_repeat('=', 26) . TF::YELLOW . '[+]' . "\n=> Pages will be reloaded after restart! <=\n§e=> §6For more info on an island, use /is info <island>! §e<=");
        } else {
            $this->sendMessage($sender, "§cUsage: /is top <level | money> <page>");
        }
    }

}