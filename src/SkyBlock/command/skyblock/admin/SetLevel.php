<?php


namespace SkyBlock\command\skyblock\admin;


use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

class SetLevel extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'setlevel');
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!$this->plugin->hasOp($sender)) {
            $this->sendMessage($sender, "§4[Error] §cNo permission!");
            return;
        }
        if (!isset($args[1]) || !isset($args[2])) {
            $this->sendMessage($sender, "§cUsage: /is setlevel <island> <level>");
            return;
        }
        $islandName = $args[1];
        if (!$this->db->isNameUsed($args[1])) {
            $this->sendMessage($sender, "§4[Error] §cIsland not found!");
            return;
        }
        if (!is_int((int) $args[2])) {
            $this->sendMessage($sender, "§4[Error] §cLevel not valid!");
            return;
        }
        $args[2] = (int) $args[2];
        if (($island = $this->im->getOnlineIsland($islandName)) !== null) {
            $this->sendMessage($sender, "§a{$args[1]} §eIsland's level set to §d{$args[2]} §esuccessfully!");
            $island->setLevel($args[2]);
        } else {
            $this->db->setIslandLevel($args[1], $args[2]);
            $this->sendMessage($sender, "§a{$args[1]} §eIsland's level set to §d{$args[2]} §esuccessfully!");
        }
    }

}