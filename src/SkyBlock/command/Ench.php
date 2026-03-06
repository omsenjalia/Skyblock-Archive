<?php


namespace SkyBlock\command;


use alvin0319\CustomItemLoader\CustomItems;
use pocketmine\command\CommandSender;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;
use SkyBlock\enchants\BaseEnchantment;
use SkyBlock\Main;

class Ench extends BaseCommand {
    /**
     * Ench constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'ench', 'Merge Ench Orb to tool');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        $hand = $sender->getInventory()->getItemInHand();
        $type = Functions::getItemtype($hand);
        if ($type == null) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can only change tools and armor!");
            return;
        }
        if ($hand->getCount() !== 1) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is not a valid item!");
            return;
        }
        $count = Main::getInstance()->getFunctions()->countEnchants($hand);
        if ($count >= 4) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot put more than 4 vanilla enchantments on an item!");
            return;
        }
        $flag = false;
        $str = TextFormat::RED . "> Combing is now running...!\n";
        $str .= TextFormat::YELLOW . "> $type Detected in hand...!\n";
        $str .= TextFormat::YELLOW . "> Merging all enchantment orbs in inventory with the $type!\n";
        foreach ($sender->getInventory()->getContents() as $slot => $item) {
            if ($item->getTypeId() === CustomItems::ENDER_EYE()->getTypeId() && $item->hasCustomName() && count(explode("\n", $item->getCustomName())) > 0) {
                $orbName = explode(" ", $item->getCustomName());
                $name = $orbName[1];
                $type = $orbName[2];
                if (!isset($orbName[6]) || !isset($orbName[9])) {
                    continue;
                }
                $level = TextFormat::clean($orbName[6]);
                $id = TextFormat::clean($orbName[9]);
                if ($type == "§r§9Enchantment") {
                    $str .= TextFormat::YELLOW . "> $name enchantment orb of level $level detected...\n";
                    if ($count < 4) {
                        if (!BaseEnchantment::hasEnchantment($hand, $id)) {
                            $flag = true;
                            $count++;
                            $str .= TextFormat::YELLOW . "> $name is now merging with the $type...\n";
                            $enchantment = BaseEnchantment::getEnchantment((int) $id);
                            $hand->addEnchantment(new EnchantmentInstance($enchantment, $level));
                            $sender->getInventory()->setItemInHand($hand);
                            $item->setCount($item->getCount() - 1);
                            $sender->getInventory()->setItem($slot, $item);
                            $str .= TextFormat::YELLOW . "Enchanting succeeded!\n";
                            $str .= TextFormat::YELLOW . "Enchanted your item in hand!\n";
                        } else {
                            $str .= TextFormat::YELLOW . "> $name enchantment is already on your $type...\n";
                        }
                    } else {
                        $str .= TextFormat::YELLOW . "> Your item already has 4 vanilla enchantments on it!";
                        $flag = true;
                        break;
                    }
                }
            }
        }
        if ($flag) {
            $this->sendMessage($sender, TextFormat::YELLOW . " All valid enchantment orbs were merged successfully!\n> Stopping Combiner...!");
        } else {
            $this->sendMessage($sender, TextFormat::YELLOW . " No valid enchantment orbs were found!\n> Stopping Combiner...!");
        }
    }
}