<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Gms extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'gms', 'Survival Mode', '', true, ['survival'], "core.gms");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset(Main::getInstance()->gandalf->edit[$sender->getName()]) && !Main::getInstance()->hasOp($sender)) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (Main::getInstance()->isInCombat($sender)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot use this command while in combat!");
            return;
        }
        if (!isset($args[0])) {
            if ($sender->getGamemode() === GameMode::SURVIVAL) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You are already in survival mode!");
                return;
            }
            $sender->setGamemode(GameMode::SURVIVAL);
            $this->sendMessage($sender, TextFormat::YELLOW . "Your gamemode has been set to survival!");
        } else {
            if (!Main::getInstance()->isTrusted($sender->getName())) {
                $this->sendMessage($sender, self::NO_PERMISSION);
                return;
            }
            $player = strtolower($args[0]);
            $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
            if ($user === null) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
                return;
            }
            if ($user->getPlayer()->getGamemode() === GameMode::SURVIVAL) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is already in survival mode!");
                return;
            }
            $user->getPlayer()->setGamemode(GameMode::SURVIVAL);
            $this->sendMessage($user->getPlayer(), TextFormat::YELLOW . "Your gamemode has been set to survival!");
            $this->sendMessage($sender, TextFormat::YELLOW . $player . "'s gamemode has been set to survival!");
        }
    }
}