<?php


namespace SkyBlock\command\skyblock\bank;


use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

class Bank extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'bank', "Shows Islands bank balance.", ['money']);
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!isset($args[1])) {
            if (!$user->isIslandSet()) {
                $this->sendMessage($sender, "§aYou dont own an island!");
                return;
            }
            $islandName = $user->getIsland();
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $this->sendMessage($sender, "§4[Error]§c Island not online");
                return;
            }
            $this->sendMessage($sender, "§a{$islandName} §eIsland's Bank: §6{$island->getMoney()}§e$" . "§7/§6{$island->getBankLimit()}$");
        } else {
            $islandName = $args[1];
            if (!$this->db->isNameUsed($islandName)) {
                $this->sendMessage($sender, "§4[Error] §cIsland not found!");
                return;
            }
            if (($island = $this->im->getOnlineIsland($args[1])) === null) {
                $money = $this->db->getIslandMoney($args[1]);
                $limit = $this->db->getIslandLevelData($args[1])['level'] * 25000;
                $this->sendMessage($sender, "§a{$args[1]} §eIsland's Bank: §6{$money}§e$" . "§7/§6$limit$");
            } else {
                $this->sendMessage($sender, "§a{$args[1]} §eIsland's Bank: §6{$island->getMoney()}§e$" . "§7/§6{$island->getBankLimit()}$");
            }
        }
    }

}