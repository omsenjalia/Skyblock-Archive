<?php


namespace SkyBlock\command\mobcoin;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class MyMobCoin extends BaseCommand {

    /**
     * MyMobCoin constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'mymobcoin', 'Check your mob coins', "", true, ["mymobcoins", "mobcoins", "mymc"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
        $this->sendMessage($sender, TextFormat::YELLOW . "Your mobcoins: $" . number_format($user->getMobCoin()));
    }
}