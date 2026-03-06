<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\user\User;

class Help extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'help', 'Skyblock commands help', ['helpme']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (isset($args[2])) {
            $this->sendMessage($sender, "§4Usage: /is help <page>");
            return;
        }
        if (!isset($args[1])) {
            $args[1] = 1;
        }
        if (!is_int((int) $args[1]) or $args[1] < 1) {
            $this->sendMessage($sender, "§4[Error]§e Please enter a valid page number!");
            return;
        }
        $args[1] = (int) $args[1];
        $commands = $this->pl->sf->getSkyblockCommands();
        ksort($commands);
        $total = count($commands);
        $limit = 8;
        $pages = ceil($total / $limit);
        $page = $args[1];
        if ($pages < $page) {
            $this->sendMessage($sender, "§4[Error]§e That page cannot be found.\nLast page = $pages");
            return;
        }
        $startnum = ($page * $limit) - $limit;
        $commands = array_splice($commands, $startnum, $limit);
        $str = TextFormat::DARK_GREEN . "-----------" . TextFormat::AQUA . " [" . TextFormat::GREEN . "SkyBlockPE Help" . TextFormat::AQUA . "] " . TextFormat::RESET . TextFormat::DARK_GREEN . "-----------\n";
        foreach ($commands as $command => $description) {
            $str .= TextFormat::AQUA . "§e/" . TextFormat::GREEN . "§eis {$command}: " . TextFormat::RESET . TextFormat::GRAY . $description . "\n";
        }
        $sender->sendMessage($str . TextFormat::DARK_GREEN . "-----------------" . TextFormat::AQUA . " [" . TextFormat::GREEN . "$page/$pages" . TextFormat::AQUA . "] " . TextFormat::RESET . TextFormat::DARK_GREEN . "-----------------");
    }

}