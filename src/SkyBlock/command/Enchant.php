<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Tool;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\Data;
use SkyBlock\enchants\BaseEnchantment;
use SkyBlock\Main;
use SkyBlock\util\Values;

class Enchant extends BaseCommand {

    public static array $enchants = [];

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'enchant', 'Enchant Item in hand', '[player] <enchantID> <level> or /enchant <accept | deny>');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /enchant <player> <enchantId> <level> or /enchant <accept / deny>");
            return;
        }
        if (strtolower($args[0]) === "accept" && !isset($args[1])) {
            if (!$sender instanceof Player) {
                $this->sendMessage($sender, self::NO_CONSOLE);
                return;
            }
            if (!isset(self::$enchants[strtolower($sender->getName())])) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You have not received any enchant requests!");
                return;
            }
            $data = self::$enchants[strtolower($sender->getName())];
            if (time() - $data["time"] > 60) {
                unset(self::$enchants[strtolower($sender->getName())]);
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " The enchant request has expired!");
                return;
            }
            $item = $sender->getInventory()->getItemInHand();
            $user = Main::getInstance()->getUserManager()->getOnlineUser($data["sender"]);
            if ($user === null) {
                unset(self::$enchants[strtolower($sender->getName())]);
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " The requester is no longer online!");
                return;
            }
            $cost = $data["cost"];
            if (!$user->hasMoney($cost)) {
                unset(self::$enchants[strtolower($sender->getName())]);
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " The requester doesnt have $cost to enchant you!");
                return;
            }
            if ($sender->getWorld()->getDisplayName() === Values::PVP_WORLD) {
                unset(self::$enchants[strtolower($sender->getName())]);
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You are in the PvP world!");
                return;
            }
            if (Main::getInstance()->isInCombat($sender)) {
                unset(self::$enchants[strtolower($sender->getName())]);
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You are in combat!");
                return;
            }
            if (!$item instanceof Tool && !$item instanceof Armor) {
                unset(self::$enchants[strtolower($sender->getName())]);
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Hold an armor or tool to enchant!");
                return;
            }
            $enchantment = $data["enchant"];
            if (!$enchantment instanceof EnchantmentInstance) {
                return;
            }
            $level = $data["level"];
            $enchantId = BaseEnchantment::getEnchantmentId($enchantment);
            if (BaseEnchantment::hasEnchantment($item, $enchantId)) {
                $level2 = BaseEnchantment::getEnchantmentLevel($item, $enchantId);
                if ($level2 >= $level) {
                    unset(self::$enchants[strtolower($sender->getName())]);
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That item already has that enchantment with the same or higher level!");
                    return;
                }
            }
            if (Main::getInstance()->getFunctions()->countEnchants($item) >= 4) {
                unset(self::$enchants[strtolower($sender->getName())]);
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot put more than 4 vanilla enchantments on an item!");
                return;
            }
            $user->removeMoney($cost);
            $item->addEnchantment(new EnchantmentInstance($enchantment->getType(), $level));
            $sender->getInventory()->setItemInHand($item);
            $this->sendMessage($user->getPlayer(), TextFormat::YELLOW . "Enchanting " . Main::getInstance()->getFunctions()->numberToEnchantment($enchantId) . " succeeded on " . $sender->getName() . "'s held item for $" . Data::$commandEnchantCost . " x$level Levels = $$cost");
            $this->sendMessage($sender, TextFormat::YELLOW . "Enchanted " . Main::getInstance()->getFunctions()->numberToEnchantment($enchantId) . " of level $level on your held item for $cost by " . $data['sender']);
            unset(self::$enchants[strtolower($sender->getName())]);
            return;
        }
        if (strtolower($args[0]) === "deny" && !isset($args[1])) {
            if ($sender instanceof Player) {
                $this->sendMessage($sender, self::NO_CONSOLE);
                return;
            }
            if (!isset(self::$enchants[strtolower($sender->getName())])) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You have not received any enchantment requests!");
                return;
            }
            $data = self::$enchants[strtolower($sender->getName())];
            $player = Server::getInstance()->getPlayerByPrefix($data["sender"]);
            if ($player instanceof Player) {
                $this->sendMessage($sender, TextFormat::YELLOW . "Enchant request was denied by " . $sender->getName());
            }
            $this->sendMessage($sender, TextFormat::YELLOW . "Enchant request by " . $data["sender"] . " denied!");
            unset(self::$enchants[strtolower($sender->getName())]);
            return;
        }
        if (!$sender->hasPermission("core.enchant") && !$sender->hasPermission("enchant.me")) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy SkyGOD rank on " . TextFormat::AQUA . "shop.fallentech.io");
            return;
        }
        if (!isset($args[0]) || !isset($args[1]) || !isset($args[2])) {
            $this->sendMessage($sender, $commandLabel);
            return;
        }
        $player = strtolower($args[0]);
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
        if ($user === null) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
            return;
        }
        if ($sender->getName() !== $user->getName()) {
            if (isset(self::$enchants[$player])) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player already has a pending enchant request!");
                return;
            }
        }
        if (!Main::getInstance()->isTrusted($user->getPlayer()->getName())) {
            if (Main::getInstance()->isInCombat($user->getPlayer())) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is in combat!");
                return;
            }
            if ($user->getPlayer()->getWorld()->getDisplayName() === Values::PVP_WORLD) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is in the PvP world!");
                return;
            }
        }
        $item = $user->getPlayer()->getInventory()->getItemInHand();
        if (!$item instanceof Tool && !$item instanceof Armor) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . "That player is not holding a tool or armor to enchant!");
            return;
        }
        if (Main::getInstance()->getFunctions()->countEnchants($item) >= 4) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . "You cannot enchant more than 4 vanilla enchants on an item!");
            return;
        }
        if (Main::getInstance()->isTrusted($sender->getName()) && $sender->getName() === $user->getName()) {
            $enchant = BaseEnchantment::parse($args[1]);
            $enchantId = Main::getInstance()->getEnchantFactory()->getIdByEnchantName($args[1]);
            if ($enchant === null && $enchantId !== null) {
                $enchant = BaseEnchantment::getEnchantment($enchantId);
            }
            if (!$enchant instanceof EnchantmentInstance) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . "Enchantment not found. Use /enchantids to see available enchants!");
                return;
            }
            if (intval($args[2]) === 0) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . "$args[2] is not a valid number!");
                return;
            }
            $args[2] = intval($args[2]);
            /** @var Player $sender */
            $item = $sender->getInventory()->getItemInHand();
            $item->addEnchantment(new EnchantmentInstance($enchant->getType(), $args[2]));
            $item = Main::getInstance()->getFunctions()->setEnchantmentNames($item, false);
            $sender->getInventory()->setItemInHand($item);
            return;
        }
        $level = (int) $args[2];
        if ($level < 1 || $level > 6) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is not a valid enchant level. You can only enchant levels 1-6!");
            return;
        }
        $enchantment = BaseEnchantment::parse($args[1]);
        if (!$enchantment instanceof EnchantmentInstance) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Enchantment not found. Use /enchantids to see available enchants!");
            return;
        }
        $enchantId = BaseEnchantment::getEnchantmentId($enchantment);
        if ($enchantId > EnchantmentIds::INFINITY || $enchantId === EnchantmentIds::THORNS) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Enchantment not found. Use /enchantids to see available enchants!");
            return;
        }
        if (BaseEnchantment::hasEnchantment($item, $enchantId)) {
            $level2 = BaseEnchantment::getEnchantmentLevel($item, $enchantId);
            if ($level2 >= $level) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Item already has that enchantment with the same level or higher!");
                return;
            }
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
        $money = $level * Data::$commandEnchantCost;
        if (!$user->hasMoney($money)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You do not have the amount of money needed. You need $money which is " . Data::$commandEnchantCost . " per enchant level!");
            return;
        }
        if ($sender->getName() === $user->getName()) {
            $user->removeMoney($money);
            $item->addEnchantment(new EnchantmentInstance($enchantment->getType(), $level));
            $user->getPlayer()->getInventory()->setItemInHand($item);
            $this->sendMessage($sender, TextFormat::YELLOW . "Enchanting succeeded for $" . Data::$commandEnchantCost . " x$level Levels = $$money!");
            $this->sendMessage($sender, TextFormat::YELLOW . "Enchanted your item in hand!");
        } else {
            $this->sendMessage($user->getPlayer(), TextFormat::YELLOW . " wants to enchant " . Main::getInstance()->getFunctions()->numberToEnchantment($enchantId) . " of level $level to your held item\nType /enchant accept or /enchant deny to accept/deny it!");
            $this->sendMessage($sender, TextFormat::YELLOW . Main::getInstance()->getFunctions()->numberToEnchantment($enchantId) . " Enchant request sent to " . $user->getName() . " they have a minute to respond!");
            self::$enchants[strtolower($user->getName())] = ["sender" => $sender->getName(), "enchant" => $enchantment, "level" => $level, "cost" => $money, "time" => time()];
        }
    }
}