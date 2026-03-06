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

class Enchanter extends BaseCommand {
    /**
     * Enchanter constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'enchanter', 'Increase Books accuracy');
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (isset($args[0]) or !$sender instanceof Player) {
            $this->sendMessage($sender, "§cUsage: /enchanter");
            return;
        }
        $hand = $sender->getInventory()->getItemInHand();
        $handId = $hand->getTypeId();
        if (!CustomItems::isBook($handId)) {
            $this->sendMessage($sender, "§4[Error] §cOnly Enchanted Books can be used to increase accuracy of by /enchanter! Hold a ce book in your hand");
            return;
        }
        if ($hand->getCount() != 1) {
            $this->sendMessage($sender, "§4[Error] §cHolding invalid item!");
            return;
        }
        if (!$hand->hasCustomName()) {
            $this->sendMessage($sender, "§4[Error] §cOnly CE Books can be used to increase accuracy of by /enchanter!");
            return;
        }
        if (count(explode("\n", $hand->getCustomName())) > 0) {
            $cebookname = explode(" ", $hand->getCustomName());
            $cename = $cebookname[1];
            if ($this->func->getEnchantmentId($cename) === null) {
                $this->sendMessage($sender, "§4[Error] §cInvalid CE book!");
                return;
            }
            $accuracy = $cebookname[7];
            $accuracy = TF::clean($accuracy);
            /** @var int $accuracy */
            $accuracy = str_replace("%%", "", $accuracy);
            if ($accuracy >= 99) {
                $this->sendMessage($sender, "§4[Error] §cAccuracy is already maxed out!");
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
                        if ($type == 'Enchanter' and $type2 == 'Scroll') {
                            $flag = true;
                            $str .= "§c> §e$cename §eBook detected in your hand ...\n";
                            $str .= "§c> §eEnchanter scroll detected...\n";
                            $newaccuracy = $accuracy + mt_rand(10, 40);
                            $str .= "§c> §eCombining scroll accuracy with CE Book accuracy...\n";
                            if ($newaccuracy >= 99) {
                                $newaccuracy = 99;
                            }
                            $str .= "§c> §eNew $cename §eBook accuracy = $newaccuracy%\n";
                            $user = $this->um->getOnlineUser($sender->getName());
                            if ($user->removeMoney(Data::$commandEnchanterCost)) {
                                $item->setCount($item->getCount() - 1);
                                $sender->getInventory()->setItem($slot, $item);
                                $hand = $sender->getInventory()->getItemInHand();
                                $cebookname[7] = "§a" . $newaccuracy . "%%";
                                $name = implode(" ", $cebookname);
                                $sender->getInventory()->setItemInHand($hand->setCustomName($name));
                                $str .= "§c> §eScroll merged with $cename §efor §610,000$!\n";
                            } else {
                                $str .= "§c> §eCouldn't improve accuracy of the CE Book, you don't have 10,000$...\n";
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
}