<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\Data;
use SkyBlock\Main;

class Feed extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'feed', 'Feed a player', '[player]');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!Main::getInstance()->staffapi->hasStaffRank($sender->getName())) {
            if (!$sender->hasPermission("core.feed")) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy SkyGOD rank on " . TextFormat::AQUA . "shop.fallentech.io");
                return;
            }
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
        if (!$user->removeMoney(Data::$commandFeedCost)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You need " . Data::$commandFeedCost . " to feed yourself!");
            return;
        }
        if (!isset($args[0])) {
            $player = $sender;
        } else {
            $playerName = strtolower($args[0]);
            $player = Server::getInstance()->getPlayerExact($playerName);
            if ($player === null) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
                return;
            }
        }
        if (Main::getInstance()->isInCombat($player)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is currently in combat!");
            return;
        }
        $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
        $player->getHungerManager()->setSaturation(20);
        if ($sender->getName() !== $player->getName()) {
            $this->sendMessage($player, TextFormat::YELLOW . "You have been fed by " . $sender->getName());
            $this->sendMessage($sender, TextFormat::YELLOW . "You have fed " . $player->getName());
        } else {
            $this->sendMessage($sender, TextFormat::YELLOW . "You have fed yourself!");
        }
    }
}