<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\perms\Permission;
use SkyBlock\user\User;

class Freeze extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'freeze', 'Freeze/Unfreeze island members', ['unfreeze']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (isset($args[1])) {
            $this->sendMessage($sender, "§6Usage: /is freeze");
            return;
        }
        if (!$user->hasIsland()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be the Island Owner to use this command!");
            return;
        }
        $islandName = $user->getIsland();
        if (($island = $this->im->getOnlineIsland($islandName)) === null) {
            $this->sendMessage($sender, "§4[Error] §cIsland not online!");
            return;
        }
        if ($island->getFreeze()) {
            $set = false;
            $memstr = "§eIsland Owner §a{$sender->getName()} §ehas unfrozen the Island!";
            $ownstr = "§eIsland unfroze successfully!";
            $nofreezestr = "§eIsland Owner §a{$sender->getName()} §ehas unfrozen the Island!";
        } else {
            $set = true;
            $memstr = "§cIsland Owner §a{$sender->getName()} §chas frozen the Island!\n§6You can't move, break, or place blocks on this island until it's unfrozen. You are allowed to leave the Island!";
            $ownstr = "§eIsland froze successfully! Helpers aren't allowed to move, place or break blocks until Island is unfrozen!";
            $nofreezestr = "§cIsland Owner §a{$sender->getName()} §chas frozen the Island!\n§6You're in the no freeze list of the Island, so you dont get affected!";
        }
        $island->setFreeze($set);
        foreach ($island->getHelpers() as $helper) {
            $player = $this->pl->getServer()->getPlayerByPrefix($helper);
            if ($player instanceof Player && $player->getPosition()->getWorld()->getDisplayName() == $island->getId()) {
                if (!$island->hasPerm($player->getName(), Permission::FREEZE)) {
                    $player->setNoClientPredictions($set);
                    if ($set) $this->pl->removePlayerPet($player);
                    $this->sendMessage($player, $memstr);
                } else {
                    $player->setNoClientPredictions(false);
                    $this->sendMessage($player, $nofreezestr);
                }
            }
        }
        foreach ($island->getRoleHelpers() as $helper) {
            $player = $this->pl->getServer()->getPlayerByPrefix($helper);
            if ($player instanceof Player && $player->getPosition()->getWorld()->getDisplayName() == $island->getId()) {
                $player->setNoClientPredictions($set);
                if ($set) $this->pl->removePlayerPet($player);
                $this->sendMessage($player, $memstr);
            }
        }
        $this->sendMessage($sender, $ownstr);
    }

}