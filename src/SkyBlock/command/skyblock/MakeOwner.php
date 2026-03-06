<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class MakeOwner extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'makeowner', "Transfer island ownership.", ['makeleader', 'newleader', 'newowner']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!$user->hasIsland()) {
            $this->sendMessage($sender, "§4[Error] §cYou must be an island owner to set a new leader to!");
        } else {
            if (!isset($args[1])) {
                $this->sendMessage($sender, "§cUsage: /is makeleader <player> or /is newleader <player>");
                return;
            }
            if (strtolower($sender->getName()) == strtolower($args[1])) {
                $this->sendMessage($sender, "§4[Error] §cYou are already the owner of this island!");
                return;
            }
            if (($user2 = $this->um->getOnlineUser($args[1])) === null) {
                $this->sendMessage($sender, "§4[Error] §c{$args[1]} is not online!");
                return;
            }
            if ($user2->isIslandSet()) {
                if ($user2->getIsland() !== $user->getIsland()) {
                    $this->sendMessage($sender, "§4[Error] §cThat player is already an Owner of an island!"); // different island
                } else {
                    $islandName = $user->getIsland();
                    if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                        $this->sendMessage($sender, "§4[Error]§c Island not online");
                        return;
                    }
                    if (!$island->isCoowner($args[1])) {
                        $this->sendMessage($sender, "§4[Error] §cThe player should be a CoOwner on your island! Use /is promote <player>");
                        return;
                    }
                    $this->pl->destroyAllPrivateChests($sender->getName());
                    $island->setOwner($args[1]);
                    $user2->setIsland($islandName);
                    $this->sendMessage($sender, "§eYou have set the ownership to §a{$user2->getName()} §esuccessfully! You are now CoOwner of §b$islandName §eisland! Private Chests unlinked!");
                    $this->sendMessage($user2->getPlayer(), "§eYou are now the Owner of §a{$islandName} §eisland!");
                }
            } else {
                $this->sendMessage($sender, "§4[Error] §cThe player should be a CoOwner on your island! Use /is promote <player>");
            }
        }
    }

}