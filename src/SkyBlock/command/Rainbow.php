<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Rainbow extends BaseCommand {
    const COOLDOWN = 5;

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'rainbow', 'Send a colorful text', "", true, ["rb"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /rainbow <message>");
            return;
        }
        if (!$sender->hasPermission("core.rainbow")) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy SkyWARRIOR rank on " . TextFormat::AQUA . "shop.fallentech.io");
            return;
        }
        $message = implode(" ", $args);
        if (!preg_match("/^[a-z0-9 .\-]+$/i", $message)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can use English characters!");
            return;
        }
        if (isset(Main::getInstance()->rainbow[strtolower($sender->getName())])) {
            $time = Main::getInstance()->rainbow[strtolower($sender->getName())];
            $left = self::COOLDOWN - (time() - $time);
            if ($left > 0) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Wait $left more seconds to use this command!");
                return;
            } else {
                unset(Main::getInstance()->rainbow[strtolower($sender->getName())]);
            }
        }
        /** @var \Common\Main $common */
        $common = Server::getInstance()->getPluginManager()->getPlugin("Common");
        $peace = $common->peace;
        $mute = Main::getInstance()->gandalf::$mute;
        if (isset($mute[strtolower($sender->getName())])) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You are muted!");
            return;
        }
        Main::getInstance()->rainbow[strtolower($sender->getName())] = time();
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            if (!isset($peace[$player->getName()]) && !isset($common->blocks[strtolower($player->getName())][strtolower($sender->getName())])) {
                $format = Main::getInstance()->getEvFunctions()->getNormalChatFormat($sender, $player);
                $fullMessage = $format . $this->colorize(TextFormat::clean($message));
                $player->sendMessage($fullMessage);
            }
        }
    }

    private function colorize(string $message) : string {
        $stringArray = str_split($message);
        $colors = ["a", "b", "c", "d", "e", "f", "3", "6", "9"];
        $new = "";
        foreach ($stringArray as $char) {
            $new .= "§" . $colors[mt_rand(0, count($colors) - 1)] . $char;
        }
        return $new;
    }
}