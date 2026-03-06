<?php


namespace SkyBlock\command\mobcoin;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class SetMobCoin extends BaseCommand {

    /**
     * SetMobCoin constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'setmobcoin', 'Set a players mob coins', '[player] <coin>', 'core.set.mobcoin');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (($sender instanceof Player && !Main::getInstance()->isTrusted($sender->getName())) && !Main::getInstance()->hasOp($sender)) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (!isset($args[0]) || !isset($args[1])) {
            $this->sendMessage($sender, TextFormat::RED . "Usage: /setmobcoin <player> <mobcoin>");
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
            $user->setMobCoin($mobcoin);
            $this->sendMessage($user->getPlayer(), TextFormat::YELLOW . "Your mobcoin has been set to " . number_format($mobcoin));
            $this->sendMessage($sender, TextFormat::YELLOW . $user->getName() . "'s mobcoin has been set to " . number_format($mobcoin));
        } else {
            if (!Main::getInstance()->getDb()->isPlayerRegistered($player)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player has never connected!");
                return;
            }
            Main::getInstance()->getDb()->setUserMobCoin($player, $mobcoin);
            $this->sendMessage($sender, TextFormat::YELLOW . $player . "'s mobcoin has been set to: " . number_format($mobcoin));
        }
    }
}