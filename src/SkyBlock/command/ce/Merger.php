<?php


namespace SkyBlock\command\ce;


use alvin0319\CustomItemLoader\CustomItems;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use SkyBlock\command\BaseCommand;
use SkyBlock\Data;
use SkyBlock\Main;

class Merger extends BaseCommand {
    /**
     * Merger constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'merger', 'Merge 2 CE Books for better accuracy');
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (isset($args[0]) or !$sender instanceof Player) {
            $this->sendMessage($sender, "§cUsage: /merger");
            return;
        }
        $hand = $sender->getInventory()->getItemInHand();
        $handId = $hand->getTypeId();
        if ($hand->getCount() !== 1 && $hand->getTypeId() === 0) {
            $this->sendMessage($sender, "§4[Error] §cHold a CE Book to merge with another CE Book in your inventory!");
            return;
        }
        if (!CustomItems::isBook($handId)) {
            $this->sendMessage($sender, "§4[Error] §cOnly Enchanted Books can be used to increase accuracy of by /merger which merges same books and gives same book with better accuracy! Hold a ce book in your hand");
            return;
        }
        if (!$hand->hasCustomName()) {
            $this->sendMessage($sender, "§4[Error] §cOnly CE Books can be used to increase accuracy of by /enchanter!");
            return;
        }
        $user = $this->um->getOnlineUser($sender->getName());
        if (!$user->hasMoney(10000)) {
            $this->sendMessage($sender, "§4[Error] §cYou do not have enough money to merge books! Money req: §610,000$");
            return;
        }
        if (count(explode("\n", $hand->getCustomName())) > 0) {
            $cebookname = explode(" ", $hand->getCustomName());
            $cename = $cebookname[1];
            if ($this->func->getEnchantmentId($cename) === null) {
                $this->sendMessage($sender, "§4[Error] §cInvalid CE book!");
                return;
            }
            /** @var int $accuracy */
            $accuracy = $cebookname[7];
            $accuracy = TF::clean($accuracy);
            $accuracy = str_replace("%%", "", $accuracy);
            $items = $sender->getInventory()->getContents();
            $flag = false;
            $str = "§c> §eStarting Merger...!\n";
            $str .= "§c> §e$cename §eBook detected in your hand ...\n";
            $heldslot = $sender->getInventory()->getHeldItemIndex();
            foreach ($items as $slot => $item) {
                if ($slot == $heldslot) continue;
                if (CustomItems::isBook($item->getTypeId())) {
                    if ($item->hasCustomName()) {
                        $spaces = explode(" ", $item->getCustomName());
                        $cename2 = $spaces[1];
                        if (TF::clean($cename) != TF::clean($cename2)) continue;
                        if ($item->getCount() != 1) {
                            $str .= "§c> Book count is more than 1, stacked books cant be merged...!";
                            break;
                        }
                        /** @var int $accuracy2 */
                        $accuracy2 = $spaces[7];
                        $accuracy2 = TF::clean($accuracy2);
                        $accuracy2 = str_replace("%%", "", $accuracy2);
                        $flag = true;
                        $str .= "§c> §eAnother $cename §eBook detected in inventory with accuracy §a{$accuracy2}%...\n";
                        $newaccuracy = $accuracy . $accuracy2;
                        if ($newaccuracy >= 99) $newaccuracy = 99;
                        $str .= "§c> §eMerging both books with new accuracy {$newaccuracy}%...\n";
                        $str .= "§c> §eNew $cename §eBook accuracy = §a$newaccuracy% \n";
                        $user->removeMoney(Data::$commandMergerCost);
                        $sender->getInventory()->remove($item);
                        $hand = VanillaItems::ENCHANTED_BOOK();
                        $cebookname[7] = "§a" . $newaccuracy . "%%";
                        $name = implode(" ", $cebookname);
                        $sender->getInventory()->setItemInHand($hand->setCustomName($name));
                        $str .= "§c> §eBooks merged of $cename §eCE for §610,000$!\n";
                        break;
                    }
                }
            }
            if ($flag == true) {
                $sender->sendMessage($str . "§c> Stopping Merger...!");
            } else {
                $sender->sendMessage($str . "§c> No valid similar CE Books were found!\n§c> Stopping Merger...!");
            }
        }
    }
}