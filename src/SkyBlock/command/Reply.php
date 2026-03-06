<?php


namespace SkyBlock\command;


use pmmp\RconServer\RconCommandSender;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Reply extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'reply', 'Reply to the last PM', '<msg>', true, ['r',]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player and !$sender instanceof RconCommandSender and !$sender instanceof ConsoleCommandSender) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /reply <message>");
            return;
        }
        if (!isset(Main::getInstance()->reply[strtolower($sender->getName())])) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You have nobody to reply to!");
            return;
        }
        $player = Main::getInstance()->reply[strtolower($sender->getName())];
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
        if ($user === null) {
            unset(Main::getInstance()->reply[strtolower($sender->getName())]);
            unset(Main::getInstance()->reply[$player]);
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online anymore!");
            return;
        }
        if ($user->getPlayer()->isInvisible()) {
            unset(Main::getInstance()->reply[strtolower($sender->getName())]);
            unset(Main::getInstance()->reply[$player]);
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is invisible!");
            return;
        }
        if ($sender instanceof ConsoleCommandSender) {
            $displayName = "CONSOLE";
        } else {
            $displayName = $sender->getDisplayName();
        }
        $message = implode(" ", $args);
        $message = TextFormat::clean($message);
        /** @var \Common\Main $common */
        $common = Server::getInstance()->getPluginManager()->getPlugin("Common");
        $sender->sendMessage("§a➼§e[me -> {$user->getPlayer()->getDisplayName()}] §r$message");
        $user->getPlayer()->sendMessage("§a➼§e[$displayName -> me] §r$message");
        $owners = ["joshy3282", "infern101"];
        if ($sender->getName() !== "CONSOLE" && !in_array($sender->getName(), $owners) && !in_array($user->getPlayer()->getName(), $owners)) {
            $seepms = $common->seepms;
            foreach ($seepms as $staff) {
                if (strtolower($sender->getName()) !== $staff && strtolower($user->getPlayer()->getName() !== $staff)) {
                    $user2 = Main::getInstance()->getUserManager()->getOnlineUser($staff);
                    $user2?->getPlayer()->sendMessage("§a➼§e[$displayName -> {$user->getPlayer()->getDisplayName()}] §r$message");
                }
            }
        }
        Main::getInstance()->reply[strtolower($player)] = strtolower($sender->getName());
        Main::getInstance()->reply[strtolower($sender->getName())] = strtolower($player);
    }
}