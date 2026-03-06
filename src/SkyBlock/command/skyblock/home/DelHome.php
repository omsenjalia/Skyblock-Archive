<?php


namespace SkyBlock\command\skyblock\home;


use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

class DelHome extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'delhome', 'Unset an island home', ['deletehome']);
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!isset($args[1]) or isset($args[2])) {
            $this->sendMessage($sender, "§6Usage: /is delhome <home name>");
            return;
        }
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be Island owner/coowner to use that command.");
            return;
        }
        $islandName = $user->getIsland();
        if (($island = $this->im->getOnlineIsland($islandName)) === null) {
            $this->sendMessage($sender, "§4[Error] §cIsland not online!");
            return;
        }
        if (!$island->hasHome($args[1])) {
            $this->sendMessage($sender, "§4[Error]§c You haven't set that home! Use /is sethome to set homes!");
            return;
        }
        $island->removeHome($args[1]);
        $this->sendMessage($sender, "§eIsland Home §a{$args[1]} §edeleted");
    }

}