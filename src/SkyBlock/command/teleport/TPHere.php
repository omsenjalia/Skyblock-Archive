<?php


namespace SkyBlock\command\teleport;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class TPHere extends BaseCommand {

    /**
     * TPHere constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'tphere', 'Force teleport a player to you', '[player]', true, [], 'core.tp.here');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!Main::getInstance()->hasOp($sender)) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /tphere <player>");
            return;
        }
        $playerName = strtolower($args[0]);
        $player = Server::getInstance()->getPlayerByPrefix($playerName);
        if (!$player instanceof Player) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
            return;
        }
        if (strtolower($sender->getName()) === strtolower($player->getName())) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot teleport yourself to yourself!");
            return;
        }
        $player->teleport($sender->getLocation());
        $this->sendMessage($sender, TextFormat::YELLOW . $player->getName() . " is now being teleported to you!");
    }
}