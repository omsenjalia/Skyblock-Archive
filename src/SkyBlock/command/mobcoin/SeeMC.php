<?php


namespace SkyBlock\command\mobcoin;


use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class SeeMC extends BaseCommand {

    /**
     * SeeMC constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'seemc', 'See players mob coins');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /seemobcoin <player>");
            return;
        }
        $player = strtolower($args[0]);
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
        if ($user !== null) {
            $this->sendMessage($sender, TextFormat::YELLOW . $user->getMobCoin() . "'s mobcoins: " . number_format($user->getMobCoin()));
        } else {
            if (!Main::getInstance()->getDb()->isPlayerRegistered($player)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player has never connected!");
                return;
            }
            $mobcoin = Main::getInstance()->getDb()->getPlayerMobCoin($player);
            $this->sendMessage($sender, TextFormat::YELLOW . $player . "'s mobcoins: " . number_format($mobcoin));
        }
    }
}