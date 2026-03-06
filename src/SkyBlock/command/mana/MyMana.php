<?php


namespace SkyBlock\command\mana;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class MyMana extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'mymana', 'Check your mana', '', true, ['mana']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
        $this->sendMessage($sender, TextFormat::YELLOW . "Your mana: $" . number_format($user->getMana()));
    }
}