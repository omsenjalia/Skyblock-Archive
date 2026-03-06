<?php

namespace SkyBlock\command;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Dupe extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'dupe', "", "", true, [], "core.dupe");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!Main::getInstance()->isTrusted($sender->getName())) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        $item = $sender->getInventory()->getItemInHand();
        $sender->getInventory()->addItem($item);
        $this->sendMessage($sender, TextFormat::YELLOW . "Successfully duplicated your held item!");
    }
}