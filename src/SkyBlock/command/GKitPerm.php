<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class GKitPerm extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'gkitperm', "", "", true, [], "core.gkitperm");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!Main::getInstance()->hasOp($sender)) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if ($sender instanceof Player && !Main::getInstance()->isTrusted($sender->getName())) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (!isset($args[0]) || !isset($args[1])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /gkitperm <achilles | theo | cosmo | arcadia | artemis | calisto> <player> <count>");
            return;
        }
        if (!isset($args[2])) {
            $count = 1;
        } else {
            $count = $args[2];
        }
        $gkit = $args[0];
        $player = strtolower($args[1]);
        if (!Main::getInstance()->getDb()->isPlayerRegistered($player)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player has never connected!");
            return;
        }
        if (!isset(Main::getInstance()->gkits[$gkit])) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Provided gkit type not found. Use /gkitperm to see available gkits!");
            return;
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
        if ($user === null) {
            Main::getInstance()->getDb()->addKitCount($player, $gkit, $count);
            return;
        }
        $user->addKitCount($gkit, $count);
        $this->sendMessage($user->getPlayer(), TextFormat::YELLOW . "You have successfully claimed x$count $gkit's. Use /gkit to claim them!");
        $this->sendMessage($sender, TextFormat::YELLOW . "Successfully gave x$count $gkit's to $player!");
    }
}