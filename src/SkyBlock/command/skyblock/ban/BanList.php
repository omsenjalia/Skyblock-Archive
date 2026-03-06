<?php


namespace SkyBlock\command\skyblock\ban;


use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

class BanList extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'banlist', 'Check your island banlist', ['bans']);
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be Island Owner/Coowner to use that command!");
        } else {
            $islandName = $user->getIsland();
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $this->sendMessage($sender, "§4[Error]§c Island not online");
                return;
            }
            $this->sendMessage($sender, "_==_| {$islandName}'s §aBan List |_==_");
            $this->sendMessage($sender, "§aBans§7[§c{$island->getBanCount()}§7]§a: §f{$island->getBanString()}");
        }
    }

}