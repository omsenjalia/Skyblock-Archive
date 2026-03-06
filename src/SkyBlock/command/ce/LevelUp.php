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
use SkyBlock\item\armor\Crown;
use SkyBlock\item\NAxe;
use SkyBlock\item\NPickaxe;
use SkyBlock\item\NShovel;
use SkyBlock\Main;
use SkyBlock\util\Constants;

class LevelUp extends BaseCommand {
    /**
     * LevelUp constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'levelup', 'Level up your CE', '<ce name>');
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
        $cename = strtolower($args[0]);
        if ($this->func->countEnchants($hand, "ce") <= 0) {
            $this->sendMessage($sender, "§4[Error] §cThe item you're holding doesn't have any custom enchantments on it to level up!");
            return;
        }
        if ($hand->getTypeId() === 0) {
            $this->sendMessage($sender, "§4[Error] §cYou are holding nothing!");
            return;
        }
        if (!$hand instanceof Armor && !$hand instanceof TieredTool && !$hand instanceof Bow && !$hand instanceof Crown) {
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
        if (($id = $this->func->getEnchantmentId($cename)) !== null) {
            if ($id < 22) {
                $this->sendMessage($sender, "§4[Error] §cThat CE wasn't found on held item! Type the name correctly!");
                return;
            }
            if (!BaseEnchantment::hasEnchantment($hand, $id)) {
                $this->sendMessage($sender, "§4[Error] §cThat CE wasn't found on held item! Type the name correctly!");
                return;
            }
            if (($celevel = BaseEnchantment::getEnchantmentLevel($hand, $id)) >= 6) {
                $this->sendMessage($sender, "§4[Error] §cThat CE specified already has the max level 6!");
                return;
            }
        } else {
            $this->sendMessage($sender, "§4[Error] §cThat CE specified was not found! Please enter correct ce name!");
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
                    $level = mt_rand(1, 5);
                    if ($type == 'LevelUp' and $type2 == 'Scroll') {
                        $flag = true;
                        $str .= "§c> §e$cename of level $celevel detected on held item ...\n";
                        $str .= "§c> §eLevelUp scroll detected...\n";
                        $newlevel = $level + $celevel;
                        $str .= "§c> §eCombining scroll level with CE level...\n";
                        if ($newlevel >= 6) {
                            $newlevel = 6;
                        }
                        $str .= "§c> §eNew $cename level = $newlevel \n";
                        $user = $this->um->getOnlineUser($sender->getName());
                        if ($user->removeMoney(Data::$commandLevelupCost)) {
                            $ench = $hand->getNamedTag()->getListTag(Item::TAG_ENCH);
                            if ($ench !== null && $ench->getTagType() === NBT::TAG_Compound) {
                                foreach ($ench as $entry) {
                                    /** @var CompoundTag $entry */
                                    if ($entry->getShort("id") === $id) {
                                        if ($entry->getShort("lvl") === $celevel) {
                                            $entry->setShort("lvl", $newlevel);
                                            break;
                                        }
                                    }
                                }
                            }
                            $hand->setNamedTag($hand->getNamedTag()->setTag(Item::TAG_ENCH, $ench));
                            if (!$hand->hasCustomName()) {
                                $hand = $this->func->setEnchantmentNames($hand, false);
                            }
                            if ($hand->hasCustomName()) {
                                $cn = $hand->getCustomName();
                                $cname = explode("\n", $cn);
                                $hand = $this->func->setEnchantmentNames($hand, $cname[0]);
                            }
                            $item->setCount($item->getCount() - 1);
                            $sender->getInventory()->setItem($slot, $item);
                            $sender->getInventory()->setItemInHand($hand);
                            $str .= "§c> §eScroll merged with $cename for 5,000$!\n";
                        } else {
                            $str .= "§c> §eCouldn't levelup the ce, you don't have 5,000$...\n";
                        }
                        break;
                    }
                }
            }
        }
        if ($flag === true) {
            $sender->sendMessage($str . "§c> Stopping Scroll finder...!");
        } else {
            $sender->sendMessage($str . "§c> No valid Scrolls were found! Get scrolls from /vote, /manashop or /warp crates or gkits\n§c> Stopping Scroll finder...!");
        }
    }
}