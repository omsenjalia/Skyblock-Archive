<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class DelFolder extends BaseCommand {
    /**
     * DelFolder constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'delfolder', 'Delete a folder from server', '<folder | players | worlds>', true, ['del'], "core.delfolder");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof ConsoleCommandSender) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /delfolder <folder | players | worlds>");
            return;
        }
        Main::getInstance()->getIslandManager()->deleteAllLevels(strtolower($args[0]) . "/");
    }
}