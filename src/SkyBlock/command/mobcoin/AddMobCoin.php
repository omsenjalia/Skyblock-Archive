<?php


namespace SkyBlock\command\mobcoin;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class AddMobCoin extends BaseCommand {

    /**
     * AddMobCoin constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'addmobcoin', 'Give Mob Coin to a player', '[player] <coin>', true, ['givemobcoin'], "core.add.mobcoin");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (($sender instanceof Player && !Main::getInstance()->isTrusted($sender->getName())) && !Main::getInstance()->hasOp($sender)) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (!isset($args[0]) || !isset($args[1])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /addmobcoin <player> <amount>");
            return;
        }
        $player = $args[0];
        if (!is_int((int) $args[1]) || $args[1] <= 0) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Amount must be a valid number!");
            return;
        }
        $mobcoin = (int) $args[1];
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
        if ($user !== null) {
            $user->addMobCoin($mobcoin);
            $this->sendMessage($user->getPlayer(), TextFormat::YELLOW . "You have been given " . number_format($mobcoin) . " mobcoins!");
            $this->sendMessage($sender, TextFormat::YELLOW . $user->getName() . "'s has been given " . number_format($mobcoin) . " mobcoins!");
        } else {
            if (!Main::getInstance()->getDb()->isPlayerRegistered($player)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player has never connected!");
                return;
            }
            Main::getInstance()->getDb()->addUserMobCoin($player, $mobcoin);
            $this->sendMessage($sender, TextFormat::YELLOW . $player . " has received " . number_format($mobcoin) . " mobcoins!");
        }
    }
}