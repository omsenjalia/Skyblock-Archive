<?php


namespace SkyBlock\command\ce;


use alvin0319\CustomItemLoader\CustomItems;
use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Bow;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\TieredTool;
use pocketmine\item\ToolTier;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use SkyBlock\command\BaseCommand;
use SkyBlock\command\Functions;
use SkyBlock\enchants\BaseEnchantment;
use SkyBlock\item\armor\Crown;
use SkyBlock\item\NAxe;
use SkyBlock\item\NPickaxe;
use SkyBlock\item\NShovel;
use SkyBlock\Main;
use SkyBlock\util\Constants;

class Combiner extends BaseCommand {
    /**
     * Combiner constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'combiner', 'Combine your CE book with your tool');
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (isset($args[0]) or !$sender instanceof Player) {
            $this->sendMessage($sender, $commandLabel);
            return;
        }
        $hand = $sender->getInventory()->getItemInHand();
        if ($hand->getTypeId() === 0) {
            $this->sendMessage($sender, "§4[Error] §cYou are holding nothing!");
            return;
        }
        if (!$hand instanceof Durable || (!$hand instanceof Armor && !$hand instanceof TieredTool && !$hand instanceof Bow && !$hand instanceof Crown)) {
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
        if ($hand->getCount() !== 1) {
            $this->sendMessage($sender, "§4[Error] §cYou are holding invalid item!");
            return;
        }
        if (($count = $this->func->countEnchants($hand, "ce")) >= 6) {
            $sender->sendMessage("§c> §cMax Custom Enchants per Item = 6 reached already! Terminating...");
            return;
        }
        if (($type = Functions::getItemType($hand)) === null) {
            $this->sendMessage($sender, "§4[Error] §cOnly Tools or Armor can be enchanted!");
            return;
        }
        $flag = false;
        $str = "§c> §eCombiner is now running...!\n§c> §e$type Detected in hand...!\n§c> §eMerging all $type type Custom Enchanted Books in Inventory with the $type!\n";
        foreach ($sender->getInventory()->getContents() as $slot => $item) {
            if (CustomItems::isBook($item->getTypeId())) {
                if ($item->hasCustomName()) {
                    if (count(explode("\n", $item->getCustomName())) > 0) {
                        $cebookname = explode(" ", $item->getCustomName());
                        $cename = $cebookname[1];
                        $cetype = $cebookname[3];
                        $accuracy = str_replace("%%", "", TF::clean($cebookname[7]));
                        if ($this->func->checkCompatibility(strtolower(TF::clean($cetype)), strtolower($type))) {
                            $str .= "§c> $cename §r§eEnchant Book of Accuracy $accuracy% Detected...\n";
                            if (($id = $this->func->getEnchantmentId($cename)) !== null) {
                                if ($count < 6) {
                                    if (!BaseEnchantment::hasEnchantment($hand, $id)) {
                                        $flag = true;
                                        $str .= "§c> §eTrying to merge the book with the $type...\n";
                                        $value = $this->func->getAccuracyValue($accuracy);
                                        if ($value == 2) {
                                            $str .= "§c> §eBook is now under control and ready to merge...\n";
                                            $celevel = mt_rand(1, 5);
                                            $str .= "§c> §e$cename §rof level $celevel is now merging with the $type...\n";
                                            $item->pop();
                                            $sender->getInventory()->setItem($slot, $item);
                                            $enchantment = BaseEnchantment::getEnchantment($id);
                                            $hand->addEnchantment(new EnchantmentInstance($enchantment, $celevel));
                                            $count++;
                                            $hand = $this->func->setEnchantmentNames($hand, false);
                                            $sender->getInventory()->setItemInHand($hand);
                                        } elseif ($value == 1) {
                                            $str .= "§c> §cOh no, didn't work! The Book is now outta control...\n";
                                            $str .= "§c> §cThe book damaged the tool in your hand while you were trying to merge...\n";
                                            $str .= "§c> §eFortunately, the book wasn't damaged and is safe...\n";
                                            assert($hand instanceof Durable);
                                            $sender->getInventory()->setItemInHand($hand->setDamage(self::getSafeDamage($hand)));
                                            $str .= "§c> §eTry to increase it's accuracy next time by Enchanter scroll with /enchanter...\n";
                                            break;
                                        } else {
                                            $str .= "§c> §cOh no, didn't work! The Book is now outta control...\n";
                                            $i = mt_rand(1, 6);
                                            switch ($i) {
                                                case 1:
                                                    $str .= "§c> §cThe book got destroyed with the tool in your hand completely while you were trying to merge...\n";
                                                    $sender->getInventory()->setItemInHand(VanillaItems::AIR());
                                                    $item->setCount($item->getCount() - 1);
                                                    $sender->getInventory()->setItem($slot, $item);
                                                    break;
                                                case 2:
                                                    $str .= "§c> §cThe book destroyed the tool in your hand completely while you were trying to merge...\n";
                                                    $str .= "§c> §eFortunately, the book wasn't damaged and is safe...\n";
                                                    $sender->getInventory()->setItemInHand(VanillaItems::AIR());
                                                    break;
                                                case 3:
                                                    $str .= "§c> §cThe book destroyed itself and damaged the tool in your hand while you were trying to merge...\n";
                                                    assert($hand instanceof Durable);
                                                    $sender->getInventory()->setItemInHand($hand->setDamage(self::getSafeDamage($hand)));
                                                    $item->setCount($item->getCount() - 1);
                                                    $sender->getInventory()->setItem($slot, $item);
                                                    break;
                                                default:
                                                    $str .= "§c> §cThe book damaged the tool in your hand while you were trying to merge...\n";
                                                    $str .= "§c> §eFortunately, the book wasn't damaged and is safe...\n";
                                                    assert($hand instanceof Durable);
                                                    $sender->getInventory()->setItemInHand($hand->setDamage(self::getSafeDamage($hand)));
                                                    break;
                                            }
                                            $str .= "§c> §eTry to increase it's accuracy next time by Enchanter scroll with /enchanter...\n";
                                        }
                                    } else {
                                        $str .= "§c> $cename §cenchant already exists on the tool!\n";
                                    }
                                } else {
                                    $str .= "§c> §cMax Custom Enchants per Item = 6 reached! Terminating...\n";
                                    $flag = true;
                                    break;
                                }
                            } else {
                                $str .= "§c> $cename §cenchant not found on server! Invalid enchant!\n";
                            }
                        }
                    }
                }
            }
        }
        if ($flag == true) {
            $sender->sendMessage($str . "§c> Stopping Combiner...!");
        }
        if ($flag == false) {
            $sender->sendMessage($str . "§c> No valid Enchants books were found!\n§c> Stopping Combiner...!");
        }
    }

    /**
     * @param Durable $item
     *
     * @return int
     */
    public static function getSafeDamage(Durable $item) : int {
        $damage = $item->getDamage() + mt_rand(20, 50);
        return $damage > $item->getMaxDurability() ? ($item->getMaxDurability() - 1) : $damage;
    }

}