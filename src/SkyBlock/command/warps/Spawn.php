<?php


namespace SkyBlock\command\warps;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class Spawn extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'spawn', 'Warp to spawn', '', true, ['hub', 'lobby']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (isset($args[0])) {
            if (!$sender->hasPermission("core.staff") && !Main::getInstance()->staffapi->isSoftStaff($sender->getName())) {
                $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /spawn");
                return;
            }
            if (!isset($args[1])) {
                $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /spawn <player> <reason>");
                return;
            }
            $playerName = strtolower($args[0]);
            $player = Server::getInstance()->getPlayerByPrefix($playerName);
            if (!$player instanceof Player) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
                return;
            }
            if (Main::getInstance()->staffapi->isSoftStaff($player->getName())) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot send another staff member to spawn!");
                return;
            }
            array_shift($args);
            $reason = implode(" ", $args);
            $reason = trim($reason, "'");
            if (strlen($reason) < 4) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Your reason must be more than 4 characters!");
                return;
            }
            Main::getInstance()->teleportToSpawn($player);
            $this->sendMessage($player, TextFormat::YELLOW . "You have been teleported to spawn by staff for `$reason`!");
            $this->sendMessage($sender, TextFormat::YELLOW . $player->getName() . " has been teleported to spawn for `$reason`!");
        } else {
            if (!$sender instanceof Player) {
                $this->sendMessage($sender, self::NO_CONSOLE);
                return;
            }
            if (Main::getInstance()->isInCombat($sender)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot use this command in combat!");
                return;
            }
            $this->sendMessage($sender, TextFormat::YELLOW . "Teleporting to spawn!");
            Main::getInstance()->teleportToSpawn($sender);
        }
    }
}