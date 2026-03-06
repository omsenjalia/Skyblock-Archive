<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class BlocksBroken extends BaseCommand {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'blocksbroken', 'Server ore blocks broken count', '', true, ['bb', 'blockbroken']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        $blocksBroken = Main::getInstance()->serverblocks;
        $this->sendMessage($sender, TextFormat::GREEN . "Total server blocks broken - " . TextFormat::YELLOW . number_format($blocksBroken));
        if ($sender instanceof Player) {
            $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
            $blocks = $user->getBlocksBroken();
            if ($blocksBroken !== 0) {
                $percent = ($blocks / $blocksBroken) * 100;
            } else {
                $percent = 0;
            }
            $this->sendMessage($sender, TextFormat::GREEN . "You have contributed " . TextFormat::YELLOW . number_format($blocks) . " (" . number_format($percent, 2) . "%)");
        }
    }
}