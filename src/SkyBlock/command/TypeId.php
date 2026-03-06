<?php

namespace SkyBlock\command;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\Main;

class TypeId extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'typeid', 'Check typeid of item', "", true, [], "core.typeid");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset(Main::getInstance()->gandalf->edit[$sender->getName()]) && !Main::getInstance()->hasOp($sender)) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        $sender->sendMessage($sender->getInventory()->getItemInHand()->getTypeId());
    }
}