<?php

namespace SkyBlock\command\skyblock\admin;

use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

class DAutoSeller extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'dautoseller');
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!$this->plugin->hasOp($sender)) {
            $this->sendMessage($sender, "§4[Error] §cNo permission!");
            return;
        }
        if (!isset($args[1])) {
            $this->sendMessage($sender, "§cUsage: /is dautoseller <island>");
            return;
        }
        $islandName = strtolower($args[1]);
        if (!$this->db->isNameUsed($islandName)) {
            $this->sendMessage($sender, "§4[Error] §cIsland not found!");
            return;
        }
        if (($island = $this->im->getOnlineIsland($islandName)) !== null) {
            if ($island->getAutoSeller() > 0) {
                $island->removeAutoSeller();
                $this->sendMessage($sender, "§eDecrease autoseller count for island $islandName!");
            } else {
                $this->sendMessage($sender, "§ealready 0 lol!");
            }
        }
    }

}