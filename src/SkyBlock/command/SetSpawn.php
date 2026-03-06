<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class SetSpawn extends BaseCommand {
    /**
     * SetSpawn constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'setspawn', 'Set worlds spawn', "", true, [], "core.setspawn");
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
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        $world = $sender->getWorld();
        $position = $sender->getPosition();
        $world->setSpawnLocation($position);
        Server::getInstance()->getWorldManager()->setDefaultWorld($world);
        $this->sendMessage($sender, TextFormat::YELLOW . "The server spawn point has been changed to " . $world->getDisplayName() . " world at " . $position->getX() . ":" . $position->getY() . ":" . $position->getZ());
    }
}