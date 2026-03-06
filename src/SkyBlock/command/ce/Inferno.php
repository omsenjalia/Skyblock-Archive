<?php


namespace SkyBlock\command\ce;


use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Bow;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\TieredTool;
use pocketmine\item\ToolTier;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Data;
use SkyBlock\enchants\BaseEnchantment;
use SkyBlock\item\NAxe;
use SkyBlock\item\NPickaxe;
use SkyBlock\item\NShovel;
use SkyBlock\Main;
use SkyBlock\util\Constants;

class Inferno extends BaseCommand {

    /**
     * Inferno constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'inferno', 'Merge inferno scroll with tool', '<vanilla enchant>');
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
        $hand = $sender->getInventory()->getItemInHand();
        $ename = strtolower($args[0]);
        if ($this->func->countEnchants($hand, "vanilla") <= 0) {
            $this->sendMessage($sender, "§4[Error] §cThe item you're holding doesn't have any vanilla enchantments on it to level up!");
            return;
        }
        if ($hand->getTypeId() === 0) {
            $this->sendMessage($sender, "§4[Error] §cYou are holding nothing!");
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
        if ($hand->getCount() != 1) {
            $this->sendMessage($sender, "§4[Error] §cYou are holding invalid item!");
            return;
        }
        if (($enchantment = BaseEnchantment::parse($args[0])) === null) {
            $this->sendMessage($sender, "§4[Error] §cThat Vanilla enchant wasn't found on held item! Type the name correctly!");
            return;
        }
        $id = BaseEnchantment::getEnchantmentId($enchantment);
        if ($id > 22) {
            $this->sendMessage($sender, "§4[Error] §cThats not a Vanilla enchant!");
            return;
        }
        if (!BaseEnchantment::hasEnchantment($hand, $id)) {
            $this->sendMessage($sender, "§4[Error] §cThat Vanilla enchant wasn't found on held item! Type the name correctly!");
            return;
        }
        if (($elevel = BaseEnchantment::getEnchantmentLevel($hand, $id)) >= 10) {
            $this->sendMessage($sender, "§4[Error] §cThat Vanilla enchant specified already has the max level 10!");
            return;
        }
        $items = $sender->getInventory()->getContents();
        $flag = false;
        $str = "§c> §eStarting Scroll finder...!\n";
        foreach ($items as $slot => $item) {
            if ($item->getTypeId() === ItemTypeIds::PRISMARINE_SHARD) {
                if ($item->hasCustomName()) {
                    $spaces = explode(" ", $item->getCustomName());
                    $type = $spaces[1];
                    $type2 = $spaces[2];
                    $level = mt_rand(1, 3);
                    if ($type == 'Inferno' and $type2 == 'Scroll') {
                        $flag = true;
                        $str .= "§c> §e$ename of level $elevel detected on held item ...\n";
                        $str .= "§c> §aInferno §escroll detected...\n";
                        $newlevel = $level + $elevel;
                        $str .= "§c> §eCombining scroll level with Enchant level...\n";
                        if ($newlevel >= 10) {
                            $newlevel = 10;
                        }
                        $str .= "§c> §eNew $ename level = $newlevel \n";
                        $user = $this->um->getOnlineUser($sender->getName());
                        if ($user->removeMoney(Data::$commandInfernoCost)) {
                            $ench = $hand->getNamedTag()->getListTag(Item::TAG_ENCH);
                            if ($ench !== null && $ench->getTagType() === NBT::TAG_Compound) {
                                foreach ($ench as $entry) {
                                    /** @var CompoundTag $entry */
                                    if ($entry->getShort("id") === $id) {
                                        if ($entry->getShort("lvl") === $elevel) {
                                            $entry->setShort("lvl", $newlevel);
                                            break;
                                        }
                                    }
                                }
                            }
                            $hand->setNamedTag($hand->getNamedTag()->setTag(Item::TAG_ENCH, $ench));
                            if (!$hand->hasCustomName()) {
                                $hand = $this->func->setEnchantmentNames($hand, false);
                            } else {
                                $cn = $hand->getCustomName();
                                $cname = explode("\n", $cn);
                                $hand = $this->func->setEnchantmentNames($hand, $cname[0]);
                            }
                            $item->pop();
                            $sender->getInventory()->setItem($slot, $item);
                            $sender->getInventory()->setItemInHand($hand);
                            $str .= "§c> §eScroll merged with $ename for 10,000$!\n";
                        } else {
                            $str .= "§c> §eCouldn't levelup the Enchant, you don't have 10,000$...\n";
                        }
                        break;
                    }
                }
            }
        }
        if ($flag == true) {
            $sender->sendMessage($str . "§c> Stopping Scroll finder...!");
        } else {
            $sender->sendMessage($str . "§c> No valid Scrolls were found! Get scrolls from /vote, /manashop or /warp crates or gkits\n§c> Stopping Scroll finder...!");
        }
    }
}