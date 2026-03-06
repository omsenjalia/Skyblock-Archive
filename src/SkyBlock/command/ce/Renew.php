<?php


namespace SkyBlock\command\ce;


use alvin0319\CustomItemLoader\CustomItems;
use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\Tool;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Data;
use SkyBlock\Main;
use SkyBlock\util\Lore;
use SkyBlock\util\Values;

class Renew extends BaseCommand {

    /**
     * Renew constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, "renew", "Renew scroll cmd");
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
        if ($hand->getTypeId() !== CustomItems::ELYTRA()->getTypeId() and !$hand instanceof Armor and !$hand instanceof Tool) {
            $this->sendMessage($sender, "§4[Error] §cOnly tools and armors can be renewed!");
            return;
        }
        if ($hand->getCount() !== 1) {
            $this->sendMessage($sender, "§4[Error] §cYou are holding more than one item!");
            return;
        }
        if (($fixlore = Lore::getLoreInfo($hand->getLore(), Values::FIX_LORE, Lore::FIX_STR)) !== null) {
            $data = explode("/", $fixlore);
            [$cur, $max] = $data;
            if ($cur < $max) {
                $this->sendMessage($sender, "§4[Error]§c Item needs to be fixed max for $max times to renew!");
                return;
            }
        } else {
            $this->sendMessage($sender, "§4[Error]§c That item has never been fixed!");
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
                    if ($type == 'Renew' and $type2 == 'Scroll') {
                        $flag = true;
                        $str .= "§c> §eRenew scroll detected...\n";
                        $str .= "§c> §eCombining scroll with tool...\n";
                        $user = $this->um->getOnlineUser($sender->getName());
                        if ($user->removeMoney(Data::$commandRenewCost)) {
                            Lore::setLoreInfo($hand, Values::FIX_LORE, Lore::FIX_STR . "0/" . $max);
                            $sender->getInventory()->setItemInHand($hand);
                            $item->setCount($item->getCount() - 1);
                            $sender->getInventory()->setItem($slot, $item);
                            $str .= "§c> §eScroll merged with tool for 5,000$!\n";
                        } else {
                            $str .= "§c> §eCouldn't fix the tool, you don't have 5,000$...\n";
                        }
                        break;
                    }
                }
            }
        }
        if ($flag) {
            $sender->sendMessage($str . "§c> Stopping Scroll finder...!");
        } else {
            $sender->sendMessage($str . "§c> No valid Scrolls were found! Get scrolls only from /vote, /manashop, /mcs or gkits\n§c> Stopping Scroll finder...!");
        }
    }

}