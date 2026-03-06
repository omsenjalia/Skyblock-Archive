<?php


namespace SkyBlock\command\mobcoin;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class PayMC extends BaseCommand {

    /**
     * Pay constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'paymobcoin', "Pay mob coin", "", true, ["paymc", "paymobcoins"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0]) || !isset($args[1])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /paymc <player> <mobcoin>");
            return;
        }
        if (!is_int((int) $args[1]) || $args[1] <= 0) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Amount must be a valid number!");
            return;
        }
        $mobcoin = (int) $args[1];
        if (strtolower($sender->getName()) === strtolower($args[0])) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot pay yourself!");
            return;
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
        if (!$user->hasMobCoin($mobcoin)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You dont have " . number_format($mobcoin) . " mobcoin to give!");
            return;
        }
        $max = 20;
        if (isset(Main::getInstance()->paymc[$sender->getName()])) {
            $left = Main::getInstance()->paymc[$sender->getName()] - time();
            if ($left > 0) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Please wait $left more seconds to pay again!");
                return;
            }
        }
        $user2 = Main::getInstance()->getUserManager()->getOnlineUser($args[0]);
        if ($user2 !== null) {
            $user->removeMobCoin($mobcoin);
            $user2->addMobCoin($mobcoin, false);
            Main::getInstance()->paymc[$sender->getName()] = time() + $max;
            $this->sendMessage($sender, TextFormat::YELLOW . "You have given " . number_format($mobcoin) . " mobcoin to " . $user2->getName() . "! You now have " . number_format($user->getMobCoin()) . " mobcoin!");
            $this->sendMessage($user2->getPlayer(), TextFormat::YELLOW . "You have received " . number_format($mobcoin) . " mobcoin from " . $user->getName() . "! You now have " . number_format($user2->getMobCoin()) . " mobcoin!");
        } else {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
        }
    }

}