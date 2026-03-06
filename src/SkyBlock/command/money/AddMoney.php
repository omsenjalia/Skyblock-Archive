<?php


namespace SkyBlock\command\money;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\Util;

class AddMoney extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'addmoney', 'Give Money to a player', '<player> <amount>', true, ['givemoney'], "core.add.money");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (($sender instanceof Player && !Main::getInstance()->isTrusted($sender->getName())) && !Main::getInstance()->hasOp($sender)) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (!isset($args[0]) || !isset($args[1])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /givemoney <player> <amount>");
            return;
        }
        $player = $args[0];
        $money = Util::convertToFloat($args[1]);
        if ($money <= 0) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Amount must be a valid number!");
            return;
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
        if ($user !== null) {
            $user->addMoney($money);
            $this->sendMessage($user->getPlayer(), TextFormat::YELLOW . "You have been given $" . number_format($money));
            $this->sendMessage($sender, TextFormat::YELLOW . $user->getName() . "'s has been given $" . number_format($money));
        } else {
            if (!Main::getInstance()->getDb()->isPlayerRegistered($player)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player has never connected!");
                return;
            }
            Main::getInstance()->getDb()->addUserMoney($player, $money);
            $this->sendMessage($sender, TextFormat::YELLOW . $player . " has received $" . number_format($money));
        }
    }
}