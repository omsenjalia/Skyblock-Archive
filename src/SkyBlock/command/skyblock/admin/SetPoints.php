<?php


namespace SkyBlock\command\skyblock\admin;


use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

class SetPoints extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'setpoints');
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!$this->plugin->hasOp($sender)) {
            $this->sendMessage($sender, "§4[Error] §cNo permission!");
            return;
        }
        if (!isset($args[1]) || !isset($args[2])) {
            $this->sendMessage($sender, "§cUsage: /is setpoints <island> <points>");
            return;
        }
        $islandName = $args[1];
        if (!$this->db->isNameUsed($args[1])) {
            $this->sendMessage($sender, "§4[Error] §cIsland not found!");
            return;
        }
        if (!is_int((int) $args[2])) {
            $this->sendMessage($sender, "§4[Error] §cPoints not valid!");
            return;
        }
        $args[2] = (int) $args[2];
        if (($island = $this->im->getOnlineIsland($islandName)) !== null) {
            $this->sendMessage($sender, "§eAdded §d{$args[2]} §epoints to §a{$args[1]} §eIsland successfully!");
            $island->setPoints($args[2]);
        } else {
            $this->sendMessage($sender, "§a{$args[1]} §eIsland not online!");
        }
    }

}