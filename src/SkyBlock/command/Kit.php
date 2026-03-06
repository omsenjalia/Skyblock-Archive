<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\util\Values;

class Kit extends BaseCommand {
    /**
     * Kit constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'kit', 'Kit Menu', '', true, ['gkit', 'kits', 'gkits']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if ($sender->getPosition()->getWorld()->getDisplayName() === Values::PVP_WORLD) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot use that command here!");
            return;
        }
        if (!isset($args[0])) {
            Main::getInstance()->getFormFunctions()->sendKitTypeMenu($sender);
        } else {
            $kitName = strtolower($args[0]);
            $kit = Main::getInstance()->getKit($kitName);
            if ($kit === null) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That kit is not found. Use /kit to check available kits!");
                return;
            }
            Main::getInstance()->getFormFunctions()->sendKitInfo($sender, $kitName);
        }
    }
}