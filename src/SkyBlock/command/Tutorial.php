<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\Main;

class Tutorial extends BaseCommand {

    /**
     * Tutorial constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, "tutorial", "Tutorial cmd", "", true, ['info', 'wiki']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        Main::getInstance()->getFormFunctions()->dataToMenu($sender, "Tutorial", "Tutorial", Main::getInstance()->tutorial, Main::getInstance()->tutorial);
    }
}