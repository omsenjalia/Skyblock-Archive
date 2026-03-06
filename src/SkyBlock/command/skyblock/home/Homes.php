<?php


namespace SkyBlock\command\skyblock\home;


use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

class Homes extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'homes', 'See all your island homes');
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!isset($args[1])) {
            if (!$user->isIslandSet()) {
                $this->sendMessage($sender, "§4[Error]§e You do not own any island to see homes of, §cuse /is homes <island name>");
                return;
            }
            $islandName = $user->getIsland();
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $this->sendMessage($sender, "§4[Error]§c Island not online");
                return;
            }
            if (!$island->hasHomes()) {
                $this->sendMessage($sender, "§4[Error]§c No Island homes have been set yet! Set Island home using /is sethome <home name>");
            } else {
                $this->sendMessage($sender, "§eAvailable §a{$island->getName()} §eHomes: §a{$island->getHomesString()}");
            }
        } else {
            if (!$this->db->isNameUsed($args[1])) {
                $this->sendMessage($sender, "§4[Error] §cIsland not found!");
                return;
            }
            if (($island = $this->im->getOnlineIsland($args[1])) === null) {
                $this->sendMessage($sender, "§4[Error] §cIsland not online!");
                return;
            }
            if (!$island->isCoowner($sender->getName()) and !$island->isAdmin($sender->getName())) {
                $this->sendMessage($sender, "You must be an Admin or CoOwner of that Island to see homes!");
            } else {
                if (!$island->hasHomes()) {
                    $this->sendMessage($sender, "§4[Error]§c No Island homes have been set yet!");
                } else {
                    $this->sendMessage($sender, "§eAvailable §a{$island->getName()} §eHomes: §a{$island->getHomesString()}");
                }
            }
        }
    }

}