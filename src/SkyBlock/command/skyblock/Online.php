<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use SkyBlock\Main;
use SkyBlock\user\User;

class Online extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'online', "Shows all online islands with info");
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (isset($args[2])) {
            $this->sendMessage($sender, "§cUsage: /is online <page>");
            return;
        }
        if (!isset($args[1])) {
            $args[1] = 1;
        }
        if (isset($args[1]) and !is_int((int) $args[1]) or $args[1] < 1) {
            $this->sendMessage($sender, "§4[Error] §cEnter a valid page number!");
            return;
        }
        $args[1] = (int) $args[1];
        $islands = $this->im->getOnlineIslands();
        $total = count($islands);
        $pages = ceil($total / 8);
        $page = $args[1];
        if ($pages < $page) {
            $this->sendMessage($sender, "§4[Error]§e That page cannot be found.\nLast page = $pages");
            return;
        }
        $endnum = $page * 8;
        $startnum = $endnum - 7;
        $i = 1;
        $str = TF::GREEN . "All online Islands list -\n";
        $str .= TF::YELLOW . '[+]' . TF::WHITE . str_repeat('=', 10) . TF::AQUA . '[ ' . $page . '/' . $pages . ' ]' . TF::WHITE . str_repeat('=', 10) . TF::YELLOW . "[+]\n";
        foreach ($islands as $island) {
            if ($i <= $endnum and $i >= $startnum) {
                $str .= $i . ". §fName: §e{$island->getName()} §fOwner: §a{$island->getOwner()} §fLevel: §d{$island->getLevel()} §fPoints: §6{$island->getPoints()}§f/§6{$island->getPointsNeeded()} §fHelpers: §9{$island->getHelperCount()}\n§fMotd: §b{$island->getMotd()}\n";
            }
            ++$i;
        }
        $this->sendMessage($sender, $str . TF::YELLOW . '[+]' . TF::WHITE . str_repeat('=', 26) . TF::YELLOW . "[+]\n§e=> §6For more info on an island, use /is info <island>! §e<=");
    }

}