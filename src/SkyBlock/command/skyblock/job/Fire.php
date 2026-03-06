<?php


namespace SkyBlock\command\skyblock\job;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\perms\Permission;
use SkyBlock\user\User;

class Fire extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'fire', "Fire a worker from your island");
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou must be an island owner/coowner to remove a user!");
        } else {
            if (!isset($args[1])) {
                $this->sendMessage($sender, "§cUsage: /is fire <player>");
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
            if (($job = $island->getRole($args[1])) == null) {
                $this->sendMessage($sender, "§4[Error] §c{$args[1]} isn't a worker on your island!");
                return;
            }
            $island->removeRole($args[1], $job);
            $jobname = ucfirst($job);
            if (($user2 = $this->um->getOnlineUser($args[1])) !== null) {
                if (strtolower($user2->getPlayer()->getPosition()->getWorld()->getDisplayName()) == $island->getId())
                    $this->plugin->teleportToSpawn($user2->getPlayer());
                $this->sendMessage($user2->getPlayer(), "§cYou have been fired from your §6{$jobname} §cjob at island {$islandName} §cby §a{$sender->getName()}!");
            }
            $this->sendMessage($sender, "§2{$args[1]} §awas fired from the island successfully!");
            if (strtolower($sender->getName()) != strtolower($island->getOwner())) {
                if (($owner = $this->um->getOnlineUser($island->getOwner())) !== null) {
                    $this->sendMessage($owner->getPlayer(), "§e{$args[1]} was fired from their §6{$jobname} §ejob on your Island.\n§6Fired by CoOwner - §a{$sender->getName()}");
                }
            }
        }
    }

}