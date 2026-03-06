<?php


namespace SkyBlock\command\skyblock\ban;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\perms\Permission;
use SkyBlock\user\User;

class Ban extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'ban', 'Ban a player from your island');
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou must be an Island owner/coowner to ban a player!");
        } else {
            if (!isset($args[1])) {
                $this->sendMessage($sender, "§6Usage: /is ban <player>");
                return;
            }
            $playerName = strtolower($args[1]);
            if (!$this->db->isPlayerRegistered($playerName)) {
                $this->sendMessage($sender, "§4[Error]§c That player never connected!");
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
            if ($island->isMember($playerName)) {
                $this->sendMessage($sender, "§4[Error]§c Cannot ban an island member, demote or remove them from Island by /is demote or /is remove!");
                return;
            }
            if ($island->hasARole($playerName)) {
                $this->sendMessage($sender, "§cPlayer has a role on your island! §6Use /is fire to remove role first.");
                return;
            }
            if ($island->getBanCount() >= 500) {
                $this->sendMessage($sender, "§4[Error]§c Exceeds Ban limit! Use /is unban to free some space");
                return;
            }
            if ($this->pl->staffapi->isSoftStaff($playerName)) {
                $this->sendMessage($sender, "§4[Error]§c Cannot ban staff!");
                return;
            }
            if ($island->isBanned($playerName)) {
                $this->sendMessage($sender, "§4[Error]§c That player is already banned!");
                return;
            }
            $island->addBan($playerName);
            if (($player = $this->pl->getServer()->getPlayerExact($playerName)) instanceof Player) {
                if ($player->getPosition()->getWorld()->getDisplayName() === $island->getId()) $this->plugin->teleportToSpawn($player);
                $this->sendMessage($player, "§cYou were banned from Island §a{$island->getName()} §cby the Island Owner!");
                $this->pl->getChatHandler()->removePlayerFromChat($player);
            }
            $this->sendMessage($sender, "§eYou have successfully banned §a{$playerName} §efrom your Island. They cannot be invited anymore nor teleport to your island!");
        }
    }

}