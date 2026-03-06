<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Tell extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'tell', 'Send PM to a player', '[player] <msg>', true, ['whisper', 'm', 'msg', 'pm', 'dm']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player && !$sender instanceof ConsoleCommandSender) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0]) || !isset($args[1])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /tell <player> <message>");
            return;
        }
        $player = Server::getInstance()->getPlayerByPrefix(strtolower($args[0]));
        if ($player === null) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
            return;
        }
        if (strtolower($sender->getName()) === strtolower($player->getName())) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot message yourself!");
            return;
        }
        if ($player->isInvisible()) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is invisible!");
            return;
        }
        if ($sender instanceof ConsoleCommandSender) {
            $displayName = "CONSOLE";
        } else {
            $displayName = $sender->getDisplayName();
        }
        /** @var \Common\Main $common */
        $common = Server::getInstance()->getPluginManager()->getPlugin("Common");
        array_shift($args);
        $message = implode(" ", $args);
        $message = TextFormat::clean($message);
        $sender->sendMessage("§a➼§e[me -> {$player->getDisplayName()}] §r$message");
        $player->sendMessage("§a➼§e[$displayName -> me] §r$message");
        $owners = ["joshy3282", "infern101"];
        if ($sender->getName() !== "CONSOLE" && !in_array($sender->getName(), $owners) && !in_array($player->getName(), $owners)) {
            $seepms = $common->seepms;
            foreach ($seepms as $staff) {
                if (strtolower($sender->getName()) !== $staff && strtolower($player->getName()) !== $staff) {
                    $user = Main::getInstance()->getUserManager()->getOnlineUser($staff);
                    $user?->getPlayer()->sendMessage("§a➼§e[$displayName -> {$player->getDisplayName()}] §r$message");
                }
            }
        }
        Main::getInstance()->reply[strtolower($player->getName())] = strtolower($sender->getName());
        Main::getInstance()->reply[strtolower($sender->getName())] = strtolower($player->getName());
    }
}