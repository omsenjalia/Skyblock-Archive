<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class TimePlayed extends BaseCommand {
    /**
     * TimePlayed constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'timeplayed', 'Check your time played', '[player]', true, ['hours']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!isset($args[0])) {
            if (!$sender instanceof Player) {
                $this->sendMessage($sender, self::NO_CONSOLE);
                return;
            }
            $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
            $this->sendMessage($sender, TextFormat::YELLOW . "Your playtime: " . $user->getTimePlayed());
        } else {
            $player = strtolower($args[0]);
            if (!Main::getInstance()->getDb()->isPlayerRegistered($player)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player has never connected!");
                return;
            }
            $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
            if ($user !== null) {
                $this->sendMessage($sender, TextFormat::YELLOW . $user->getName() . "'s playtime: " . $user->getTimePlayed());
            } else {
                $this->sendMessage($sender, TextFormat::YELLOW . $player . "'s playtime: " . Main::getInstance()->getDb()->getTimePlayed($player));
            }
        }
    }
}