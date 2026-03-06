<?php


namespace SkyBlock\command\skyblock\ban;


use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

class Unban extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'unban', 'Unban a player from your island');
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!$user->hasIsland()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be the Island Owner to use that command!");
        } else {
            if (!isset($args[1])) {
                $this->sendMessage($sender, "§6Usage: /is unban <player>");
                return;
            }
            $playerName = strtolower($args[1]);
            $islandName = $user->getIsland();
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $this->sendMessage($sender, "§4[Error]§c Island not online");
                return;
            }
            if (!$island->isBanned($playerName)) {
                $this->sendMessage($sender, "§4[Error]§c That player isnt banned to be unbanned!");
                return;
            }
            $island->removeBan($playerName);
            $this->sendMessage($sender, "§eYou have successfully unbanned §a{$playerName} §efrom your Island.");
        }
    }

}