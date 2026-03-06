<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;
use SkyBlock\Main;

class Time extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'time', 'Set time of a world', 'set <day | night> | /time stop');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender->hasPermission("pocketmine.command.time")) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy SkyLord rank on " . TextFormat::AQUA . "shop.fallentech.io");
            return;
        }
        if (!isset($args[0]) || !$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        $world = $sender->getWorld();
        if (!Main::getInstance()->hasOp($sender)) {
            $island = Main::getInstance()->getIslandManager()->getOnlineIslandByWorld($world->getDisplayName());
            if ($island === null) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can only change the time of your island!");
                return;
            }
            if (!$island->isMember($sender->getName())) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You are not a member of this island!");
                return;
            }
        }
        if (strtolower($args[0]) === "set") {
            if (!isset($args[1])) {
                $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /time set <day/night/time> | /time stop | /time start");
                return;
            }
            if ($args[1] === "day") {
                $value = World::TIME_DAY;
            } elseif ($args[1] === "night") {
                $value = World::TIME_NIGHT;
            } elseif (is_int((int) $args[1])) {
                $value = (int) $args[1];
                if ($value < 0) {
                    $value = 0;
                } elseif ($value > 24000) {
                    $value = 24000;
                }
            } else {
                $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /time set <day/night/time>");
                return;
            }
            $world->setTime($value);
            $this->sendMessage($sender, TextFormat::YELLOW . "Time has been set to $value!");
        }
        if (strtolower($args[0]) === "start") {
            $world->startTime();
            $this->sendMessage($sender, TextFormat::YELLOW . "Time has been started!");
        }
        if (strtolower($args[0]) === "stop") {
            $world->stopTime();
            $this->sendMessage($sender, TextFormat::YELLOW . "Time has been stopped!");
        }
    }
}