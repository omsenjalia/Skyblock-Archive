<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class Teleport extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'tpto', "Teleport to an Island by name.", ['teleport', 'tp']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!isset($args[1])) {
            $this->sendMessage($sender, "§cUsage: /is tp <island name>");
            return;
        }
        if (!ctype_alnum($args[1])) {
            $this->sendMessage($sender, "§4[Error] §cOnly numbers and letters allowed.");
            return;
        }
        $islandName = $args[1];
        if (($island = $this->im->getOnlineIsland($islandName)) === null) {
            $this->sendMessage($sender, "§4[Error] §cIsland owner must be online if you want to teleport to the island!");
            return;
        }
        if (($island->isLocked()) and (!$island->isMember($sender->getName())) and (!$island->hasARole($sender->getName()))) {
            $this->sendMessage($sender, "§4[Error] §cThat island is locked, you cannot join it!");
            return;
        }
        if ($island->isBanned(strtolower($sender->getName()))) {
            $this->sendMessage($sender, "§4[Error] §cYou were banned from that island, you cannot join it!");
            return;
        }
        if (is_null($island->getWorldLevel())) {
            $this->sendMessage($sender, "§4[Error] §cWorld not loaded yet!");
            return;
        }
        if ($island->getVLimit() > 0 and !$island->isMember($sender->getName()) and $island->isIslandFullForVisitors()) {
            $this->sendMessage($sender, "§4[Error] §cThat Island is at maximum capacity of visitors, Island Visitor limit - §f{$island->getVLimit()}");
            return;
        }
        $player = $island->getOwner();
        $island->teleport($sender);
        if (($user2 = $this->um->getOnlineUser($player)) !== null) {
            $this->sendMessage($user2->getPlayer(), "§a{$sender->getName()} §ejust teleported to your island by /is tp! Lock your island by /is lock");
        }
        $this->sendMessage($sender, "§eYou teleported to island §a{$args[1]}§e's spawn successfully");
    }

}