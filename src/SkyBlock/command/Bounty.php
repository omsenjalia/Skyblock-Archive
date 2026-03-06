<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;
use SkyBlock\Main;

class Bounty extends BaseCommand {

    public const COOLDOWN = 2 * 60;
    public static array $bountyTimer = [];

    public function __construct(Main $plugin) {
        parent::__construct($plugin, "bounty", "Bounty Help");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /bounty <set / me / search / top>");
            return;
        }
        switch ($args[0]) {
            case "set":
            case "add":
                if (!isset($args[1]) || !isset($args[2])) {
                    $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /bounty set <player> <money>");
                    return;
                }
                $player = strtolower($args[1]);
                if ($player == strtolower($sender->getName())) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot give yourself a bounty!");
                    return;
                }
                if (isset(self::$bountyTimer[$sender->getName()])) {
                    $left = self::$bountyTimer[$sender->getName()] - time();
                    if ($left > 0) {
                        $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Please wait $left seconds to use this command again!");
                        return;
                    }
                }
                $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
                if ($user === null) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
                    return;
                }
                $money = $args[2];
                if (!is_int((int) $money)) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is not a valid amount for bounty!");
                    break;
                }
                $money = (int) $money;
                if ($money <= 0) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is not a valid amount for bounty!");
                    break;
                }
                $user2 = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
                if (!$user->removeMobCoin($money)) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You do not have " . number_format($money) . " to set that bounty!");
                    return;
                }
                $user->addBounty($money);
                self::$bountyTimer[$sender->getName()] = time() + self::COOLDOWN;
                $this->sendMessage($sender, "Added $" . number_format($money) . " as a bounty to " . $args[1] . "!");
                $this->sendMessage($user->getPlayer(), "You have had a $" . number_format($money) . " bounty put on you by " . $sender->getName() . "!");
                break;
            case "me":
                $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
                $this->sendMessage($sender, TextFormat::YELLOW . "Your bounty is: $" . number_format($user->getBounty()));
                break;
            case "search":
            case "user":
                if (!isset($args[1])) {
                    $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /bounty search <player>");
                    return;
                }
                $playerName = strtolower($args[1]);
                $user = Main::getInstance()->getUserManager()->getOnlineUser($playerName);
                if ($user !== null) {
                    $bounty = $user->getBounty();
                } else {
                    if (!Main::getInstance()->getDb()->isPlayerRegistered($playerName)) {
                        $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player has never connected!");
                        return;
                    }
                    $bounty = Main::getInstance()->getDb()->getPlayerXPBank($playerName);
                }
                $this->sendMessage($sender, TextFormat::YELLOW . "$playerName's bounty is: $" . number_format($bounty));
                break;
            case "top":
            case "list":
                if (!isset($args[0])) {
                    $args[0] = 1;
                }
                if (!is_int((int) $args[0]) || $args[0] <= 0) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Enter a valid page number!");
                    return;
                }
                $page = (int) $args[0];
                $array = Main::getInstance()->db->prepare("SELECT COUNT(*) AS COUNT FROM player ORDER BY bounty DESC;")->execute();
                $array = $array->fetchArray(SQLITE3_ASSOC);
                $total = $array["count"];
                $pages = ceil($total / 8);
                if ($pages < $page) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That page cannot be found. Last page is $pages");
                    return;
                }
                $startNumber = ($page - 1) * 8;
                $this->sendMessage($sender, TextFormat::YELLOW . "Top Bounty List -");
                $string = TextFormat::YELLOW . "[+]" . TextFormat::WHITE . str_repeat("=", 10) . TextFormat::AQUA . "[ " . $page . "/" . $pages . " ]" . TextFormat::WHITE . str_repeat("=", 10) . TextFormat::YELLOW . "[+]\n";
                $array = Main::getInstance()->db->prepare("SELECT player, bounty FROM player ORDER BY bounty DESC LIMIT $startNumber, 8;")->execute();
                $result = $array->fetchArray(SQLITE3_ASSOC);
                while ($result) {
                    $bounty = $result["$bounty"];
                    $name = $result["player"];
                    $startNumber++;
                    $user = Main::getInstance()->getUserManager()->getOnlineUser($name);
                    if ($user === null) {
                        $string .= TextFormat::WHITE . "$startNumber. $name => $" . number_format($bounty) . " \n";
                    } else {
                        $string .= TextFormat::WHITE . "$startNumber. $name => $" . number_format($user->getBounty()) . " \n";
                    }
                }
                $this->sendMessage($sender, $string . TextFormat::YELLOW . "[+]" . TextFormat::WHITE . str_repeat("=", 26) . TextFormat::YELLOW . "[+]\n=> Pages will be reloaded after restart! <=");
            default:
                $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /bounty <set / me / search / top>");
                break;
        }
    }
}