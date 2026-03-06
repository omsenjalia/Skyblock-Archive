<?php


namespace SkyBlock\command\skyblock\admin;


use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

class FDel extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'fdel');
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!$this->plugin->hasOp($sender)) {
            $this->sendMessage($sender, "§4[Error] §cNo permission!");
            return;
        }
        if (!isset($args[2])) {
            $this->sendMessage($sender, "§cUsage: /is fdel <island> <reason>");
            return;
        }
        $islandName = strtolower($args[1]);
        if (!$this->db->isNameUsed($islandName)) {
            $this->sendMessage($sender, "The requested Island does not exist");
            return;
        }
        array_shift($args);
        array_shift($args);
        $reason = implode(" ", $args);
        $reason = trim($reason, "'");
        if (strlen($reason) < 5) {
            $this->sendMessage($sender, "§4[Error]§c> Please write a reason more than 5 letters!");
            return;
        }
        if (($island = $this->im->getOnlineIsland($islandName)) !== null) {
            $helpers = $island->getHelpers();
            if (!empty($helpers)) {
                foreach ($helpers as $h) {
                    if (($user2 = $this->um->getOnlineUser($h)) !== null) {
                        $this->sendMessage($user2->getPlayer(), "> §cStaff deleted §a$islandName §cIsland for reason - §a`$reason`!");
                        $user2->removeIsland($islandName);
                    }
                }
            }
            $coowners = $island->getCoowners();
            if (!empty($coowners)) {
                foreach ($coowners as $coowner) {
                    if (($user2 = $this->um->getOnlineUser($coowner)) !== null) {
                        $this->sendMessage($user2->getPlayer(), "> §cStaff deleted your §a$islandName §cIsland for reason - §a`$reason`!");
                        $user2->setIsland();
                    }
                }
            }
            $owner = $island->getOwner();
            if (($user2 = $this->um->getOnlineUser($owner)) !== null) {
                $this->sendMessage($user2->getPlayer(), "> §cStaff deleted your §a$islandName §cIsland for reason - §a`$reason`!");
                $user2->setIsland();
            }
            $this->pl->getChatHandler()->setChatOffline($island->getId());
            $this->im->removeIsland($island->getId());
        }
        $this->pl->destroyAllPrivateChests($sender->getName());
        $this->db->delIsland($islandName);
        $this->im->setIslandOffline($islandName);
        $this->pl->resettime[strtolower($sender->getName())] = time();
        $this->sendMessage($sender, "§eIsland §a$islandName §ewas successfully deleted.");
        $this->pl->sendDiscordMessage("Island Deletion!", "Island `$islandName` was deleted by {$sender->getName()} for Reason - **$reason**!\n", 5);

    }

}