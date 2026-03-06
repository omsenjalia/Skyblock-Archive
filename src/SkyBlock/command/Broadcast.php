<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Broadcast extends BaseCommand {
    /**
     * Broadcast constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'broadcast', 'Broadcast on a server', '<message>', true, ['bcast'], "core.broadcast");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!Main::getInstance()->hasOp($sender)) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /broadcast <message>");
            return;
        }
        Server::getInstance()->broadcastMessage(TextFormat::YELLOW . "»>\n" . TextFormat::GOLD . "[FT]»> " . TextFormat::AQUA . implode(" ", $args) . "\n" . TextFormat::YELLOW . "»>");
    }

}