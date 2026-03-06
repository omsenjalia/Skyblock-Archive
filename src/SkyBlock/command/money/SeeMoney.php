<?php


namespace SkyBlock\command\money;


use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class SeeMoney extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'seemoney', 'See players money');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /seemoney <player>");
            return;
        }
        $player = strtolower($args[0]);
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
        if ($user !== null) {
            $this->sendMessage($sender, TextFormat::YELLOW . $user->getMoney() . "'s money: $" . number_format($user->getMoney()));
            return;
        } else {
            if (!Main::getInstance()->getDb()->isPlayerRegistered($player)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player has never connected!");
                return;
            }
            $money = Main::getInstance()->getDb()->getPlayerMoney($player);
            $this->sendMessage($sender, TextFormat::YELLOW . $player . "'s money: $" . number_format($money));
        }
    }
}