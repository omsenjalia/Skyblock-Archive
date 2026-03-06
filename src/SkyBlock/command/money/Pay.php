<?php


namespace SkyBlock\command\money;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\Util;

class Pay extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'pay');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0]) || !isset($args[1])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /pay <player> <money>");
            return;
        }
        $money = Util::convertToFloat($args[1]);
        if ($money <= 0) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Amount must be above 0!");
            return;
        }
        if (strtolower($sender->getName()) === strtolower($args[0])) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot pay yourself!");
            return;
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
        if (!$user->hasMoney($money)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You dont have $" . number_format($money) . " to give!");
            return;
        }
        $max = 20;
        if (isset(Main::getInstance()->pay[$sender->getName()])) {
            $left = Main::getInstance()->pay[$sender->getName()] - time();
            if ($left > 0) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Please wait $left more seconds to pay again!");
                return;
            }
        }
        $user2 = Main::getInstance()->getUserManager()->getOnlineUser($args[0]);
        if ($user2 !== null) {
            $user->removeMoney($money);
            $user2->addMoney($money, false);
            Main::getInstance()->pay[$sender->getName()] = time() + $max;
            $this->sendMessage($sender, TextFormat::YELLOW . "You have given $" . number_format($money) . " to " . $user2->getName() . "! You now have $" . number_format($user->getName()));
            $this->sendMessage($user2->getPlayer(), TextFormat::YELLOW . "You have received $" . number_format($money) . " from " . $user->getName() . "! You now have $" . number_format($user2->getName()));
        } else {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
        }
    }
}