<?php


namespace SkyBlock\command;


use alvin0319\CustomItemLoader\CustomItems;
use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Bow;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\ToolTier;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;
use SkyBlock\Data;
use SkyBlock\enchants\BaseEnchantment;
use SkyBlock\Main;
use SkyBlock\util\Constants;

class RemoveVanillaEnch extends BaseCommand {
    /**
     * RemoveVanillaEnch constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'removevanillaench', 'Remove Vanilla Enchant from tool', '<enchantment name>', true, ['removeve']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /removeve <enchantment>");
            return;
        }
        if (!$sender->hasPermission("core.removeve")) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy SkyHULK rank on " . TextFormat::AQUA . "shop.fallentech.io");
            return;
        }
        $inventory = $sender->getInventory();
        $hand = $inventory->getItemInHand();
        $item = $hand;
        if (Main::getInstance()->getFunctions()->countEnchants($hand) <= 0) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " The item you are holding does not have any vanilla enchantments to remove!");
            return;
        }
        if ($hand->getTypeId() === VanillaItems::AIR()->getTypeId()) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You are not holding an item!");
            return;
        }
        if (!$hand instanceof Armor && !$hand instanceof TieredTool && !$hand instanceof Bow) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can only remove enchants from tools, armor, and bows!");
            return;
        }
        if ($hand instanceof TieredTool && $hand->getTypeId() < ToolTier::IRON()) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can only remove enchants from tools iron or better!");
            return;
        }
        if ($hand instanceof Armor && $hand->getMaxDurability() < Constants::ARMOR_TIER_CHAIN_MAX_DURABILITY) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can only remove enchants from armor iron or better!");
            return;
        }
        if ($hand->getCount() !== 1) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is an invalid item!");
            return;
        }
        $enchantment = BaseEnchantment::parse($args[0]);
        if (!$enchantment instanceof EnchantmentInstance) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That enchantment was not found. Use /enchantids to see available enchantments!");
            return;
        }
        $enchantId = BaseEnchantment::getEnchantmentId($enchantment);
        if ($enchantId < 0 || $enchantId > 22) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That enchantment was not found. Use /enchantids to see available enchantments!");
            return;
        }
        if (Main::getInstance()->getFunctions()->isInventoryFull($sender)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Your inventory is full. Empty a slot and run this command again!");
            return;
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
        $cost = Data::$commandRemoveVECost;
        if (!$user->hasMoney($cost)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You dont have $cost to remove an enchantment!");
            return;
        }
        $name = ucwords(strtolower(Main::getInstance()->getFunctions()->numberToEnchantment($enchantId)));
        $n = str_replace(" ", "", $name);
        $level = BaseEnchantment::getEnchantmentLevel($hand, $enchantId);
        if ($level <= 0) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That enchantment was not found. Use /enchantids to see available enchantments!");
            return;
        }
        $ench = $hand->getNamedTag()->getListTag(Item::TAG_ENCH);
        if ($ench !== null && $ench->getTagType() === NBT::TAG_Compound) {
            foreach ($ench as $k => $entry) {
                /** @var CompoundTag $entry */
                if ($entry->getShort("id") === $enchantId) {
                    if ($entry->getShort("lvl") === $level) {
                        $ench->remove($k);
                        break;
                    }
                }
            }
        }
        $hand->setNamedTag($hand->getNamedTag()->setTag(Item::TAG_ENCH, $ench));
        if (!$item->hasCustomName()) {
            $hand = Main::getInstance()->getFunctions()->setEnchantmentNames($hand, false);
        }
        if ($item->hasCustomName()) {
            $cn = $item->getCustomName();
            $cname = explode("\n", $cn);
            $hand = Main::getInstance()->getFunctions()->setEnchantmentNames($hand, $cname[0]);
        }
        $inventory->setItemInHand($hand);
        $ite = CustomItems::ENDER_EYE();
        $slot = $sender->getInventory()->firstEmpty();
        $ite->setCustomName(TF::RESET . TF::BOLD . " §6$n §r§9Enchantment Orb \n §aLevel: §6$level \n §3ID: §6$enchantId \n §eUse this on a tool or armor by /ench ");
        $user->removeMobCoin($cost);
        $sender->getInventory()->setItem($slot, $ite);
        $this->sendMessage($sender, TextFormat::YELLOW . "Removed vanilla enchant for " . Data::$commandRemoveVECost . ". Added $name vanilla enchant orb to your inventory. Use /ench to merge it with an item!");
    }
}