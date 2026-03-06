<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\perms\Permission;
use SkyBlock\user\User;

class Promote extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'promote', 'Promote a Helper to Admin and Coowner of island', ['addadmin', 'makeadmin', 'addcoowner', 'makecoowner', 'promo']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be Island Owner/CoOwner to use that command!");
            return;
        }
        $islandName = $user->getIsland();
        if (($island = $this->im->getOnlineIsland($islandName)) === null) {
            $this->sendMessage($sender, "§4[Error]§c Island not online");
            return;
        }
        if (!$island->hasPerm($sender->getName(), Permission::MANAGER)) {
            $this->sendMessage($sender, TextFormat::RED . "You dont have managing perms on this island!");
            return;
        }
        if (!isset($args[1])) {
            $this->sendMessage($sender, "§cUsage: /is promote <player>");
            return;
        }
        $playerName = strtolower($args[1]);
        $player = $this->pl->getServer()->getPlayerByPrefix($playerName);
        if (!$player instanceof Player) {
            $this->sendMessage($sender, "§4[Error] §c{$args[1]} is not online!");
            return;
        }
        if (($user2 = $this->um->getOnlineUser($player->getName())) === null) {
            $this->sendMessage($sender, "§4[Error] §c{$player->getName()} is not online!");
            return;
        }
        if (strtolower($player->getName()) === strtolower($sender->getName())) {
            $this->sendMessage($sender, "§cYou cannot promote yourself!");
            return;
        }
        if (!$island->isHelper(strtolower($player->getName()))) {
            $this->sendMessage($sender, "§4[Error] §cThat player is not a helper on your island to promote!");
            return;
        }
        if ($island->isCoowner(strtolower($player->getName()))) {
            $this->sendMessage($sender, "§4[Error] §cThat player is already Coowner on your island! To give ownership use /is makeowner <player>");
            return;
        }
        if ($island->isAdmin(strtolower($player->getName()))) {
            if ($island->getCoOwnerCount() >= $island->getCoownerLimit()) {
                $this->sendMessage($sender, "§4[Error] §cYou can only have §f{$island->getCoownerLimit()} §cCoOwners at your Island Level. Increase your Island Level to increase CoOwner limit. Use /is perks to see perks you unlock at Island levels!");
                return;
            }
            if ($user2->isIslandSet()) {
                $this->sendMessage($sender, "§cThat player is already an Owner of an island!, §6ask them to /is delete.");
                return;
            }
            $island->removeAdmin($player->getName());
            $island->addCoowner($player->getName());
            $user2->setIsland($islandName);
            $this->sendMessage($sender, "§aYou promoted §2{$player->getName()} §ato Island CoOwner!\n§ePerks - §6Can tp to island even when you are offline, can use /upgrade, can get manager perms from /is perms!");
            $this->sendMessage($player, "§aYou are now an §bCoOwner §aon §6{$islandName} §aisland.\n§ePerks - §6Can tp to island even when you are offline, can use /upgrade, can get manager perms from /is perms!");
        } else {
            $island->addAdmin($player->getName());
            $this->sendMessage($sender, "§aYou promoted §2{$player->getName()} §ato Island Admin!");
            $this->sendMessage($player, "§aYou are now an §9Admin §aon §6{$islandName} §aIsland!");
        }
    }

}