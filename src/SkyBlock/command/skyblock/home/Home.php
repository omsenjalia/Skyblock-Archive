<?php


namespace SkyBlock\command\skyblock\home;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\perms\Permission;
use SkyBlock\user\User;

class Home extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'home', 'Teleport to an island home');
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!isset($args[1])) {
            $this->sendMessage($sender, "§6Usage: /is home <home name> <island name>");
            return;
        }
        if (!isset($args[2])) {
            if (!$user->isIslandSet()) {
                $this->sendMessage($sender, "§4[Error]§e You do not own any island to go home to, §cuse /is home <home name> <island name>");
                return;
            }
            $islandName = $user->getIsland();
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $this->sendMessage($sender, "§4[Error]§c Island not online");
                return;
            }
            if (!$island->hasPerm($user->getName(), Permission::HOME)) {
                $this->sendMessage($user->getPlayer(), TextFormat::RED . "You dont have home perms on this island");
                return;
            }
            if (!$island->hasHome($args[1])) {
                $this->sendMessage($sender, "§4[Error]§c Your Island haven't set that home! Use /is sethome to set homes!");
                return;
            }
            $home = $island->getHomePosition($args[1]);
            $home->getWorld()->loadChunk($home->getFloorX() >> 4, $home->getFloorZ() >> 4);
            $sender->teleport($home, 0.0, 0.0);
            $this->sendMessage($sender, "§aYou have been teleported to your Island home §e{$args[1]} §asuccessfully");
        } else {
            if (isset($args[3])) {
                $this->sendMessage($sender, "§6Usage: /is home <home name> <island name>!");
                return;
            }
            if (!$this->db->isNameUsed($args[2])) {
                $this->sendMessage($sender, "§4[Error] §cIsland not found!");
                return;
            }
            if (($island = $this->im->getOnlineIsland($args[2])) === null) {
                $this->sendMessage($sender, "§4[Error] §cIsland not online! Owners of that island {$args[2]} are not online!");
                return;
            }
            if (!$island->isCoowner($sender->getName()) and !$island->isAdmin($sender->getName())) {
                $this->sendMessage($sender, "You must be an Admin or CoOwner of that Island to teleport to home!");
            } else {
                if (!$island->hasPerm($user->getName(), Permission::HOME)) {
                    $this->sendMessage($user->getPlayer(), TextFormat::RED . "You dont have home perms on this island");
                    return;
                }
                if (!$island->hasHome($args[1])) {
                    $this->sendMessage($sender, "§4[Error] §cIsland §e{$args[2]} §cdoesn't have home named {$args[1]}! Use /is sethome");
                    return;
                }
                $home = $island->getHomePosition($args[1]);
                $home->getWorld()->loadChunk($home->getFloorX() >> 4, $home->getFloorZ() >> 4);
                $sender->teleport($home, 0.0, 0.0);
                $this->sendMessage($sender, "§aYou have been teleported to Island §e{$args[2]}'s §ahome §6{$args[1]} §asuccessfully");
            }
        }
    }

}