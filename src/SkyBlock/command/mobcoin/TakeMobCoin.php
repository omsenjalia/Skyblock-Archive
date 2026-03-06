<?php


namespace SkyBlock\command\mobcoin;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class TakeMobCoin extends BaseCommand {

    /**
     * TakeMobCoin constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'takemobcoin', "", "", true, [], "core.take.mobcoin");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (($sender instanceof Player && !Main::getInstance()->isTrusted($sender->getName())) && !Main::getInstance()->hasOp($sender)) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (!isset($args[0]) || !isset($args[1])) {
            $this->sendMessage($sender, TextFormat::RED . "Usage: /takemobcoin <player> <mobcoin>");
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
            $user->removeMobCoin($mobcoin);
            $this->sendMessage($user->getPlayer(), TextFormat::YELLOW . "You have lost: " . number_format($mobcoin) . " mobcoins!");
            $this->sendMessage($sender, TextFormat::YELLOW . $user->getName() . " has lost: " . number_format($mobcoin) . " mobcoins!");
        } else {
            if (!Main::getInstance()->getDb()->isPlayerRegistered($player)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player has never connected!");
                return;
            }
            Main::getInstance()->getDb()->subtractUserMobCoin($player, $mobcoin);
            $this->sendMessage($sender, TextFormat::YELLOW . $player . " has lost: " . number_format($mobcoin) . " mobcoins!");
        }
    }
}