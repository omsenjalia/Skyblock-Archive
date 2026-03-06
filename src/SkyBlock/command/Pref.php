<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\Main;

class Pref extends BaseCommand {

    /**
     * Pref constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, "pref", "Server preferences", "", true, ['hud']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        Main::getInstance()->getFormFunctions()->sendPrefMenu($sender);
    }
}