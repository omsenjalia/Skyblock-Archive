<?php


namespace SkyBlock\command\skyblock\bank;


use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

class Withdraw extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'withdraw', "Withdraw money from island bank");
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!isset($args[1])) {
            $this->sendMessage($sender, "§6Usage: /is withdraw <money>");
            return;
        }
        if (!$user->hasIsland()) {
            $this->sendMessage($sender, "§aYou don't own an island to withdraw money from!");
            return;
        }
        $islandName = $user->getIsland();
        if (($island = $this->im->getOnlineIsland($islandName)) === null) {
            $this->sendMessage($sender, "§4[Error]§c Island not online");
            return;
        }
        if (!is_int((int) $args[1]) or $args[1] <= 0) {
            $this->sendMessage($sender, "§4[Error]§c Please enter a valid number to withdraw!");
            return;
        }
        $args[1] = (int) $args[1];
        if (!$island->hasMoney($args[1])) {
            $this->sendMessage($sender, "§4[Error]§c Your island doesn't have that amount of money to withdraw! Balance: §6{$island->getMoney()}§c$");
            return;
        }
        $island->removeMoney($args[1]);
        $user->addMoney($args[1], false);
        $this->sendMessage($sender, "§6{$args[1]}§e$ withdrawn from island bank! Balance: §6{$island->getMoney()}§e$");
        $this->sendMessage($sender, "§eYou have earned §6{$args[1]}§e$! Balance: §6{$user->getMoney()}§e$");
    }

}