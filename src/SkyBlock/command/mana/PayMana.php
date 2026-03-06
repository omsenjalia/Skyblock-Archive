<?php


namespace SkyBlock\command\mana;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class PayMana extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'paymana');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0]) || !isset($args[1])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /paymana <player> <mana>");
            return;
        }
        if (!is_int((int) $args[1]) || $args[1] <= 0) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Amount must be a valid number!");
            return;
        }
        $mana = (int) $args[1];
        if (strtolower($sender->getName()) === strtolower($args[0])) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot pay yourself!");
            return;
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
        if (!$user->hasMana($mana)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You dont have " . number_format($mana) . " mana to give!");
            return;
        }
        $max = 20;
        if (isset(Main::getInstance()->payMana[$sender->getName()])) {
            $left = Main::getInstance()->payMana[$sender->getName()] - time();
            if ($left > 0) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Please wait $left more seconds to pay again!");
                return;
            }
        }
        $user2 = Main::getInstance()->getUserManager()->getOnlineUser($args[0]);
        if ($user2 !== null) {
            $user->removeMana($mana);
            $user2->addMana($mana, false);
            Main::getInstance()->payMana[$sender->getName()] = time() + $max;
            $this->sendMessage($sender, TextFormat::YELLOW . "You have given " . number_format($mana) . " mana to " . $user2->getName() . "! You now have " . number_format($user->getMana()) . " mana!");
            $this->sendMessage($user2->getPlayer(), TextFormat::YELLOW . "You have received " . number_format($mana) . " mana from " . $user->getName() . "! You now have " . number_format($user2->getMana()) . " mana!");
        } else {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
        }
    }
}