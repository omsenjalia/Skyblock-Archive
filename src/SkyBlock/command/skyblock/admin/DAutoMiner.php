<?php

namespace SkyBlock\command\skyblock\admin;

use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

class DAutoMiner extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'dautominer');
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!$this->plugin->hasOp($sender)) {
            $this->sendMessage($sender, "§4[Error] §cNo permission!");
            return;
        }
        if (!isset($args[1])) {
            $this->sendMessage($sender, "§cUsage: /is dautominer <island>");
            return;
        }
        $islandName = strtolower($args[1]);
        if (!$this->db->isNameUsed($islandName)) {
            $this->sendMessage($sender, "§4[Error] §cIsland not found!");
            return;
        }
        if (($island = $this->im->getOnlineIsland($islandName)) !== null) {
            if ($island->getAutoMiner() > 0) {
                $island->removeAutoMiner();
                $this->sendMessage($sender, "§eDecrease autominer count for island $islandName!");
            } else {
                $this->sendMessage($sender, "§ealready 0 lol!");
            }
        }
    }

}