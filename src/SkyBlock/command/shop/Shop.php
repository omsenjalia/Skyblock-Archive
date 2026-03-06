<?php


namespace SkyBlock\command\shop;


use pocketmine\block\BlockTypeIds;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\Values;

class Shop extends BaseCommand {
    /**
     * Shop constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'shop', 'Shop Menu', '', true, ['buy']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if ($sender->getWorld()->getDisplayName() === Values::PVP_WORLD) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot use this command here!");
            return;
        }
        if (!isset($args[0])) {
            Main::getInstance()->getFormFunctions()->getShop()->sendShopMainMenu($sender);
        } else {
            $itemName = $args[0];
            $item = StringToItemParser::getInstance()->parse($itemName) ?? LegacyStringToItemParser::getInstance()->parse($itemName);
            if ($item === null) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is not a valid item!");
                return;
            }
            if ($item->getTypeId() === BlockTypeIds::AIR || $item->getTypeId() === ItemTypeIds::POTION) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That item cannot be bought from the shop!");
                return;
            }
            $count = (isset($args[1]) && is_int((int) $args[1])) ? (int) $args[1] : 1;
            if ($count > 2304 || $count < 1) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is not a valid amount. Item amount must be between 1 and 2304!");
                return;
            }
            $perMoney = Main::getInstance()->getFormFunctions()->getShop()->getShopMoneyData($item->getVanillaName());
            if ($perMoney === null || $perMoney <= 0) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That item cannot be bought from the shop!");
                return;
            }
            $money = $perMoney * ($count);

            $item->setCount($count);
            if (!$sender->getInventory()->canAddItem($item)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Your inventory does not have enough space to buy that many items!");
                return;
            }
            $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
            if (!$user->hasMoney($money)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You need $money to make that purchase!");
                return;
            }
            $sendMessage = false;
            if (!isset(Main::getInstance()->shopconfirm[$sender->getName()])) {
                $sendMessage = true;
            } else {
                $oldItem = Main::getInstance()->shopconfirm[$sender->getName()];
                assert($oldItem instanceof Item);
                if ($oldItem->getTypeId() !== $item->getTypeId() || $oldItem->getStateId() !== $item->getStateId() || $oldItem->getCount() !== $item->getCount()) {
                    $sendMessage = true;
                }
            }
            $perMoney = number_format($perMoney);
            $moneyString = number_format($money);
            if ($sendMessage) {
                $this->sendMessage($sender, TextFormat::YELLOW . "Are you sure you want to buy x$count " . $item->getName() . " for $$perMoney each ($$moneyString total). Run this command again to confirm you do!");
                Main::getInstance()->shopconfirm[$sender->getName()] = $item;
                return;
            }
            $user->removeMoney($money);
            $sender->getInventory()->addItem($item);
            $this->sendMessage($sender, TextFormat::YELLOW . "You have purchased x$count " . $item->getName() . " for $$perMoney each ($$moneyString total)");
        }
    }
}