<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\Data;
use SkyBlock\enchants\BaseEnchantment;
use SkyBlock\Main;
use SkyBlock\util\Util;

class Brag extends BaseCommand {
    public const COOLDOWN = 20;
    public static array $brag = [];

    /**
     * Brag constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'brag', 'Brag about help item to a player', '[player]', true, ['showcase']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /brag <player>");
            return;
        }
        $playerName = strtolower($args[0]);
        $player = Server::getInstance()->getPlayerByPrefix($playerName);
        if (!$player instanceof Player) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
            return;
        }
        if (strtolower($player->getName()) === strtolower($sender->getName())) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot brag an item to yourself!");
            return;
        }
        $item = $sender->getInventory()->getItemInHand();
        if ($item->getTypeId() === VanillaItems::AIR()->getTypeId()) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You are not holding any item to brag!");
            return;
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
        if (!$user->removeMoney(Data::$commandBragCost)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You need $" . Data::$commandBragCost . " to brag your item!");
            return;
        }
        if (isset(self::$brag[$sender->getName()]) && !Main::getInstance()->hasOp($sender)) {
            $left = self::$brag[$sender->getName()] - time();
            if ($left > 0) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Please wait $left seconds to use this command again!");
                return;
            }
        }
        self::$brag[$sender->getName()] = time() + self::COOLDOWN;
        $str = "§e➼ §a{$sender->getName()} §ejust bragged to you about their §7x§c{$item->getCount()} §b" . Util::getNameOfItem($item, $item->getVanillaName()) . " " . Util::getLoreString($item->getLore()) . "\n";
        if ($item->hasEnchantments()) {
            $str .= "§3➼ Enchantments: ";
            foreach ($item->getEnchantments() as $enchantment) {
                $ench = BaseEnchantment::getEnchantmentId($enchantment);
                $level = $enchantment->getLevel();
                if ($ench < 25) {
                    $name = Main::getInstance()->getFunctions()->numberToEnchantment($ench);
                    $str .= "§e" . $name;
                } elseif ($ench > 99 && $ench < 175) {
                    $name = BaseEnchantment::getEnchantment($ench)->getName();
                    $str .= "§a" . $name;
                } elseif ($ench >= 175) {
                    $name = BaseEnchantment::getEnchantment($ench)->getName();
                    $str .= "§b" . $name;
                }
                $str .= " §f" . $level . ", ";
            }
            $str = substr($str, 0, -2);
        }
        $this->sendMessage($sender, $str);
        $this->sendMessage($sender, TextFormat::YELLOW . "You have bragged your item to " . $player->getName() . " for $" . Data::$commandBragCost);
    }
}