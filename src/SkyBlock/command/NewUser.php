<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class NewUser extends BaseCommand {

    /**
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'newuser', 'Create New User', '[player]');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!Main::getInstance()->hasOp($sender)) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if ($sender instanceof Player && !Main::getInstance()->isTrusted($sender->getName())) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /newuser <username>");
            return;
        }
        $playerName = strtolower($args[0]);
        if (Main::getInstance()->getDb()->isPlayerRegistered($playerName)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is already registered!");
            return;
        }
        Main::getInstance()->getDb()->newUser($playerName, !Main::getInstance()->getDb()->hasPets($playerName));
        $this->sendMessage($sender, TextFormat::YELLOW . "New user " . $playerName . " has been registered!");
    }
}