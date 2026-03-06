<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class Delete extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'delete', "Delete your island.", ['disband']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!$user->hasIsland()) {
            $this->sendMessage($sender, "§4[Error] §cYou must be an Island Owner to delete your island!");
            return;
        }
        if (!isset($this->pl->delete[strtolower($sender->getName())])) {
            $this->pl->delete[strtolower($sender->getName())] = true;
            $this->sendMessage($sender, "§eThis command will delete your island and all your progress, to confirm it, do §a/is delete confirm");
        }
        if (!isset($args[1])) {
            $this->sendMessage($sender, "§ePlease enter confirm at the end to delete the island, §a/is delete confirm");
            return;
        }
        if (strtolower($args[1]) === "confirm") {
            $islandName = $user->getIsland();
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $this->sendMessage($sender, "§4[Error]§c Island not online");
                return;
            }
            $helpers = $island->getHelpers();
            if (!empty($helpers)) {
                foreach ($helpers as $h) {
                    if (($user2 = $this->um->getOnlineUser($h)) !== null) {
                        $user2->removeIsland($islandName);
                    }
                }
            }
            $coowners = $island->getCoowners();
            if (!empty($coowners)) {
                foreach ($coowners as $coowner) {
                    if (($user2 = $this->um->getOnlineUser($coowner)) !== null) {
                        $user2->setIsland();
                    }
                }
            }
            $this->pl->destroyAllPrivateChests($sender->getName());
            $this->db->delIsland($islandName);
            $user->setIsland();
            $this->pl->getChatHandler()->setChatOffline($island->getId());
            $this->im->removeIsland($island->getId());
            $this->im->setIslandOffline($islandName);
            $this->pl->resettime[strtolower($sender->getName())] = time();
            unset($this->pl->delete[strtolower($sender->getName())]);
            $this->sendMessage($sender, "§aYou successfully deleted the island!");
        } else {
            $this->sendMessage($sender, "§ePlease enter confirm at the end to delete the island, §a/is delete confirm");
        }
    }

}