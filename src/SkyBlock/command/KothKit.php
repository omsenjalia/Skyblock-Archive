<?php

namespace SkyBlock\command;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\Main;

class KothKit extends BaseCommand {

    /**
     * KOTHKit constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'kothkit', '', '', true, ['kk']); // suspicious name
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
        Main::getInstance()->giveRewards($sender);
        $this->sendMessage($sender, "You have received a KOTH kit!");
    }
}