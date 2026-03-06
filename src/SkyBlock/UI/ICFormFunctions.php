<?php

namespace SkyBlock\UI;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use SkyBlock\ItemCloud;
use SkyBlock\Main;
use SkyBlock\util\TexturePath;
use SkyBlock\util\Util;

class ICFormFunctions {
    /** @var Main */
    public Main $pl;
    /** @var FormFunctions */
    public FormFunctions $ff;

    /**
     * ShopFormFunctions constructor.
     *
     * @param Main          $plugin
     * @param FormFunctions $formfunc
     */
    public function __construct(Main $plugin, FormFunctions $formfunc) {
        $this->pl = $plugin;
        $this->ff = $formfunc;
    }

    public function sendItemCloudInv(Player $player) {
        $form = new SimpleForm(null);
        $title = "§b§lItem Cloud";
        $buttons = [];
        $cloud = null;
        $name = strtolower($player->getName());
        if (isset($this->pl->clouds[$name])) {
            $cloud = $this->pl->clouds[$name];
        } else {
            $this->pl->clouds[$name] = new ItemCloud($name, []);
            $cloud = $this->pl->clouds[$name];
        }
        $form->setTitle($title);
        foreach ($cloud->getItems() as $item => $count) {
            $namespace = explode(":", $item)[0];
            $typeId = explode(":", $item)[0];
            $texture = $this->getTexturePath($namespace);
            if (!$texture) {
                $form->addButton("§j" . $namespace . " §qX" . $count);
            } else {
                $form->addButton("§j" . $namespace . " §qX" . $count, 0, $texture);
            }
            $buttons[] = [$namespace, $count];
        }

        $form->addButton("§4§lExit");
        $func = function(Player $player, ?int $data) use ($buttons) : void {
            if ($data !== null) {
                if (!isset($buttons[$data][0])) {
                    return;
                }
                $namespace = $buttons[$data][0];
                $count = $buttons[$data][1];
                $this->withdrawForm($player, $namespace, $count);
                //                switch ($data) {
                //                    case 1:
                //                        $this->sendAmountWindow($player, ItemTypeNames::LEATHER_HELMET);
                //                        break;
                //                    case 2:
                //                        $this->sendAmountWindow($player, ItemTypeNames::GOLDEN_HELMET);
                //                        break;
                //                    case 3:
                //                        $this->sendAmountWindow($player, ItemTypeNames::CHAINMAIL_HELMET);
                //                        break;
                //                    case 4:
                //                        $this->sendAmountWindow($player, ItemTypeNames::IRON_HELMET);
                //                        break;
                //                    case 5:
                //                        $this->sendAmountWindow($player, ItemTypeNames::DIAMOND_HELMET);
                //                        break;
                //                    default:
                //                        $this->sendEquipmentsMenu($player);
                //                        break;
                //                }
            }
        };
        $form->setCallable($func);
        $player->sendForm($form);
    }

    public function withdrawForm(Player $player, string $item, int $count) {
        $form = new CustomForm(null);
        $form->setTitle("§e§lItem Cloud §6Withdraw");
        //        $form->addLabel("You are withdrawing §j" . $item . "\n§6Please enter a number greater than 0 and less than " . $count . "!");
        $form->addLabel("§eYou have §l§q" . $count . " " . $item . " §r§ein the Item Cloud");
        $form->addInput("§ePick an amount you want to withdraw!");
        $form->setCallable(function(Player $player, ?array $data) use ($item, $count) : void {
            if ($data !== null) {
                if (!is_int((int) $data[1]) or empty($data[1])) {
                    $error = "§6Please enter a number!";
                    $player->sendMessage($error);
                    return;
                }
                $data[1] = (int) $data[1];
                $amount = (int) $data[1];
                if ($amount < 1 or $amount > $count) {
                    $error = "§6Please enter a valid amount!";
                    $player->sendMessage($error);
                    return;
                }
                $cloud = $this->pl->clouds[strtolower($player->getName())];
                $item = StringToItemParser::getInstance()->parse($item);
                if (!$cloud->itemExists($item->getVanillaName(), $amount)) {
                    $player->sendMessage("§4[Error] §cYou don't have enough item in your account.");
                    return;
                }
                if (($slots = Util::getSlotsForItem($player->getInventory(), $item)) <= 0) {
                    $player->sendMessage("§4[Error] §cYour inventory is full to add that item!");
                    return;
                }
                $changed = "§7Downloaded - x§c{$amount} §a{$item->getName()}";
                if ($amount > $slots) {
                    $changed = "§cYou didnt have space in your inventory to download §7x§c$amount §cof §a{$item->getName()}. §cDownloaded §7x§c{$slots} §cinstead.";
                    $amount = $slots;
                }
                $item->setCount($amount);
                $cloud->removeItem($item->getVanillaName(), $amount);
                $player->getInventory()->addItem($item);
                $player->sendMessage("§eYou have downloaded an Item from your ItemCloud account.\n$changed");
                return;
            }
        }
        );
        $player->sendForm($form);
    }

    public function getTexturePath(string $namespace) {
        $namespace = strtolower(StringToItemParser::getInstance()->parse($namespace)->getName());
        return TexturePath::getTexture($namespace);

    }
}