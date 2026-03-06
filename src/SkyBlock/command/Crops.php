<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Crops extends BaseCommand {
    /**
     * Crops constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'crops');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        $this->sendMessage($sender, TextFormat::YELLOW . "Unlock different crops by increasing your island level!");
        foreach (Main::getInstance()->crops as $data) {
            $this->sendMessage($sender, TextFormat::YELLOW . $data["name"] . TextFormat::WHITE . " => " . TextFormat::YELLOW . "Island Level " . $data["level"]);
        }
    }
}