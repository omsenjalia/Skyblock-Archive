<?php

namespace SkyBlock\command;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class SetXp extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'setxp', 'Set a players xp', '[player] <xp>', 'core.set.xp');
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
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /setxp <player> <xp>");
            return;
        }
        $player = $args[0];
        $xp = $args[1];
        if (!is_int((int) $xp) || $xp < 0) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is not a valid amount of XP!");
            return;
        }
        $xp = (int) $xp;
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
        if ($user !== null) {
            $user->setXP($xp);
            $this->sendMessage($user->getPlayer(), TextFormat::YELLOW . "Your XP has been set to " . number_format($xp));
            $this->sendMessage($sender, TextFormat::YELLOW . $user->getName() . "'s XP has been set to " . number_format($xp));
        } else {
            if (!Main::getInstance()->getDb()->isPlayerRegistered($player)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player has never connected!");
                return;
            }
            Main::getInstance()->getDb()->setUserXp($player, $xp);
            $this->sendMessage($sender, TextFormat::YELLOW . $player . "'s XP has been set to " . number_format($xp));
        }
    }
}