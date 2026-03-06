<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\util\Values;

class Servers extends BaseCommand {
    /**
     * Servers constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'servers', 'Servers Menu', '', true, []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (in_array($sender->getPosition()->getWorld()->getDisplayName(), Values::PVP_WORLDS, true)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot use that command here!");
            return;
        }
        Main::getInstance()->getFormFunctions()->sendServersMenu($sender);
    }
}
