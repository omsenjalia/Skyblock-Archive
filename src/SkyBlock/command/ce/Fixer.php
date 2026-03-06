<?php


namespace SkyBlock\command\ce;


use alvin0319\CustomItemLoader\CustomItems;
use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Durable;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\Tool;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Data;
use SkyBlock\item\armor\Crown;
use SkyBlock\Main;
use SkyBlock\util\Lore;
use SkyBlock\util\Values;

class Fixer extends BaseCommand {

    /**
     * Fixer constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'fixer', 'Merge fixer scroll to your damaged tool');
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (isset($args[0]) or !$sender instanceof Player) {
            $this->sendUsage($sender, $commandLabel);
            return;
        }
        $hand = $sender->getInventory()->getItemInHand();
        if ($hand->getTypeId() === 0) {
            $this->sendMessage($sender, "§4[Error] §cYou are holding nothing!");
            return;
        }
        if (!$hand instanceof Durable || ($hand->getTypeId() !== CustomItems::ELYTRA()->getTypeId() and !$hand instanceof Armor and !$hand instanceof Tool and !$hand instanceof Crown)) {
            $this->sendMessage($sender, "§4[Error] §cOnly tools and armors can be fixed!");
            return;
        }
        if ($hand->getCount() != 1) {
            $this->sendMessage($sender, "§4[Error] §cYou are holding invalid item!");
            return;
        }
        if ($sender->getWorld()->getDisplayName() === Values::PVP_WORLD) {
            $this->sendMessage($sender, "§eCan't fix items here.§r");
            return;
        }
        if ($hand->getDamage() == 0) {
            $this->sendMessage($sender, "§4[Error]§c This item is already brand new!");
            return;
        }
        $new = 1;
        $max = Values::MAX_DEFAULT_FIX;
        if (($fixlore = Lore::getLoreInfo($hand->getLore(), Values::FIX_LORE, Lore::FIX_STR)) !== null) {
            $data = explode("/", $fixlore);
            [$cur, $max] = $data;
            if ($cur >= $max) {
                $this->sendMessage($sender, "§4[Error]§c That item has already been fixed Max - $max times! Check item info");
                return;
            }
            $cur = (int) $cur;
            $new = $cur + 1;
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
                    if ($type == 'Fixer' and $type2 == 'Scroll') {
                        $flag = true;
                        $str .= "§c> §eFixer scroll detected...\n";
                        $str .= "§c> §eCombining scroll with tool...\n";
                        $user = $this->um->getOnlineUser($sender->getName());
                        if ($user->removeMoney(Data::$commandFixerCost)) {
                            $hand->setDamage(0);
                            Lore::setLoreInfo($hand, Values::FIX_LORE, Lore::FIX_STR . "$new/$max");
                            $sender->getInventory()->setItemInHand($hand);
                            $item->setCount($item->getCount() - 1);
                            $sender->getInventory()->setItem($slot, $item);
                            $str .= "§c> §eScroll merged with tool for 7,500$!\n";
                        } else {
                            $str .= "§c> §eCouldn't fix the tool, you don't have 7,500$...\n";
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