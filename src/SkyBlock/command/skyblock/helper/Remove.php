<?php


namespace SkyBlock\command\skyblock\helper;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\perms\Permission;
use SkyBlock\user\User;

class Remove extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'remove', "Remove a player from your island", ['removehelper', 'rmhelper']);
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou must be an Island owner/coowner to remove a user!");
        } else {
            if (!isset($args[1])) {
                $this->sendMessage($sender, "§cUsage: /is remove <player>");
                return;
            }
            $islandName = $user->getIsland();
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $this->sendMessage($sender, "§4[Error]§c Island not online");
                return;
            }
            if (!$island->hasPerm($sender->getName(), Permission::MANAGER)) {
                $this->sendMessage($sender, TextFormat::RED . "You dont have managing perms on this island");
                return;
            }
            if ($island->isOwner($args[1])) {
                $this->sendMessage($sender, "§4[Error] §cYou can't remove owner from the island! Use /is delete to delete the island!");
                return;
            }
            if ($island->isCoowner($args[1])) {
                $this->sendMessage($sender, "§4[Error] §c{$args[1]} is a Coowner on island, use /is demote to demote them first!");
                return;
            }
            if ($island->isAdmin($args[1])) {
                $this->sendMessage($sender, "§4[Error] §c{$args[1]} is an Admin on island, use /is demote to demote them first!");
                return;
            }
            if (!$island->isHelper($args[1])) {
                $this->sendMessage($sender, "§4[Error] §c{$args[1]} isn't a player of your island!");
                return;
            }
            $island->removeHelper($args[1]);
            if (($user2 = $this->um->getOnlineUser($args[1])) !== null) {
                $user2->removeIsland($islandName);
                $this->plugin->getChatHandler()->removePlayerFromChat($user2->getPlayer());
                if (strtolower($user2->getPlayer()->getPosition()->getWorld()->getDisplayName()) == $island->getId())
                    $this->plugin->teleportToSpawn($user2->getPlayer());
                $this->sendMessage($user2->getPlayer(), "§cYou have been removed from island {$islandName} §cby §a{$sender->getName()}!");
            }
            $this->sendMessage($sender, "§2{$args[1]} §awas removed from the island successfully!");
            if (strtolower($sender->getName()) != strtolower($island->getOwner())) {
                if (($owner = $this->um->getOnlineUser($island->getOwner())) !== null) {
                    $this->sendMessage($owner->getPlayer(), "§e{$args[1]} was removed from Helper on your Island.\n§6Removed by CoOwner - §a{$sender->getName()}");
                }
            }
        }
    }

}