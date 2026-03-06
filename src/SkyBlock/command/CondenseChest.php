<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class CondenseChest extends BaseCommand {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'condensechest', 'Condense Chests Content', '', true, ['cc']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!$sender->hasPermission("core.condensechest") || Main::getInstance()->isTrusted($sender->getName())) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy /condensechest on " . TextFormat::AQUA . "shop.fallentech.io");
            return;
        }
        if (isset(Main::getInstance()->condensechest[strtolower($sender->getName())])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Condense chest mode has been turned off!");
            unset(Main::getInstance()->condensechest[strtolower($sender->getName())]);
        } else {
            Main::getInstance()->condensechest[strtolower($sender->getName())] = true;
            $this->sendMessage($sender, TextFormat::YELLOW . "Condense chest mode has been turned on. Hit chests on your islands to condense contents!");
        }
    }
}