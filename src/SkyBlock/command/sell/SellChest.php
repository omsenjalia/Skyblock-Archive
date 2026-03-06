<?php


namespace SkyBlock\command\sell;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class SellChest extends BaseCommand {
    /**
     * SellChest constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'sc', 'Sell chest mode', '', true, ['sellchest']);
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!$sender->hasPermission("core.sellchest")) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy /sellchest on " . TextFormat::AQUA . "shop.fallentech.io");
            return;
        }
        if (isset(Main::getInstance()->sellchest[strtolower($sender->getName())])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Sell chest XP mode has been disabled!");
            unset(Main::getInstance()->sellchest[strtolower($sender->getName())]);
        } else {
            Main::getInstance()->sellchest[strtolower($sender->getName())] = "Money";
            $this->sendMessage($sender, TextFormat::YELLOW . "Sell chest XP mode has been enabled!");
        }
    }
}