<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Gmc extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'gmc', 'Creative Mode', '', true, ['creative'], "core.gmc");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
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
            if ($sender->getGamemode() === GameMode::CREATIVE) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You are already in creative mode!");
                return;
            }
            $sender->setGamemode(GameMode::CREATIVE);
            $this->sendMessage($sender, TextFormat::YELLOW . "Your gamemode has been set to creative!");
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
            if ($user->getPlayer()->getGamemode() === GameMode::CREATIVE) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is already in creative mode!");
                return;
            }
            $user->getPlayer()->setGamemode(GameMode::CREATIVE);
            $this->sendMessage($user->getPlayer(), TextFormat::YELLOW . "Your gamemode has been set to creative!");
            $this->sendMessage($sender, TextFormat::YELLOW . $player . "'s gamemode has been set to creative!");
        }

    }
}