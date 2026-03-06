<?php

namespace SkyBlock\command;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class AutoSprint extends BaseCommand {

    /**
     * AutoSprint constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, "autosprint", "Toggle auto sprint", "", true, ['as', 'ts', 'togglesprint']);
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        $name = $sender->getName();
        if (isset(Main::getInstance()->autosprint[$name])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Disabling auto-sprint!");
            $sender->setSprinting(false);
            unset(Main::getInstance()->autosprint[$name]);
        } else {
            $this->sendMessage($sender, TextFormat::YELLOW . "Enabling auto-sprint!");
            $sender->setSprinting();
            Main::getInstance()->autosprint[$name] = true;
        }
    }
}
