<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\item\LegacyStringToItemParserException;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\ItemCloud;
use SkyBlock\Main;
use SkyBlock\util\Util;
use SkyBlock\util\Values;

class ItemCloudIC extends BaseCommand {

    /**
     * ItemCloudIC constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'ic', 'ItemCloud Help', 'help', true, ['itemcloud']);
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0])) {
            Main::getInstance()->getFormFunctions()->getItemCloud()->sendItemCloudInv($sender);
            return;
        }
        if ($sender->getWorld()->getDisplayName() === Values::PVP_WORLD) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot use this command here!");
            return;
        }
        switch (strtolower($args[0])) {
            case "dc":
                $name = strtolower($sender->getName());
                if (!isset(Main::getInstance()->clouds[$name])) {
                    Main::getInstance()->clouds[$name] = new ItemCloud($sender->getName(), []);
                    $this->sendMessage($sender, TextFormat::YELLOW . "You have created your ItemCloud. Use /ic help for info!");
                }
                if (!$sender->hasPermission("core.icdc")) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy /ic dc on " . TextFormat::AQUA . "shop.fallentech.io");
                    return;
                }
                if (isset(Main::getInstance()->icdc[$name])) {
                    $this->sendMessage($sender, TextFormat::YELLOW . "ItemCloud download chest disabled!");
                    unset(Main::getInstance()->icdc[$name]);
                    return;
                }
                if (!isset($args[1])) {
                    $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /ic dc <item>");
                    return;
                }
                $item = Util::getItemFromArg($args[1]);
                if ($item === null) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is not a valid item!");
                    return;
                }
                if ($item->getTypeId() === VanillaItems::AIR()->getTypeId()) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot upload nothing!");
                    return;
                }
                $cloud = Main::getInstance()->clouds[$name];
                if ($cloud->getCount($item->getVanillaName()) === 0) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You dont have enough of that item in your account!");
                    return;
                }
                Main::getInstance()->icdc[$name] = $item;
                $this->sendMessage($sender, TextFormat::YELLOW . "ItemCloud download chest for item " . $item->getName() . " has been enabled!\nHit a chest on your island to download items from your ItemCloud into it!");
                break;
            case "uc":
                $name = strtolower($sender->getName());
                if (!isset(Main::getInstance()->clouds[$name])) {
                    Main::getInstance()->clouds[$name] = new ItemCloud($sender->getName(), []);
                    $this->sendMessage($sender, TextFormat::YELLOW . "You have created your ItemCloud. Use /ic help for info!");
                }
                if (!$sender->hasPermission("core.icuc")) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy /ic uc on " . TextFormat::AQUA . "shop.fallentech.io");
                    return;
                }
                if (isset(Main::getInstance()->icuc[$name])) {
                    $this->sendMessage($sender, TextFormat::YELLOW . "ItemCloud upload chest disabled!");
                    unset(Main::getInstance()->icuc[$name]);
                    return;
                }
                if (!isset($args[1])) {
                    $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /ic uc <item>");
                    return;
                }
                $item = Util::getItemFromArg($args[1]);
                if ($item === null) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is not a valid item!");
                    return;
                }
                if ($item->getTypeId() === VanillaItems::AIR()->getTypeId()) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot upload nothing!");
                    return;
                }
                $cloud = Main::getInstance()->clouds[$name];
                if ($cloud->getCount($item->getVanillaName()) === 0 && count($cloud->getItems()) >= 10) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You already have more than 10 items in your ItemCloud. You will need to remove some!");
                    return;
                }
                Main::getInstance()->icuc[$name] = $item;
                $this->sendMessage($sender, TextFormat::YELLOW . "ItemCloud upload chest for item " . $item->getName() . " has been enabled!\nHit a chest on your island to upload items from it to your ItemCloud!");
                break;
            case "upload":
            case "up":
                $name = strtolower($sender->getName());
                if (!isset(Main::getInstance()->clouds[$name])) {
                    Main::getInstance()->clouds[$name] = new ItemCloud($sender->getName(), []);
                    $this->sendMessage($sender, TextFormat::YELLOW . "You have created your ItemCloud. Use /ic help for info!");
                }
                if (!isset($args[1])) {
                    $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /ic up <item> <count/all>");
                    return;
                }
                if (!isset($args[2])) {
                    $args[2] = "all";
                }
                $item = Util::getItemFromArg($args[1]);
                if ($item === null) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is not a valid item!");
                    return;
                }
                if ($item->getTypeId() === VanillaItems::AIR()->getTypeId()) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot upload nothing!");
                    return;
                }
                $amount = $args[2];
                if (!is_int($amount)) {
                    if (strtolower($amount) !== "all") {
                        $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Invalid amount specified!");
                        return;
                    }
                    $amount = 0;
                    foreach ($sender->getInventory()->getContents() as $i) {
                        if ($i->getTypeId() === $item->getTypeId() && $i->getStateId() === $item->getStateId()) {
                            if (!$i->hasEnchantments() && !$i->hasCustomName() && count($i->getLore()) < 2) {
                                $amount += $i->getCount();
                            }
                        }
                    }
                }
                if ($amount < 1) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Invalid item specified!");
                    return;
                }
                $cloud = Main::getInstance()->clouds[$name];
                if ($cloud->getCount($item->getVanillaName()) === 0 && count($cloud->getItems()) >= 10) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You already have more than 10 items in your ItemCloud. You will need to remove some!");
                    return;
                }
                $item->setCount($amount);
                $count = 0;
                foreach ($sender->getInventory()->getContents() as $i) {
                    if ($i->getTypeId() === $item->getTypeId() && $i->getStateId() === $item->getStateId()) {
                        if (!$i->hasEnchantments() && !$i->hasCustomName() && count($i->getLore()) < 2) {
                            $count += $i->getCount();
                        }
                    }
                }
                if ($amount <= $count) {
                    $cloud->addItem($item->getVanillaName(), $amount);
                    $name = $item->getName();
                    $this->sendMessage($sender, TextFormat::YELLOW . "Uploaded x" . $item->getCount() . " $name to your ItemCloud!");
                } else {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You dont have enough items to upload!");
                }
                break;
            case "transfer":
            case "tr":
                $name = strtolower($sender->getName());
                if (!isset(Main::getInstance()->clouds[$name])) {
                    Main::getInstance()->clouds[$name] = new ItemCloud($sender->getName(), []);
                    $this->sendMessage($sender, TextFormat::YELLOW . "You have created your ItemCloud. Use /ic help for info!");
                }
                if (!isset($args[2])) {
                    $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /tc tr <player> <item> <count/all>");
                    return;
                }
                $playerName = strtolower($args[1]);
                if ($name === $playerName) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot transfer items to your own account!");
                    return;
                }
                if (!isset(Main::getInstance()->clouds[$playerName])) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player does not have an ItemCloud setup!");
                    return;
                }
                $player = Server::getInstance()->getPlayerExact($playerName);
                if (!$player instanceof Player) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
                    return;
                }
                $max = 20;
                $left = Main::getInstance()->ic_tr_timer[$sender->getName()] - time();
                if (isset(Main::getInstance()->ic_tr_timer[$sender->getName()]) && $left > 0) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Please wait $left seconds to use this command again!");
                    return;
                }
                if (!isset($args[3])) {
                    $args[3] = "all";
                }
                $item = Util::getItemFromArg($args[2]);
                if ($item === null) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is not a valid item!");
                    return;
                }
                if ($item->getTypeId() === VanillaItems::AIR()->getTypeId()) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot upload nothing!");
                    return;
                }
                $cloud = Main::getInstance()->clouds[$name];
                $transferCloud = Main::getInstance()->clouds[$playerName];
                $amount = $args[3];
                if (!is_int($amount)) {
                    if (strtolower($amount) !== "all") {
                        $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Invalid amount specified!");
                        return;
                    }
                    $amount = $cloud->getCount($item->getVanillaName());
                    if ($amount === 0) {
                        $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You do not have enough items in your ItemCloud!");
                        return;
                    }
                }
                if ($amount < 1) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Invalid amount specified!");
                    return;
                }
                if (!$cloud->itemExists($item->getVanillaName(), $amount)) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You do not have enough items in your ItemCloud!");
                    return;
                }
                if ($transferCloud->getCount($item->getVanillaName()) === 0 && count($transferCloud->getItems()) >= 10) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You already have more than 10 items in your ItemCloud. You will need to remove some!");
                    return;
                }
                $item->setCount($amount);
                $transferCloud->addItem($item->getVanillaName(), $item->getCount(), false);
                $cloud->removeItem($item->getVanillaName(), $item->getCount());
                Main::getInstance()->ic_tr_timer[$sender->getName()] = time() + $max;
                $this->sendMessage($player, TextFormat::YELLOW . "Received x" . $item->getCount() . " " . $item->getName() . " from " . $sender->getName() . "'s ItemCloud!");
                $this->sendMessage($sender, TextFormat::YELLOW . "Sent x" . $item->getCount() . " " . $item->getName() . " to $playerName's ItemCloud!");
                break;
            case "download":
            case "down":
                $name = strtolower($sender->getName());
                if (!isset(Main::getInstance()->clouds[$name])) {
                    Main::getInstance()->clouds[$name] = new ItemCloud($sender->getName(), []);
                    $this->sendMessage($sender, TextFormat::YELLOW . "You have created your ItemCloud. Use /ic help for info!");
                }
                if (!isset($args[1])) {
                    $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /tc down <item> <count/all>");
                    return;
                }
                if (!isset($args[2])) {
                    $args[2] = "all";
                }
                $item = Util::getItemFromArg($args[1]);
                if ($item === null) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is not a valid item!");
                    return;
                }
                if ($item->getTypeId() === VanillaItems::AIR()->getTypeId()) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot upload nothing!");
                    return;
                }
                $cloud = Main::getInstance()->clouds[$name];
                $amount = $args[2];
                if (!is_int($amount)) {
                    if (strtolower($amount) !== "all") {
                        $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Invalid amount specified!");
                        return;
                    }
                    $amount = $cloud->getCount($item->getVanillaName());
                    if ($amount === 0) {
                        $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You do not have enough items in your ItemCloud!");
                        return;
                    }
                }
                if ($amount < 1) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Invalid amount specified!");
                    return;
                }
                if (!$cloud->itemExists($item->getVanillaName(), $amount)) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You do not have enough items in your ItemCloud!");
                    return;
                }
                $slots = Util::getSlotsForItem($sender->getInventory(), $item);
                if ($slots <= 0) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Your inventory is full!");
                }
                $changed = "You have downloaded x$amount " . $item->getName();
                if ($amount > $slots) {
                    $changed = "You dont have enough space in your inventory to download x$amount " . $item->getName() . ". Downloaded for x$slots instead!";
                    $amount = $slots;
                }
                $item->setCount($amount);
                $cloud->removeItem($item->getVanillaName(), $item->getCount());
                $sender->getInventory()->addItem($item);
                $this->sendMessage($sender, TextFormat::YELLOW . $changed);
                break;
            case "lock":
            case "unlock":
                $name = strtolower($sender->getName());
                if (!isset(Main::getInstance()->clouds[$name])) {
                    Main::getInstance()->clouds[$name] = new ItemCloud($sender->getName(), []);
                    $this->sendMessage($sender, TextFormat::YELLOW . "You have created your ItemCloud. Use /ic help for info!");
                }
                $cloud = Main::getInstance()->clouds[$name];
                if ($cloud->isLock()) {
                    $cloud->setLock(false);
                    $this->sendMessage($sender, TextFormat::YELLOW . "You have unlocked your ItemCloud!");
                } else {
                    $cloud->setLock(true);
                    $this->sendMessage($sender, TextFormat::YELLOW . "You have locked your ItemCloud. Players can no longer buy from your pshops!");
                }
                break;
            case "list":
                $name = strtolower($sender->getName());
                if (!isset(Main::getInstance()->clouds[$name])) {
                    Main::getInstance()->clouds[$name] = new ItemCloud($sender->getName(), []);
                    $this->sendMessage($sender, TextFormat::YELLOW . "You have created your ItemCloud. Use /ic help for info!");
                }
                $cloud = Main::getInstance()->clouds[$name];
                $output = "Item List: \n";
                $i = 0;
                foreach ($cloud->getItems() as $item => $count) {
                    $namespace = explode(":", $item)[0];
                    try {
                        $item = StringToItemParser::getInstance()->parse($namespace);
                    } catch (LegacyStringToItemParserException) {
                        $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is not a valid item!");
                        return;
                    }
                    $name = $item->getName();
                    $name = str_replace("minecraft:", "", $name);
                    $name = str_replace("_", " ", $name);
                    $name = ucwords($name);
                    $output .= TextFormat::WHITE . $i++ . TextFormat::YELLOW . ". $name: " . number_format($count) . "\n";
                }
                $sender->sendMessage($output);
                break;
            case "count":
                $name = strtolower($sender->getName());
                if (!isset(Main::getInstance()->clouds[$name])) {
                    Main::getInstance()->clouds[$name] = new ItemCloud($sender->getName(), []);
                    $this->sendMessage($sender, TextFormat::YELLOW . "You have created your ItemCloud. Use /ic help for info!");
                }
                if (!isset($args[1])) {
                    $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /ic count <item>");
                    return;
                }
                $item = Util::getItemFromArg($args[1]);
                if ($item === null) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is not a valid item!");
                    return;
                }
                if ($item->getTypeId() === VanillaItems::AIR()->getTypeId()) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot upload nothing!");
                    return;
                }
                $cloud = Main::getInstance()->clouds[$name];
                $count = $cloud->getCount($item->getVanillaName());
                if ($count === 0) {
                    $this->sendMessage($sender, TextFormat::YELLOW . "You do not have " . $item->getName() . " in your account!");
                    return;
                } else {
                    $this->sendMessage($sender, TextFormat::YELLOW . "You have " . number_format($count) . " " . $item->getCount() . "!");
                }
                break;
            case "help":
                $this->sendMessage($sender, TextFormat::YELLOW .
                                          "/ic up - Upload items from your inventory to your ItemCloud\n
					/ic down - Download items from your ItemCloud to your inventory\n
					/ic count - Check the amount of a specified item is on your ItemCloud\n
					/ic list - List all the items in your ItemCloud
					/ic uc - Upload items from chests to your ItemCloud (Exclusive Command)\n
					/ic dc - Download items from your ItemCloud to chests (Exclusive Command)\n
					/ic tr - Transfer items from your ItemCloud to anothers ItemCloud\n
					/is lock - Lock your ItemCloud"
                );
                break;
            default:
                $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /ic help");
                break;
        }
    }

}
