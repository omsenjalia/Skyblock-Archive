<?php


namespace SkyBlock\command\skyblock\helper;


use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

class Leave extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'leave', "Leave an island");
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!isset($args[1])) {
            $this->sendMessage($sender, "§cUsage: /is leave <island name>");
            return;
        }
        $islandName = strtolower($args[1]);
        if (($island = $this->im->getOnlineIsland($islandName)) !== null) {
            if ($island->isOwner(strtolower($sender->getName()))) {
                $this->sendMessage($sender, "§4[Error] §cYou cannot leave the island if you're the owner! Use /is delete to delete the island or make someone else leader by /is makeleader");
                return;
            }
            if (!$island->isHelper($sender->getName())) {
                $this->sendMessage($sender, "§4[Error] §cYou are not a member of that island!");
                return;
            }
            $this->plugin->getChatHandler()->removePlayerFromChat($sender);
            $island->removeHelper($sender->getName());
            if ($island->isCoowner($sender->getName())) {
                $user->setIsland();
                $island->removeCoowner($sender->getName());
            }
            if ($island->isAdmin($sender->getName())) $island->removeAdmin($sender->getName());
            $user->removeIsland($args[1]);
            $this->sendMessage($sender, "§eYou left the island §a{$args[1]} §esuccessfully!");
        } else {
            if (!$this->db->isNameUsed($args[1])) {
                $this->sendMessage($sender, "§4[Error] §cIsland not found!");
                return;
            }
            $data = $this->db->getIslandInfoData($args[1]);
            $admin = [];
            $coowner = [];
            $helper = [];
            $admins = $data['admins'];
            if ($admins != "") $admin = explode(",", $admins);
            $coowners = $data['coowners'];
            if ($coowners != "") $coowner = explode(",", $coowners);
            $helpers = $data['helpers'];
            if ($helpers != "") $helper = explode(",", $helpers);
            if (!in_array(strtolower($sender->getName()), $helper, true)) {
                $this->sendMessage($sender, "§4[Error] §cYou are not a member of that island!");
            } else {
                $this->plugin->getChatHandler()->removePlayerFromChat($sender);
                $this->db->removeCoowner($sender->getName(), $args[1], $coowner);
                $this->db->removeAdmin($sender->getName(), $args[1], $admin);
                $this->db->removeHelper($sender->getName(), $args[1], $helper);
                $user->removeIsland($args[1]);
                $this->sendMessage($sender, "§eYou left the island §a{$args[1]} §esuccessfully!");
            }
        }
    }

}