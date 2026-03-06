<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class SeeXP extends BaseCommand {

    /**
     * SeeXP constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'seexp', 'See players XP');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /seexp <player>");
            return;
        }
        $player = strtolower($args[0]);
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
        if ($user !== null) {
            $this->sendMessage($sender, TextFormat::YELLOW . $user->getName() . "'s XP: " . number_format($user->getXP()));
        } else {
            if (!Main::getInstance()->getDb()->isPlayerRegistered($player)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player has never connected!");
                return;
            }
            $this->sendMessage($sender, TextFormat::YELLOW . $player . "'s XP: " . number_format(Main::getInstance()->getDb()->getPlayerXP($player)));
        }
    }
}