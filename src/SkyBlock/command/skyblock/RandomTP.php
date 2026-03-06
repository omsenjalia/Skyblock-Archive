<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class RandomTP extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'randomtp', "Teleport to an unlocked random online island.", ['rtp']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (isset($args[1])) {
            $this->sendMessage($sender, "§cUsage: /is rtp");
            return;
        }
        $i = 0;
        $islands = $this->im->getOnlineIslands();
        $keys = array_keys($islands);
        shuffle($keys);
        $random = array();
        foreach ($keys as $key) $random[$key] = $islands[$key];
        $island = null;
        foreach ($random as $island) {
            if ($user->isIslandSet()) {
                if (strtolower($user->getIsland()) == strtolower($island->getName())) continue;
            }
            if (is_null($island->getWorldLevel())) continue;
            if (is_null($island->getWorldLevel()->getProvider())) continue;
            if ($island->isBanned(strtolower($sender->getName())) or $island->isLocked() or $island->isIslandFullForVisitors()) continue;
            elseif ($this->um->getOnlineUser($island->getOwner()) === null) continue;
            else {
                $i = 1;
                $island->teleport($sender);
                break;
            }
        }
        if ($i == 1) {
            $this->sendMessage($this->um->getOnlineUser($island->getOwner())->getPlayer(), "§a{$sender->getName()} §ejust teleported to your island by /is randomtp! Lock your island by /is lock");
            $this->sendMessage($sender, "§eYou teleported to island §a{$island->getName()}§e's spawn successfully by /is randomtp");
        } else {
            $this->sendMessage($sender, "§cNone of the online islands are unlocked to teleport!");
        }
    }

}