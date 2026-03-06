<?php


namespace SkyBlock\command\warps;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class SetPos extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'setpos', 'Set warp pos', '<key> <key1>');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0]) || !isset($args[1])) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        $key = strtolower($args[0]);
        $nested = strtolower($args[1]);
        if (!Main::getInstance()->common->exists($key)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That key does not exist!");
            return;
        }
        if ($key === "warps") {
            if (!isset(Main::getInstance()->warps[$nested])) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That warp was not found!");
                return;
            }
            if ($nested === "warzone" || $nested === "dropparty") {
                if (!isset($args[2]) || !is_int((int) $args[2])) {
                    $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /setpos <warps> <warzone/dropparty> <spawn number>");
                    return;
                }
                if ($args[2] < 0 || $args[2] > 6) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " The spawn number must be 0-6!");
                    return;
                }
                Main::getInstance()->warps[$nested][$args[2]]["x"] = (int) $sender->getPosition()->getX();
                Main::getInstance()->warps[$nested][$args[2]]["y"] = (int) $sender->getPosition()->getY();
                Main::getInstance()->warps[$nested][$args[2]]["z"] = (int) $sender->getPosition()->getZ();
                $this->sendMessage($sender, TextFormat::YELLOW . "$key $nested $args[2] position has been set!");
            } else {
                Main::getInstance()->warps[$nested][$args[2]]["x"] = (int) $sender->getPosition()->getX();
                Main::getInstance()->warps[$nested][$args[2]]["y"] = (int) $sender->getPosition()->getY();
                Main::getInstance()->warps[$nested][$args[2]]["z"] = (int) $sender->getPosition()->getZ();
                $this->sendMessage($sender, TextFormat::YELLOW . "$key $nested position has been set!");
            }
        } else if ($key === "dropparty") {
            if (!isset(Main::getInstance()->dropparty[$nested])) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . "The available dropparty positions are: middle, c1, c2, c3, c4");
                return;
            }
            Main::getInstance()->dropparty[$nested]['x'] = (int) $sender->getPosition()->getX();
            Main::getInstance()->dropparty[$nested]['y'] = (int) $sender->getPosition()->getY();
            Main::getInstance()->dropparty[$nested]['z'] = (int) $sender->getPosition()->getZ();
            $this->sendMessage($sender, TextFormat::YELLOW . "$key $nested position has been set!");
        } else if ($key === "particles") {
            if (!isset(Main::getInstance()->particles[$nested])) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " The available particle positions are: combat, farming, gambling, mining, topblocks, topplayed, topislands, topgangs, stats, welcome");
                return;
            }
            Main::getInstance()->particles[$nested]['x'] = (int) $sender->getPosition()->getX();
            Main::getInstance()->particles[$nested]['y'] = (int) $sender->getPosition()->getY() + 3;
            Main::getInstance()->particles[$nested]['z'] = (int) $sender->getPosition()->getZ();
            $this->sendMessage($sender, TextFormat::YELLOW . "$key $nested position has been set!");
        } else if ($key === "envoy") {
            if (!isset(Main::getInstance()->warzone[$nested])) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " The available envoy numbers are 0-20!");
                return;
            }
            Main::getInstance()->warzone[$nested]['x'] = (int) $sender->getPosition()->getX();
            Main::getInstance()->warzone[$nested]['y'] = (int) $sender->getPosition()->getY();
            Main::getInstance()->warzone[$nested]['z'] = (int) $sender->getPosition()->getZ();
        } else {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Error.");
        }
    }
}