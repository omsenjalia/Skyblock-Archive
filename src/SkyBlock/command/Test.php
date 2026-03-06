<?php

namespace SkyBlock\command;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\Main;

class Test extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 't', 'Set time of a world', 'set <day | night> | /time stop');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            return;
        }
        if (!Main::getInstance()->isTrusted($sender->getName())) {
            return;
        }
        if (isset($args[0])) {
            $sender->setMovementSpeed((float) $args[0]);
        } else {
            $sender->sendMessage($sender->getMovementSpeed());
        }
    }
}