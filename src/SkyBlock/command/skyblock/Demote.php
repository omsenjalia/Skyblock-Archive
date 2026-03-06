<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\perms\Permission;
use SkyBlock\user\User;

class Demote extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'demote', 'Demote an Admin or Coowner to Helper of island', ['demo', 'removeadmin', 'rmadmin']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!isset($args[1])) {
            $this->sendMessage($sender, "§6Usage: /is demote <player>");
            return;
        }
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be Island Owner/CoOwner to use that command.");
            return;
        }
        $islandName = $user->getIsland();
        if (($island = $this->im->getOnlineIsland($islandName)) === null) {
            $this->sendMessage($sender, "§4[Error] §cIsland not online!");
            return;
        }
        if (!$island->hasPerm($sender->getName(), Permission::MANAGER)) {
            $this->sendMessage($sender, TextFormat::RED . "You dont have managing perms on this island");
            return;
        }
        $player = strtolower($args[1]);
        if ($player === strtolower($sender->getName())) {
            $this->sendMessage($sender, "§4[Error] §cYou cannot demote yourself!");
            return;
        }
        if (!$island->isHelper($player)) {
            $this->sendMessage($sender, "§4[Error] §cPlayer not a member of this Island");
            return;
        }
        if ($island->isCoowner($player)) {
            $island->removeCoowner($player);
            $island->addAdmin($player);
            if (($user2 = $this->um->getOnlineUser($player)) !== null) {
                $user2->setIsland();
                $this->sendMessage($user2->getPlayer(), "§cYou have been demoted to Admin on §e{$islandName} §cisland!");
            }
            $this->sendMessage($sender, "§a$player §ehas been demoted to Admin!");
        } else {
            if ($island->isAdmin($player)) {
                $island->removeAdmin($player);
                if (($user2 = $this->um->getOnlineUser($player)) !== null) {
                    $this->sendMessage($user2->getPlayer(), "§cYou have been demoted to Helper on §e{$islandName} §cisland!");
                }
                $this->sendMessage($sender, "§a$player §ehas been demoted to Helper!");
            } else {
                $this->sendMessage($sender, "§4[Error] §cPlayer is not an Admin or CoOwner on your island! Use /is remove to remove the helper!");
            }
        }
    }

}