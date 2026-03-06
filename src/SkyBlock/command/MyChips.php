<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class MyChips extends BaseCommand {
    /**
     * MyChips constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'mychips', 'Check your chips');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
        $chips = $user->getChips();
        $this->sendMessage($sender, TextFormat::YELLOW . "You have $chips casino chips!");
    }
}