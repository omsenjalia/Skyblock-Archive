<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;
use SkyBlock\util\Util;

class TakeOver extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'takeover', 'Takeover the Island Owner position');
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (isset($args[1])) {
            $this->sendMessage($sender, "§6Usage: /is takeover");
            return;
        }
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be Island CoOwner to use that command.");
            return;
        }
        $islandName = $user->getIsland();
        if (($island = $this->im->getOnlineIsland($islandName)) === null) {
            $this->sendMessage($sender, "§4[Error]§c Island not online");
            return;
        }
        if (!$island->isCoowner($sender->getName())) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be Island CoOwner to use that command.");
            return;
        }
        $owner = $island->getOwner();
        $days = 10;
        $player = $this->pl->getServer()->getOfflinePlayer($owner);
        if ($player instanceof Player) {
            $this->sendMessage($sender, "§4[Error] §cOwner of the Island is online. Owner needs to be offline for more than $days days for a CoOwner to take over the island ownership!");
            return;
        }
        $last = (int) ($player->getLastPlayed() / 1000);
        $diff = strtotime("now") - $last;
        $offset = $days * 24 * 60 * 60; // 10 days
        if ($diff < $offset) {
            $this->sendMessage($sender, "§4[Error] §cOwner needs to be offline for more than $days days for a CoOwner to take over the island ownership! §aOwner `{$owner}`'s Last seen - §6" . Util::getTimePlayed($diff));
        } else {
            $this->pl->destroyAllPrivateChests($owner);
            $island->setOwner($sender->getName());
            $user->setIsland($islandName);
            $this->sendMessage($sender, "§eYou have successfully taken over Island §a{$island->getName()}! §6You are now the §bOwner §6of §b{$island->getName()} §6island! §cPrivate Chests were unlinked!");
        }
    }

}