<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\util\Values;

class GetPos extends BaseCommand {

    /**
     * GetPos constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'getpos', "Display your Coords", "", true, ['pos', 'coords']);
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if ($sender->getPosition()->getWorld()->getDisplayName() === Values::PVP_WORLD) {
            if (!isset(Main::getInstance()->gandalf->edit[$sender->getName()]) && !Main::getInstance()->hasOp($sender)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use that command here!");
                return;
            }
        }
        if (isset($args[0])) {
            if (!Main::getInstance()->isTrusted($sender->getName())) {
                $this->sendMessage($sender, self::NO_PERMISSION);
                return;
            }
            $playerName = $args[0];
            $player = Server::getInstance()->getPlayerByPrefix($playerName);
            if ($player === null) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
                return;
            };
        } else {
            $player = $sender;
        }
        $this->sendMessage($sender, TextFormat::GREEN . "World: " . TextFormat::AQUA .
                                  $player->getPosition()->getWorld()->getDisplayName() . "\n" . TextFormat::GREEN .
                                  "Coordinates: " . TextFormat::YELLOW . "X: " . TextFormat::AQUA . $player->getPosition()->getFloorX() .
                                  TextFormat::GREEN . ", " . TextFormat::YELLOW . "Y: " . TextFormat::AQUA .
                                  $player->getPosition()->getFloorY() . TextFormat::GREEN . ", " . TextFormat::YELLOW . "Z: " .
                                  TextFormat::AQUA . $player->getPosition()->getFloorZ()
        ); // todo check if this is fine
    }
}