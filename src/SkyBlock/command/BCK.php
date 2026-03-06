<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class BCK extends BaseCommand {
    /**
     * BCK constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'bck', 'Give crate keys to players', "", true, [], "core.bck");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (($sender instanceof Player && !Main::getInstance()->isTrusted($sender->getName())) && !Main::getInstance()->hasOp($sender)) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (!isset($args[2])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /bck <key> <amount> <player>");
            return;
        }
        $key = strtolower($args[0]);
        if (!is_int((int) $args[1])) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Amount must be a valid number!");
            return;
        }
        $count = (int) $args[1];
        if ($count < 1 || $count > 64) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Amount should be more than 0 and less than 64!");
            return;
        }
        $player = $args[2];
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
        if ($user === null) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Player not online!");
            return;
        }
        switch (strtolower($key)) {
            case "firework":
            case "fireworks":
                $this->sendMessage($sender, "Fireworks are disabled");
                break;
            case "bundle":
                $this->sendMessage($sender, "Bundle is disabled");
                break;
            case "vote":
                $user->getPlayer()->getInventory()->addItem(Main::getInstance()->getCrateKeys('vote', $count));
                break;
            case "common":
                $user->getPlayer()->getInventory()->addItem(Main::getInstance()->getCrateKeys('common', $count));
                break;
            case "rare":
                $user->getPlayer()->getInventory()->addItem(Main::getInstance()->getCrateKeys('rare', $count));
                break;
            case "legendary":
                $user->getPlayer()->getInventory()->addItem(Main::getInstance()->getCrateKeys('legendary', $count));
                break;
            case "mystic":
                $user->getPlayer()->getInventory()->addItem(Main::getInstance()->getCrateKeys('mystic', $count));
                break;
            case "random":
                $user->getPlayer()->getInventory()->addItem(Main::getInstance()->getFunctions()->getRandomKey());
                break;
            default:
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Invalid key specified! Available options: vote, common, rare, legendary, mystic, random");
                return;
        }
        $this->sendMessage($user->getPlayer(), TextFormat::YELLOW . "You claimed " . TextFormat::AQUA . "x$count" . TextFormat::GREEN . " $key" . TextFormat::YELLOW . " keys!");
        $this->sendMessage($sender, TextFormat::YELLOW . "Successfully gave " . TextFormat::AQUA . "x$count" . TextFormat::GREEN . " $key" . TextFormat::YELLOW . " keys to " . $user->getName());
    }
}