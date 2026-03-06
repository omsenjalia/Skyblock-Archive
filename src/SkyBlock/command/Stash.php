<?php

namespace SkyBlock\command;

use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;
use SkyBlock\util\Values;

/**
 * @deprecated
 * */
class Stash extends BaseCommand {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, "stash", "Collect your stashed items", "", true, ["pickupstash", "pis"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if ($sender->getPosition()->getWorld()->getDisplayName() === Values::PVP_WORLD) {
            $this->sendMessage($sender, "§4[Error] §cYou cannot claim your Stash here!");
            return;
        }
        if (Main::getInstance()->isInCombat($sender)) {
            $this->sendMessage($sender, "§4[Error] §cYou cannot claim your Stash while in combat!");
            return;
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
        if ($user === null) {
            return;
        }
        $stash = $user->getStash();
        $stashCount = count($stash);
        if ($stashCount < 1) {
            $this->sendMessage($sender, "You have no items left to pickup from your stash!");
            return;
        }
        $added = 0;
        foreach ($stash as $id => $itemJson) {
            $item = User::jsonDeserializeItem($itemJson);
            if ($sender->getInventory()->canAddItem($item)) {
                $sender->getInventory()->addItem($item);
                $added++;
                $user->removeStash($id);
            }
        }
        $user->update();
        $this->sendMessage($sender, "Added $added items from your stash into your inventory!");
        $left = $stashCount - $added;
        if ($left > 0) {
            $this->sendMessage($sender, "§7You still have $left items left to pickup from your stash!");
        }

    }

}