<?php


namespace SkyBlock\command\warps;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class IsWorld extends BaseCommand {
    /**
     * World constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, "isworld", "Teleport to a skyblock island by name", "<is name>", true, [], "core.isworld");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!Main::getInstance()->hasOp($sender) && !isset(Main::getInstance()->gandalf->edit[$sender->getName()])) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /isworld <name>");
            return;
        }
        if (!Main::getInstance()->getDb()->isNameUsed($args[0])) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That island does not exist!");
            return;
        }
        $world = Main::getInstance()->getDb()->getWorldName($args[0]);
        if (Server::getInstance()->getWorldManager()->isWorldGenerated($world)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That island does not exist!");
            return;
        } else if (!Server::getInstance()->getWorldManager()->isWorldLoaded($world)) {
            $this->sendMessage($sender, TextFormat::YELLOW . "$args[0] island is being loaded!");
            if (!Server::getInstance()->getWorldManager()->loadWorld($world, true)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " The island could not be loaded!");
                return;
            }
        }
        $sender->teleport(Server::getInstance()->getWorldManager()->getWorldByName($world)->getSpawnLocation(), 0.0, 0.0);
        $this->sendMessage($sender, TextFormat::YELLOW . "You are being teleported to the $args[0] island!");
    }
}