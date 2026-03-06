<?php


namespace SkyBlock\command\skyblock\bank;


use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

class Donate extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'donate', "Donate money to your island bank");
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!isset($args[1])) {
            $this->sendMessage($sender, "§4Usage: /is donate <amount> <island>");
            return;
        }
        if ((!is_int((int) $args[1])) or ($args[1] < 5000)) {
            $this->sendMessage($sender, "§4Usage: /is donate <amount> <island>\n§4[Error] §cYou cannot donate less than 5000$");
            return;
        }
        $args[1] = (int) $args[1];
        if (!isset($args[2])) {
            if (!$user->isIslandSet()) {
                $this->sendMessage($sender, "§aYou dont own an island, so use /is donate <amount> <island> to donate money to!");
                return;
            }
            $islandName = $user->getIsland();
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $this->sendMessage($sender, "§4[Error]§c Island not online");
                return;
            }
            $args[1] = (int) $args[1];
            if (($money = $args[1] + $island->getMoney()) > $island->getBankLimit()) {
                $this->sendMessage($sender, "§eYou cannot donate §6$money$ §ebecause it exceeds the Island Bank limit §6{$island->getBankLimit()}$ §eat your Island level {$island->getLevel()}! Increase Island level by building or mining!");
                return;
            }
            if ($user->removeMoney($args[1])) {
                $this->sendMessage($sender, "§eAdded §6{$args[1]}§e$ to §a$islandName §eisland's bank successfully!");
                $island->addMoney($args[1]);
            } else {
                $this->sendMessage($sender, "§4[Error]§c You don't have §6{$args[1]}§c$ §cto donate the island!");
            }
        } elseif (isset($args[2])) {
            if (!$this->db->isNameUsed($args[2])) {
                $this->sendMessage($sender, "§4[Error] §cIsland not found!");
                return;
            }
            if (($island = $this->im->getOnlineIsland($args[2])) === null) {
                $helpers = $this->db->getHelpers($args[2]);
                $helper = explode(",", $helpers);
                if (!in_array(strtolower($sender->getName()), $helper, true)) {
                    $this->sendMessage($sender, "§4[Error] §cYou are not a member of {$args[2]} island to donate to that island!");
                    return;
                }
                $args[1] = (int) $args[1];
                $imoney = $this->db->getIslandMoney($args[2]);
                $ilevel = $this->db->getIslandLevelData($args[2])['level'];
                $limit = $ilevel * 25000;
                if (($money = $args[1] + $imoney) > ($limit)) {
                    $this->sendMessage($sender, "§eYou cannot donate §6$money$ §ebecause it exceeds the Island Bank limit §6{$limit}$ §eat that Island level $ilevel! Increase Island level by building or mining!");
                    return;
                }
                if ($user->removeMoney($args[1])) {
                    $this->sendMessage($sender, "§eAdded §6{$args[1]}§e$ to §a{$args[2]} §eisland's bank successfully!");
                    $this->db->addIslandMoney($args[2], $args[1]);
                } else {
                    $this->sendMessage($sender, "§4[Error]§c You don't have §6{$args[1]}§c$ to donate the island!");
                }
            } else {
                if (!$island->isMember($sender->getName())) {
                    $this->sendMessage($sender, "§4[Error]§c You are not member of §6{$args[2]}§c island to donate to that island!");
                    return;
                }
                $args[1] = (int) $args[1];
                if (($money = $args[1] + $island->getMoney()) > $island->getBankLimit()) {
                    $this->sendMessage($sender, "§eYou cannot donate §6$money$ §ebecause it exceeds the Island Bank limit §6{$island->getBankLimit()}$ §eat your Island level {$island->getLevel()}! Increase Island level by building or mining!");
                    return;
                }
                if ($user->removeMoney($args[1])) {
                    $this->sendMessage($sender, "§eAdded §6{$args[1]}§e$ to §a{$args[2]} §eisland's bank successfully!");
                    $island->addMoney($args[1]);
                } else {
                    $this->sendMessage($sender, "§4[Error]§c You don't have §6{$args[1]}§c$ to donate the island!");
                }
            }
        }
    }

}