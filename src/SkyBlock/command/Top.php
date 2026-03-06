<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Top extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'top', "", "", true, [], "core.top");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!Main::getInstance()->hasOp($sender)) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        $this->sendMessage($sender, TextFormat::YELLOW . "Teleporting you to the top!");
        $sender->teleport($sender->getPosition()->add(0, $sender->getPosition()->getWorld()->getHighestBlockAt($sender->getPosition()->getFloorX(), $sender->getPosition()->getFloorZ()) + 1, 0));
    }
}