<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\util\Values;

class Profile extends BaseCommand {
    /**
     * Profile constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'profile', 'Profile of a player', '', true, ['profiles']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (isset($args[0])) {
            $playerName = strtolower($args[0]);
            if (!Main::getInstance()->getDb()->isPlayerRegistered($playerName)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player has never connected!");
                return;
            }
            Main::getInstance()->getFormFunctions()->sendProfileView($sender, $playerName);
        } else {
            if ($sender->getPosition()->getWorld()->getDisplayName() === Values::PVP_WORLD) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot use that command here!");
                return;
            }
            Main::getInstance()->getFormFunctions()->sendPrefMenu($sender);
        }
    }
}