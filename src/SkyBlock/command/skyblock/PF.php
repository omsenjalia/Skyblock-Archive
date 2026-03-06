<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class PF extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'user', 'Check all islands of any player', ['player']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!isset($args[1])) {
            $this->sendMessage($sender, "§6Usage: /is user <player>");
            return;
        }
        $player = strtolower($args[1]);
        if (!$this->db->isPlayerRegistered($player)) {
            $this->sendMessage($sender, "§4[Error]§c That player never connected!");
            return;
        }
        if (($user2 = $this->um->getOnlineUser($player)) !== null) {
            if (!$user2->isIslandSet() and !$user2->hasIslands()) {
                $this->sendMessage($sender, "§4[Error]§c That player doesn't have an island!");
                return;
            }
            $island = $user2->getIsland();
            $islands = $user2->getIslandsString();
            if ($island !== "") {
                $rank = ($user2->hasIsland()) ? "Owner" : "CoOwner";
                $this->sendMessage($sender, "§a{$player}§e's island: §b$island §7| §eRank: §f$rank\n§eHelper on islands: §b$islands");
            } else    $this->sendMessage($sender, "§a{$player}§e's island: §b$island\n§eHelper on islands: §b$islands");
        } else {
            $island = $this->pl->getUserManager()->getPlayerIsland($player);
            $islands = $this->pl->getUserManager()->getPlayerIslands($player);
            $islandstr = implode(", ", $islands);
            if ($island === "" and empty($islands)) {
                $this->sendMessage($sender, "§4[Error]§c That player doesn't have an island!");
                return;
            }
            $this->sendMessage($sender, "§a{$player}§e's island: §b$island\n§eHelper on islands: §b$islandstr");
        }


    }

}