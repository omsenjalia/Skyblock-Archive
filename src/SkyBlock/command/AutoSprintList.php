<?php

namespace SkyBlock\command;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class AutoSprintList extends BaseCommand {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, "aslist", "List of auto sprinters");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!Main::getInstance()->staffapi->isSoftStaff($sender->getName())) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        $this->sendMessage($sender, "Auto sprint list:" . "\n" . TextFormat::WHITE . implode(", ", array_keys(Main::getInstance()->autosprint)));
    }

}