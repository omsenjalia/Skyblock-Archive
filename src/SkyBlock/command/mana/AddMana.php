<?php


namespace SkyBlock\command\mana;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class AddMana extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'addmana', 'Give Mana to a player', '[player] <mana>', true, ['givemana'], "core.add.mana");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (($sender instanceof Player && !Main::getInstance()->isTrusted($sender->getName())) && !Main::getInstance()->hasOp($sender)) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (!isset($args[0]) || !isset($args[1])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /addmana <player> <amount>");
            return;
        }
        $player = $args[0];
        if (!is_int((int) $args[1]) || $args[1] <= 0) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Amount must be a valid number!");
            return;
        }
        $mana = (int) $args[1];
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
        if ($user !== null) {
            $user->addMana($mana);
            $this->sendMessage($user->getPlayer(), TextFormat::YELLOW . "You have been given " . number_format($mana) . " mana!");
            $this->sendMessage($sender, TextFormat::YELLOW . $user->getName() . "'s has been given " . number_format($mana) . " mana!");
        } else {
            if (!Main::getInstance()->getDb()->isPlayerRegistered($player)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player has never connected!");
                return;
            }
            Main::getInstance()->getDb()->addUserMana($player, $mana);
            $this->sendMessage($sender, TextFormat::YELLOW . $player . " has received " . number_format($mana) . " mana!");
        }
    }
}