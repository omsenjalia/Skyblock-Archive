<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class Lock extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'lock', 'Lock/unlock your island, then members/everybody will be able to join by /is tp', ['unlock']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be Island Owner/Coowner to use that command!");
        } else {
            $islandName = $user->getIsland();
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $this->sendMessage($sender, "§4[Error]§c Island not online");
                return;
            }
            $state = $island->getLocked();
            $nextstate = ($state == "false") ? "true" : "false";
            $island->setLocked($nextstate);
            $locked = ($nextstate == "true") ? "locked" : "unlocked";
            $this->sendMessage($sender, "§aYour island has been {$locked}!");
            if ($locked == "locked") $this->sendMessage($sender, "§aNow only members and players with roles can teleport to your island!");
        }
    }

}