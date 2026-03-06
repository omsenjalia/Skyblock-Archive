<?php


namespace SkyBlock\command\ce;


use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Bow;
use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\item\ToolTier;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use SkyBlock\command\BaseCommand;
use SkyBlock\Data;
use SkyBlock\enchants\BaseEnchantment;
use SkyBlock\Main;
use SkyBlock\util\Constants;

class
Removece extends BaseCommand {
    /**
     * Removece constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'removece', 'Remove CE from tool and get it as CE book', '<ce name>');
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
        if (!$sender->hasPermission('core.removece') and !$sender->hasPermission('remove.ce')) {
            $this->sendMessage($sender, "§c> You dont have permission to use this command! §aBuy SkyELITE Rank from shop.fallentech.io now!");
            return;
        }
        $name = ucfirst(strtolower($args[0]));
        $inventory = $sender->getInventory();
        $hand = $inventory->getItemInHand();
        $item = $hand;
        if ($this->func->countEnchants($hand, "ce") <= 0) {
            $this->sendMessage($sender, "§4[Error] §cThe item you're holding doesn't have any custom enchantments on it to remove!");
            return;
        }
        if ($hand->getTypeId() == 0) {
            $this->sendMessage($sender, "§4[Error] §cYou are holding nothing!");
            return;
        }
        if (!$hand instanceof \SkyBlock\item\TieredTool) {
            if (!$hand instanceof Armor && !$hand instanceof TieredTool && !$hand instanceof Bow) {
                $this->sendMessage($sender, "§4[Error] §cOnly tools and armors can be used!");
                return;
            }
            if ($hand instanceof TieredTool and $hand->getTier() < ToolTier::IRON()) {
                $this->sendMessage($sender, "§4[Error] §cOnly Iron or Diamond tools can be used!");
                return;
            }
        }
        if ($hand instanceof Armor && $hand->getMaxDurability() < Constants::ARMOR_TIER_CHAIN_MAX_DURABILITY) {
            $this->sendMessage($sender, "§4[Error] §cOnly Chain/Iron/Diamond armors can be used!");
            return;
        }
        if ($hand->getCount() !== 1) {
            $this->sendMessage($sender, "§4[Error] §cYou are holding more than one item!");
            return;
        }
        if (($id = $this->func->getEnchantmentId($name)) !== null) {
            if ($id < 22) {
                $this->sendMessage($sender, "§4[Error] §cThat CE wasn't found on held item! Type the name correctly!");
                return;
            }
            if (!BaseEnchantment::hasEnchantment($hand, $id)) {
                $this->sendMessage($sender, "§4[Error] §cThat CE wasn't found on held item! Type the name correctly!");
                return;
            }
        } else {
            $this->sendMessage($sender, "§4[Error] §cThat CE specified was not found! Please enter correct ce name!");
            return;
        }
        if ($this->func->isInventoryFull($sender)) {
            $this->sendMessage($sender, "§4[Error] §cYour Inventory is full! Empty a slot to claim the book!");
            return;
        }
        $user = $this->um->getOnlineUser($sender->getName());
        $cost = Data::$commandRemoveCECost;
        if (!$user->hasMoney($cost)) {
            $this->sendMessage($sender, "§4[Error]§c You don't have §6$cost$ §cto remove CE!");
            return;
        }
        $level = BaseEnchantment::getEnchantmentLevel($hand, $id);
        $ench = $hand->getNamedTag()->getListTag(Item::TAG_ENCH);
        if ($ench !== null && $ench->getTagType() === NBT::TAG_Compound) {
            foreach ($ench as $k => $entry) {
                /** @var CompoundTag $entry */
                if ($entry->getShort("id") === $id) {
                    if ($level === null or $entry->getShort("lvl") === $level) {
                        $ench->remove($k);
                        break;
                    }
                }
            }
        }
        $hand->setNamedTag($hand->getNamedTag()->setTag(Item::TAG_ENCH, $ench));
        if (!$item->hasCustomName()) {
            $hand = $this->func->setEnchantmentNames($hand, false);
        }
        if ($item->hasCustomName()) {
            $cn = $item->getCustomName();
            $cname = explode("\n", $cn);
            $hand = $this->func->setEnchantmentNames($hand, $cname[0]);
        }
        $inventory->setItemInHand($hand);
        $ite = VanillaItems::ENCHANTED_BOOK();
        $data = $this->func->getEnchantmentData($name);
        if ($data === null) return;
        $name = $data[0];
        $rarity = $data[3];
        $type = $data[4];
        $slot = $sender->getInventory()->firstEmpty();
        $chance = mt_rand(1, 75);
        $user->removeMoney($cost);
        $sender->getInventory()->setItem($slot, $ite->setCustomName(TF::RESET . " §l{$this->func->getColorForEnchant($rarity)}$name \n §r{$this->func->getColorForType($type)}$type Custom Enchant \n §a$chance%% §fAccuracy \n §bUse /combiner to merge this enchant!"));
        $this->sendMessage($sender, "§eSuccessfully removed CE for free, Added $name §aCustom Enchanted Book in inventory!\n§bUse /combiner to merge it with a $type!");
    }
}