<?php


namespace SkyBlock\command\skyblock\helper;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\perms\Permission;
use SkyBlock\user\User;

class Invite extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'invite', "Invite a player to be member of your island.", ['ask', 'addhelper', 'inv']);
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be Island Owner/Coowner to use that command!");
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
        if (!isset($args[1])) {
            $this->sendMessage($sender, "§cUsage: /is invite <player> or /is ask <player>");
            return;
        }
        $playerName = strtolower($args[1]);
        $player = $this->pl->getServer()->getPlayerByPrefix($playerName);
        if (!$player instanceof Player) {
            $this->sendMessage($sender, "§4[Error] §c{$args[1]} is not online!");
            return;
        }
        if (strtolower($player->getName()) === strtolower($sender->getName())) {
            $this->sendMessage($sender, "§cYou cannot invite yourself!");
            return;
        }
        if ($island->isBanned($player->getName())) {
            $this->sendMessage($sender, "§cThat player was banned from this island by Owner!");
            return;
        }
        if ($island->hasARole($player->getName())) {
            $this->sendMessage($sender, "§cPlayer has a role on your island! §6Use /is fire to remove role first before inviting.");
            return;
        }
        $user2 = $this->um->getOnlineUser(strtolower($player->getName()));
        if ($user2 == null) {
            $this->sendMessage($sender, "§4[Error] §c{$args[1]} is not online!");
            return;
        }
        $limit = $island->getHelperLimit();
        if ($island->getHelperCount() >= $limit) {
            $this->sendMessage($sender, "§4[Error] §cYou can only have {$limit} helpers on your island, increase your island level by mining or building to get more helpers!");
            return;
        }
        if (strtolower($islandName) == strtolower($user2->getIsland())) {
            $this->sendMessage($sender, "§4[Error] §cThat player is already a member of the island!");
            return;
        }
        if ($island->isHelper(strtolower($player->getName()))) {
            $this->sendMessage($sender, "§4[Error] §cThat player is already a helper on your island!");
            return;
        }
        if ($user2->getIslandsCount() >= $this->func->getUserHelperLimit($player)) {
            $this->sendMessage($sender, "§4[Error] §cThat player is already helper on max number of islands they can be!");
            return;
        }
        if (isset($this->pl->invitations[strtolower($sender->getName())][strtolower($player->getName())])) {
            $time = $this->pl->invitations[strtolower($sender->getName())][strtolower($player->getName())]["time"];
            $now = time();
            if (($now - $time) <= 60) {
                $this->sendMessage($sender, "§4[Error] §cYou've already sent an island invite request to that player! Wait till it gets timed out or till they respond!");
                return;
            } else unset($this->pl->invitations[strtolower($sender->getName())][strtolower($player->getName())]);
        }
        $this->pl->invitations[strtolower($sender->getName())][strtolower($player->getName())]["time"] = time();
        $this->pl->invitations[strtolower($sender->getName())][strtolower($player->getName())]["island"] = $islandName;
        $this->sendMessage($sender, "§aYou sent an invitation to §2{$player->getName()} §asuccessfully!");
        $this->sendMessage($player, "{$sender->getName()} §ainvited you to their island §e$islandName §a! §2Do /is accept {$sender->getName()} §ato accept their invite, or §2/is deny {$sender->getName()} §ato deny their request.");
    }

}