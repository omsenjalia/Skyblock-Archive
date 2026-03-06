<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class TeamChat extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'teamchat', "Enable/Disable Team/Island chat.", ['tc', 'tchat', 'chat']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if ($this->plugin->getGangChatHandler()->isInChat($sender)) {
            $this->plugin->getGangChatHandler()->removePlayerFromChat($sender);
            $this->sendMessage($sender, "§eYou successfully left the gang chat!");
        }
        if ($this->plugin->getChatHandler()->isInChat($sender)) {
            $this->plugin->getChatHandler()->removePlayerFromChat($sender);
            $this->sendMessage($sender, "You successfully left the island chat!");
        } else {
            if (!isset($args[1])) {
                if (!$user->isIslandSet()) {
                    $this->sendMessage($sender, "§4[Error]§e You do not own any island to teamchat with, §cuse /is tc <island name>");
                    return;
                }
                $islandName = $user->getIsland();
                if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                    $this->sendMessage($sender, "§4[Error]§c Island not online");
                    return;
                }
                $level = $island->getId();
                $this->plugin->getChatHandler()->addPlayerToChat($sender, $level);
                $this->sendMessage($sender, "You joined {$islandName}'s team chat room! Now only your island members will see your messages!");
            } else {
                if (!ctype_alnum($args[1])) {
                    $this->sendMessage($sender, "§4[Error] §cOnly letters and numbers allowed!");
                    return;
                }
                if (!$this->db->isNameUsed($args[1])) {
                    $this->sendMessage($sender, "§4[Error] §cIsland not found!");
                    return;
                }
                if (($island = $this->im->getOnlineIsland($args[1])) === null) {
                    $this->sendMessage($sender, "§4[Error] §cIsland not online! Owner of the island {$args[1]} is not online!");
                    return;
                }
                if (!$island->isMember($sender->getName())) {
                    $this->sendMessage($sender, "You must be a member of that island!");
                } else {
                    $level = $island->getId();
                    $this->plugin->getChatHandler()->addPlayerToChat($sender, $level);
                    $this->sendMessage($sender, "You joined {$args[1]}'s team chat room! Now only those island members will see your messages!");
                }
            }
        }
    }

}