<?php


namespace SkyBlock\command\money;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class MyMoney extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'mymoney', 'Check your money');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
        $this->sendMessage($sender, TextFormat::YELLOW . "Your balance: $" . number_format($user->getMoney()));
    }
}