<?php

namespace SkyBlock\util;

use jojoe77777\FormAPI\CustomForm;
use pocketmine\block\Dirt;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\LegacyStringToItemParserException;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use SkyBlock\Data;
use SkyBlock\Main;

class ShopUtil {
    use SingletonTrait;

    public function sendAmountWindowForItem(Player $player, Item $item) : void {
        $form = new CustomForm(null);
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $form->setTitle($title);
        $form->addLabel("Please choose how " . (strtolower(substr($item->getName(), -1)) === "s" ? "many " : "much ") . "'" . $item->getName() . "' you want to buy?");
        $form->addInput("Amount -", "", $item->getMaxStackSize());
        $form->setCallable(function(Player $player, ?array $data) use ($item) : void {
            if ($data !== null) {
                if (!is_int((int) $data[1]) || empty($data[1])) {
                    $error = "§6Please enter a number!";
                    $this->sendResultForm($player, $error, "sendAmountWindowForItem", [$item]);
                    return;
                }
                $amount = (int) $data[1];
                if ($amount < 1 || $amount > 2304) {
                    $error = "§6Please enter a number greater than 0 and less than 2304!";
                    $this->sendResultForm($player, $error, "sendAmountWindowForItem", [$item]);
                    return;
                }
                self::sendConfirmWindow($player, $item, $amount);
            }
        }
        );
        $player->sendForm($form);
    }

    public function sendConfirmWindow(Player $player, Item $item, int $amount) : void {
        $item = $item->setCount($amount);
        $cost = self::getShopCostFromItem($item) ?? self::getShopCostFromNamespace($item->getVanillaName());
        if ($cost === null) {
            return;
        }
        $money = $cost * $amount;
        if ($money === 0) {
            $player->sendMessage("§cERROR - Report this item to staff! {{$item->getVanillaName()} => price not found}");
            return;
        }
        $message = "Are you sure you wanna buy x" . $amount . " " . $item->getName() . " for " . $money . "$ (at $cost$ each)";
        $func = function(Player $player, ?bool $data) use ($item, $amount, $money) : void {
            if ($data) {
                if ($player->getWorld()->getDisplayName() === "PvP") {
                    $player->sendMessage("§cYou can't shop here!");
                    return;
                }
                if (!$player->getInventory()->canAddItem($item)) {
                    $error = "§6Your Inventory is not empty enough to buy that much amount of Items!";
                    $this->sendResultForm($player, $error, "sendAmountWindowForItem", [$item]);
                    return;
                }
                $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
                if (!$user->removeMoney($money)) {
                    $error = "§6You don't have enough money to purchase that much! Required money: " . $money . "$ Check your money by /mymoney";
                    $this->sendResultForm($player, $error, "sendConfirmWindow", [$item, $amount]);
                    return;
                }
                $player->getInventory()->addItem($item);
                $itemName = $item->getName();
                $player->sendMessage("Transaction Successful! Successfully bought $itemName x " . $amount . " for " . $money . "$!");
            }

        };
        Main::getInstance()->getFormFunctions()->sendModalForm($player, "§e§lCheckout:", $message, ["Yes", "No"], $func);
    }

    public function sendResultForm(Player $player, string $message, string $func, array $args = []) : void {
        Main::getInstance()->getFormFunctions()->sendModalForm($player, "§6Result", $message, ["§2Go back", "§cExit"], function(Player $player, ?bool $data) use ($func, $args) {
            if ($data) {
                assert(method_exists($this, $func));
                array_unshift($args, $player);
                call_user_func_array([$this, $func], $args);
            }
        }
        );
    }

    public function getShopCostFromItem(Item $item) : ?int {
        return Data::$shopPrices[$item->getTypeId()] ?? null;
    }

    public function getShopCostFromNamespace(string $name, int $damage = 0) : ?int {
        $namespace = "minecraft:" . strtolower($name);
        $namespace = str_replace(" ", "_", $namespace);

        $cost = null;
        if ($damage === 0) {
            if (Main::getInstance()->shop->exists($name)) {
                $cost = Main::getInstance()->shop->get($name, null);
            } elseif (Main::getInstance()->shop->exists($namespace)) {
                $cost = Main::getInstance()->shop->get($namespace, null);
            } else {
                Main::getInstance()->getLogger()->critical("Item in shop not found! {" . $namespace . "}");
                return 0;
            }
        } else {
            if (Main::getInstance()->shop->exists($namespace)) {
                $cost = Main::getInstance()->shop->get($namespace, null);
            }
        }
        return $cost;
    }

    public function sendAmountWindow(Player $player, $namespace) : void {
        if (is_numeric($namespace)) {
            try {
                $namespace = str_replace(" ", "_", LegacyStringToItemParser::getInstance()->parse($namespace)->getVanillaName());
            } catch (LegacyStringToItemParserException) {
                Main::getInstance()->getLogger()->critical("the namespace \'" . $namespace . "\' is not a string or a legacy id int!");
                $player->sendMessage("§cERROR, please report this bug to staff! {0-" . $namespace . "}");
                return;
            }
        } elseif (!is_string($namespace)) {
            Main::getInstance()->getLogger()->critical("the namespace \'" . $namespace . "\' is not a string or a legacy id int!");
            $player->sendMessage("§cERROR, please report this bug to staff! {0-" . $namespace . "}");
            return;
        }
        $item = StringToItemParser::getInstance()->parse($namespace);
        $form = new CustomForm(null);
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $form->setTitle($title);
        if (!$item) {
            $player->sendMessage("§cERROR, please report this bug to staff! {1-" . $namespace . "}");
            return;
        }
        $form->addLabel("Please choose how " . (strtolower(substr($item->getName(), -1)) === "s" ? "many '" : "much '") . $item->getName() . "' you want to buy");
        $form->addInput("Amount -", "", $item->getMaxStackSize());
        $form->setCallable(function(Player $player, ?array $data) use ($item, $namespace) : void {
            if ($data !== null) {
                if (!is_int((int) $data[1]) || empty($data[1])) {
                    $error = "§6Please enter a number!";
                    $this->sendResultForm($player, $error, "sendAmountWindow", [$namespace]);
                    return;
                }
                $amount = $data[1];
                if ($amount < 1 || $amount > 2304) {
                    $error = "§6Please enter a number greater than 0 and less than 2304!";
                    $this->sendResultForm($player, $error, "sendAmountWindow", [$namespace]);
                    return;
                }
                self::sendConfirmWindow($player, $item, $amount);
            }
        }
        );
        $player->sendForm($form);

    }

}