<?php


namespace SkyBlock\command\ce;


use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\Tool;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Data;
use SkyBlock\Main;
use SkyBlock\util\Lore;
use SkyBlock\util\Values;

class Surge extends BaseCommand {

    /**
     * Surge constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, "surge", "Surge scroll cmd");
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
        if ($hand->getTypeId() !== 444 and !$hand instanceof Armor and !$hand instanceof Tool) {
            $this->sendMessage($sender, "§4[Error] §cOnly tools and armors can be surged!");
            return;
        }
        if ($hand->getCount() !== 1) {
            $this->sendMessage($sender, "§4[Error] §cYou are holding more than one item!");
            return;
        }
        $incrBy = 1;
        $newcur = 0;
        $newmax = Values::MAX_DEFAULT_FIX + $incrBy;
        if (($fixlore = Lore::getLoreInfo($hand->getLore(), Values::FIX_LORE, Lore::FIX_STR)) !== null) {
            $data = explode("/", $fixlore);
            [$cur, $max] = $data;
            if ($max >= Values::MAX_FIX) {
                $this->sendMessage($sender, "§4[Error]§c That item has already been surged max! Max - " . Values::MAX_FIX . " fix limit!\n§7You need a Renew scroll to renew the Item. Get it from /mcs");
                return;
            }
            $newcur = $cur;
            $max = (int) $max;
            $newmax = $max + $incrBy;
            if ($newmax > Values::MAX_FIX) $newmax = Values::MAX_FIX;
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
                    if ($type == 'Surge' and $type2 == 'Scroll') {
                        $flag = true;
                        $str .= "§c> §eSurge scroll detected...\n";
                        $str .= "§c> §eCombining scroll with tool...\n";
                        $user = $this->um->getOnlineUser($sender->getName());
                        if ($user->removeMoney(Data::$commandSurgeCost)) {
                            Lore::setLoreInfo($hand, Values::FIX_LORE, Lore::FIX_STR . "$newcur/$newmax");
                            $sender->getInventory()->setItemInHand($hand);
                            $item->setCount($item->getCount() - 1);
                            $sender->getInventory()->setItem($slot, $item);
                            $str .= "§c> §eScroll merged with tool for 2,500$!\n";
                        } else {
                            $str .= "§c> §eCouldn't fix the tool, you don't have 2,500$...\n";
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