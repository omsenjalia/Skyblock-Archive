<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\util\Values;

class TempFly extends BaseCommand {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'tempfly', 'Give temp fly to player', '<player>');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if ($sender instanceof Player) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /tempfly <player>");
            return;
        }
        $playerName = strtolower($args[0]);
        $player = Server::getInstance()->getPlayerExact($playerName);
        if (!$player instanceof Player) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
            return;
        }
        if (!$player->hasPermission("core.fly")) {
            Main::getInstance()->flycount[$player->getName()] = time();
            $this->sendMessage($player, TextFormat::YELLOW . "You have received /fly from voting. You will be able to fly for " . (Values::FLY_TIME / 3600) . " hours!");
        }
    }
}