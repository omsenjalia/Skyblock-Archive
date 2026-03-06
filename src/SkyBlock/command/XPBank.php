<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Limits;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class XPBank extends BaseCommand {

    /**
     * XPBank constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, "xpbank", "XP Bank", "<add | rm | bank | top>", true, ['xb']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /xpbank <add / rm / bank / top>");
            return;
        }
        switch (strtolower($args[0])) {
            case "add":
                if (!isset($args[1])) {
                    $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /xpbank add <amount>");
                }
                if (!is_int((int) $args[1]) || $args[1] <= 0) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Please give a valid amount!");
                    return;
                }
                $amount = (int) $args[1];
                if ($sender->getXpManager()->getCurrentTotalXp() < $amount) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You do not have $amount to put into your XP bank!");
                    return;
                }
                $sender->getXpManager()->addXp(-$amount, false);
                $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
                $user->addXPBank($amount);
                $this->sendMessage($sender, TextFormat::YELLOW . "Added " . number_format($amount) . " XP into your XP bank. Your XP bank balance: " . number_format($user->getXPBank()));
                break;
            case "rm":
                if (!isset($args[1])) {
                    $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /xpbank rm <amount>");
                    return;
                }
                if (!is_int((int) $args[1]) || $args[1] <= 0) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Please give a valid amount!");
                    return;
                }
                $amount = (int) $args[1];
                $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
                if (!$user->hasXPBank($amount)) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You do not have that much XP in your XP bank to withdraw. Your XP bank balance: " . number_format($user->getXPBank()));
                    return;
                }
                if ($sender->getXpManager()->getCurrentTotalXp() + $amount > Limits::INT32_MAX) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot have more than " . number_format(Limits::INT32_MAX) . " XP. Please withdraw less!");
                    return;
                }
                Functions::safeXPAdd($user, $amount);
                $user->subtractXPBank($amount);
                $this->sendMessage($sender, TextFormat::YELLOW . "Withdrew " . number_format($amount) . " XP from your XP bank. Your XP bank balance: " . number_format($user->getXPBank()));
                break;
            case "bank":
                $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
                $this->sendMessage($sender, TextFormat::YELLOW . "Your XP bank balance: " . number_format($user->getXPBank()));
                break;
            case "top":
                if (!isset($args[1])) {
                    $args[1] = 1;
                }
                if (!is_int((int) $args[0]) || $args[0] <= 0) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Enter a valid page number!");
                    return;
                }
                $page = (int) $args[0];
                $array = Main::getInstance()->db->prepare("SELECT COUNT(*) AS COUNT FROM player ORDER BY xpbank DESC;")->execute();
                $array = $array->fetchArray(SQLITE3_ASSOC);
                $total = $array["count"];
                $pages = ceil($total / 8);
                if ($pages < $page) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That page cannot be found. Last page is $pages");
                    return;
                }
                $startNumber = ($page - 1) * 8;
                $this->sendMessage($sender, TextFormat::YELLOW . "Top XP Bank List -");
                $string = TextFormat::YELLOW . "[+]" . TextFormat::WHITE . str_repeat("=", 10) . TextFormat::AQUA . "[ " . $page . "/" . $pages . " ]" . TextFormat::WHITE . str_repeat("=", 10) . TextFormat::YELLOW . "[+]\n";
                $array = Main::getInstance()->db->prepare("SELECT player, xpbank FROM player ORDER BY xpbank DESC LIMIT $startNumber, 8;")->execute();
                $result = $array->fetchArray(SQLITE3_ASSOC);
                while ($result) {
                    $xpbank = $result["xpbank"];
                    $name = $result["player"];
                    $startNumber++;
                    $user = Main::getInstance()->getUserManager()->getOnlineUser($name);
                    if ($user === null) {
                        $string .= TextFormat::WHITE . "$startNumber. $name => " . number_format($xpbank) . " XP \n";
                    } else {
                        $string .= TextFormat::WHITE . "$startNumber. $name => " . number_format($user->getXPBank()) . " XP \n";
                    }
                }
                $this->sendMessage($sender, $string . TextFormat::YELLOW . "[+]" . TextFormat::WHITE . str_repeat("=", 26) . TextFormat::YELLOW . "[+]\n=> Pages will be reloaded after restart! <=");
            default:
                $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /xpbank <add / rm / bank / top>");
        }
    }
}