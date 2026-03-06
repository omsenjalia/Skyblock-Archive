<?php


namespace SkyBlock\command;


use pmmp\RconServer\RconCommandSender;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\db\RecordDB;
use SkyBlock\Main;
use SkyBlock\util\Util;

class Records extends BaseCommand {

    /**
     * Records constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, "records", "Fallentech Records", "<type> [server]", true, ['record']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if ($sender instanceof RconCommandSender) {
            return;
        }
        $types = array_keys(RecordDB::$cache);
        $servers = ["red" => "Skyblock-Red", "blue" => "Skyblock-Blue", "green" => "Skyblock-Green", "redog" => "Skyblock-Red-OG", "facs" => "Factions"];
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /records <type> [server]" . "\nAvailable Types - " . implode(", ", $types) . "\nAvailable Servers - " . implode(", ", array_keys($servers)));
            return;
        }
        $type = strtolower($args[0]);
        if (!in_array($type, $types, true)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is not a valid record type. The available types are " . implode(", ", $types));
            return;
        }
        if (!isset($args[1])) {
            $server = "";
        } else {
            $server = strtolower($args[1]);
            if (!isset($servers[$server])) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is not a valid server. The available servers are " . implode(", ", array_keys($servers)));
                return;
            }
            $server = $servers[$server];
        }
        $onSelect = function(array $rows) use ($sender, $type, $server) : void {
            if ($server instanceof Player && !$server->isOnline()) {
                return;
            }
            $uType = ucfirst($type);
            $uServer = ($server === "") ? "All servers" : $server;
            $str = "§aTop §d$uType §aFallentech records in a season §7[§c{$uServer}§7] §a-" . "\n";
            if (empty($rows)) {
                $this->sendMessage($sender, $str . "- Empty");
                return;
            }
            foreach ($rows as $i => $row) {
                $no = $i + 1;
                $v = ($server === "") ? $row["server"] . " " : "";
                $season = "S" . $row["season"];
                $unit = ($row["unit"] === "") ? "" : " " . $row["unit"];
                $val = ($row["type"] === RecordDB::TOP_TIME_PLAYED) ? Util::getTimePlayed($row["value"]) : number_format($row["value"]);
                $str .= "§f" . $no . ". §b" . $row['name'] . " §f=> §e" . $val . $unit . " §7[§c" . $v . $season . "§7]\n";
            }
            $extra = ($server === "") ? " Use /records <type> <server> to see per server records!" : "";
            $sender->sendMessage($str . "§7=> Pages will be reloaded after restart! This list doesn't reset with season reset!$extra <=");
        };
        RecordDB::fetch($type, $server, $onSelect);
    }
}