<?php


namespace SkyBlock\command\mana;


use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class SeeMana extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'seemana', 'See players mana');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /seemana <player>");
            return;
        }
        $player = strtolower($args[0]);
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
        if ($user !== null) {
            $this->sendMessage($sender, TextFormat::YELLOW . $user->getMana() . "'s mana: " . number_format($user->getMana()));
        } else {
            if (!Main::getInstance()->getDb()->isPlayerRegistered($player)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player has never connected!");
                return;
            }
            $mana = Main::getInstance()->getDb()->getPlayerMana($player);
            $this->sendMessage($sender, TextFormat::YELLOW . $player . "'s mana: " . number_format($mana));
        }
    }
}