<?php


namespace SkyBlock\command\warps;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class World extends BaseCommand {
    /**
     * World constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, "world", "Teleport to a world", "<world>", true, [], "core.world");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!isset(Main::getInstance()->gandalf->edit[$sender->getName()]) && !Main::getInstance()->hasOp($sender)) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /world <name>");
            return;
        }
        if (!Server::getInstance()->getWorldManager()->loadWorld($args[0], true)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Could not load the world $args[0]!");
            return;
        }
        $sender->teleport(Server::getInstance()->getWorldManager()->getWorldByName($args[0])->getSpawnLocation(), 0.0, 0.0);
        $this->sendMessage($sender, TextFormat::YELLOW . "You are being teleported to world $args[0]!");
    }
}