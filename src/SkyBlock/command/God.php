<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class God extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'god');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!Main::getInstance()->staffapi->isHardStaff($sender->getName()) && !isset(Main::getInstance()->gandalf->edit[$sender->getName()])) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (isset($args[0])) {
            if (!Main::getInstance()->isTrusted($sender->getName())) {
                $this->sendMessage($sender, self::NO_PERMISSION);
                return;
            }
            $player = $args[0];
            $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
            if ($user === null) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
                return;
            }
            if (isset(Main::getInstance()->god[$player])) {
                unset(Main::getInstance()->god[$player]);
                $this->sendMessage($user->getPlayer(), TextFormat::YELLOW . "You are no longer in god mode!");
                $this->sendMessage($sender, TextFormat::YELLOW . $user->getName() . " is no longer in god mode!");
                return;
            }
            Main::getInstance()->god[$player] = $player;
            $this->sendMessage($user->getPlayer(), TextFormat::YELLOW . "You are now in god mode!");
            $this->sendMessage($sender, TextFormat::YELLOW . $user->getName() . " is now in god mode!");
        } else {
            if (Main::getInstance()->isInCombat($sender)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot use this command in combat!");
                return;
            }
            if (isset(Main::getInstance()->god[$sender->getName()])) {
                unset(Main::getInstance()->god[$sender->getName()]);
                $this->sendMessage($sender, TextFormat::YELLOW . "You are now in god mode anymore!");
                return;
            }
            Main::getInstance()->god[$sender->getName()] = true;
            $this->sendMessage($sender, TextFormat::YELLOW . "You are now in god mode!");
        }
    }
}