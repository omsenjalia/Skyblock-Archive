<?php


namespace SkyBlock\command\ce;


use alvin0319\CustomItemLoader\CustomItems;
use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Bow;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\TieredTool;
use pocketmine\item\ToolTier;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use SkyBlock\command\BaseCommand;
use SkyBlock\Data;
use SkyBlock\enchants\BaseEnchantment;
use SkyBlock\item\NAxe;
use SkyBlock\item\NPickaxe;
use SkyBlock\item\NShovel;
use SkyBlock\Main;
use SkyBlock\util\Constants;

class Carver extends BaseCommand {

    /**
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'carver', 'Merge carver scroll with tool', '<enchantment name>');
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!isset($args[0]) or !$sender instanceof Player) {
            $this->sendUsage($sender, $commandLabel);
            return;
        }
        $inventory = $sender->getInventory();
        $hand = $inventory->getItemInHand();
        if ($this->func->countEnchants($hand) <= 0) {
            $this->sendMessage($sender, "§4[Error] §cThe item you're holding doesn't have any vanilla enchantments on it to remove!");
            return;
        }
        if ($hand->getTypeId() === 0) {
            $this->sendMessage($sender, "§4[Error] §cYou are holding nothing!");
            return;
        }
        if ($hand->getCount() !== 1) {
            $this->sendMessage($sender, "§4[Error] §cYou are holding invalid item!");
            return;
        }
        if (!$hand instanceof Armor && !$hand instanceof TieredTool && !$hand instanceof Bow) {
            $this->sendMessage($sender, "§4[Error] §cOnly tools and armors can be used!");
            return;
        }
        if (($hand instanceof TieredTool and $hand->getTier() < ToolTier::IRON()) and !$hand instanceof NPickaxe and !$hand instanceof NAxe and !$hand instanceof NShovel) {
            $this->sendMessage($sender, "§4[Error] §cOnly Iron, Diamond or Netherite tools can be used!");
            return;
        }
        if ($hand instanceof Armor && $hand->getMaxDurability() < Constants::ARMOR_TIER_CHAIN_MAX_DURABILITY) {
            $this->sendMessage($sender, "§4[Error] §cOnly Chain/Iron/Diamond armors can be used!");
            return;
        }
        if (!($enchantment = BaseEnchantment::parse($args[0])) instanceof EnchantmentInstance) {
            $this->sendMessage($sender, "§4[Error]§c Enchantment not found! Use /enchantids and see available enchants!");
            return;
        }
        $eid = BaseEnchantment::getEnchantmentId($enchantment);
        if ($eid < 0 || $eid > 22) {
            $this->sendMessage($sender, "§4[Error]§c Vanilla Enchantment not found! Use /enchantids and see available enchants!");
            return;
        }
        if ($this->func->isInventoryFull($sender)) {
            $this->sendMessage($sender, "§4[Error] §cYour Inventory is full! Empty a slot to claim the orb!");
            return;
        }
        $name = ucwords(strtolower($this->func->numberToEnchantment($eid)));
        $n = str_replace(' ', '', $name);
        $level = BaseEnchantment::getEnchantmentLevel($hand, $eid);
        if ($level <= 0) {
            $this->sendMessage($sender, "§4[Error]§c Enchantment not found on held item!");
            return;
        }
        if ($this->func->isInventoryFull($sender)) {
            $this->sendMessage($sender, "§4[Error] §cYour Inventory is full! Empty a slot to claim the book!");
            return;
        }
        $items = $sender->getInventory()->getContents();
        $flag = false;
        $str = "§c> §eStarting Scroll finder...!\n";
        foreach ($items as $slot => $item) {
            if ($item->getTypeId() == ItemTypeIds::PRISMARINE_SHARD) {
                if ($item->hasCustomName()) {
                    $spaces = explode(" ", $item->getCustomName());
                    $type = $spaces[1];
                    $type2 = $spaces[2];
                    if ($type == 'Carver' and $type2 == 'Scroll') {
                        $flag = true;
                        $str .= "§c> §eCarver scroll detected...\n";
                        $str .= "§c> §eCombining scroll with tool...\n";
                        $user = $this->um->getOnlineUser($sender->getName());
                        if ($user->removeMoney(Data::$commandCarverCost)) {
                            $ench = $hand->getNamedTag()->getListTag(Item::TAG_ENCH);
                            if ($ench !== null && $ench->getTagType() === NBT::TAG_Compound) {
                                foreach ($ench as $k => $entry) {
                                    /** @var CompoundTag $entry */
                                    if ($entry->getShort("id") === $eid) {
                                        if ($level === null or $entry->getShort("lvl") === $level) {
                                            $ench->remove($k);
                                            break;
                                        }
                                    }
                                }
                            }
                            $hand->setNamedTag($hand->getNamedTag()->setTag(Item::TAG_ENCH, $ench));
                            $item1 = $hand;
                            if (!$item1->hasCustomName()) {
                                $hand = $this->func->setEnchantmentNames($hand, false);
                            }
                            if ($item1->hasCustomName()) {
                                $cn = $item1->getCustomName();
                                $cname = explode("\n", $cn);
                                $hand = $this->func->setEnchantmentNames($hand, $cname[0]);
                            }
                            $inventory->setItemInHand($hand);
                            $ite = CustomItems::ENDER_EYE();
                            $slot1 = $sender->getInventory()->firstEmpty();
                            $ite->setCustomName(TF::RESET . TF::BOLD . " §6$n §r§9Enchantment Orb \n §aLevel: §6$level \n §3ID: §6$eid \n §eUse this on a tool or armor by /ench ");
                            $sender->getInventory()->setItem($slot1, $ite);
                            $item->setCount($item->getCount() - 1);
                            $sender->getInventory()->setItem($slot, $item);
                            $str .= "§c> §eScroll merged with tool for 10,000$!\n";
                        } else {
                            $str .= "§c> §eCouldn't remove the Vanilla enchant, you don't have 10,000$...\n";
                        }
                        break;
                    }
                }
            }
        }
        if ($flag) {
            $sender->sendMessage($str . "§c> Stopping Scroll finder...!");
        } else {
            $sender->sendMessage($str . "§c> No valid Scrolls were found! Get scrolls only from /vote, /manashop or gkits\n§c> Stopping Scroll finder...!");
        }
    }

}