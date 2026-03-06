<?php


namespace SkyBlock\UI;


use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\block\BlockTypeNames;
use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\LegacyStringToItemParserException;
use pocketmine\item\Potion;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\{TextFormat as TF};
use SkyBlock\Main;
use SkyBlock\tiles\AutoSellerTile;

class ShopFormFunctions {

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

    public function sendCEBooksAmount(Player $player, string $cebook, string $info) : void {
        $form = new CustomForm(null);
        $title = "§4§lCEBooks";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§rsmall";
        }
        $form->setTitle($title);
        $form->addLabel("Please choose how many " . ucfirst($cebook) . " CE books you want to buy?\n" . $info);
        $form->addInput("Amount -", "", "1");
        $form->setCallable(function(Player $player, ?array $data) use ($cebook, $info) : void {
            if ($data !== null) {
                if (!is_int((int) $data[1]) or empty($data[1])) {
                    $error = "§6Please enter a number!";
                    $this->sendResultForm($player, $error, "sendCEBooksAmount", [$cebook, $info]);
                    return;
                }
                $data[1] = (int) $data[1];
                $amount = (int) $data[1];
                if ($amount < 1 or $amount > 1000) {
                    $error = "§6Please enter a number greater than 0 and less than 1000!";
                    $this->sendResultForm($player, $error, "sendCEBooksAmount", [$cebook, $info]);
                    return;
                }
                $amount = $data[1];
                $this->sendCEBooksInfo($player, $cebook, $info, $amount);
            }
        }
        );
        $player->sendForm($form);
    }

    public function sendResultForm(Player $player, string $msg, string $funcName, array $args = []) : void {
        $this->ff->sendModalForm($player, "§6Result", $msg, ["§2Go back", "§cExit"], function(Player $player, ?bool $data) use ($funcName, $args) {
            if ($data) {
                assert(method_exists($this, $funcName));
                array_unshift($args, $player);
                call_user_func_array(array($this, $funcName), $args);
            }
        }
        );
    }

    public function sendCEBooksInfo(Player $player, string $cebook, string $info, int $amount) : void {
        $func = function(Player $player, ?bool $data) use ($cebook, $amount) : void {
            if ($data !== null) {
                if ($data) {
                    $item = $this->pl->getCEBook($cebook, $amount);
                    if (!$player->getInventory()->canAddItem($item)) {
                        $error = "§6Your Inventory is full!";
                        $this->sendResultForm($player, $error, "sendCEBooksMenu");
                        return;
                    }
                    if ($player->getXpManager()->getCurrentTotalXp() < ($cost = $this->getCost($cebook, $amount))) {
                        $error = "§6You don't have enough XP to purchase that much! Required XP: $cost! Check your XP by /myxp";
                        $this->sendResultForm($player, $error, "sendCEBooksMenu");
                        return;
                    }
                    $player->getXpManager()->addXp(-$cost, false);
                    $player->getInventory()->addItem($item);
                    $nam = ucfirst($cebook);
                    $this->ff->sendMessage($player, "§aSuccessfully bought x$amount of $nam Custom Enchant Book for $cost XP! Check your inventory for a $nam book and tap a block to redeem $nam Enchantment Book!");
                }
                $this->sendCEBooksMenu($player);
            }
        };
        $this->ff->sendModalForm($player, "Checkout", "Are you sure you wanna buy x$amount of " . ucfirst($cebook) . " CEBooks for {$this->getCost($cebook, $amount)}XP?", ["Yes", "No"], $func);
    }

    private function getCost($name, $amount) : int {
        return (int) match ($name) {
            'common' => 2000 * $amount,
            'rare' => 5000 * $amount,
            'legendary' => 20000 * $amount,
            'exclusive' => 500000 * $amount,
            'ancient' => 10000000 * $amount,
            default => 2000,
        };
    }

    public function sendCEBooksMenu(Player $player) : void {
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        $buttons = ["Back", "§6§lCommon", "§6§lRare", "§6§lLegendary", "§b§lExclusive", "§1§lAncient", "§c§lVaulted"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendCEBooksAmount($player, "common", "Common Custom Enchant Book - \nPer Cost - 2,000 XP");
                        break;
                    case 2:
                        $this->sendCEBooksAmount($player, "rare", "Rare Custom Enchant Book - \nPer Cost - 5,000 XP");
                        break;
                    case 3:
                        $this->sendCEBooksAmount($player, "legendary", "Legendary Custom Enchant Book - \nPer Cost - 20,000 XP");
                        break;
                    case 4:
                        $this->sendCEBooksAmount($player, "exclusive", "Exclusive Custom Enchant Book - \nPer Cost - 500,000 XP");
                        break;
                    case 5:
                        $this->sendCEBooksAmount($player, "ancient", "Ancient Custom Enchant Book - \nPer Cost - 10,000,000 XP");
                        break;
                    case 6:
                        $error = "VAULTED CEs are only available via Godly Relic! They cant be obtained via Books or envoys, their production is stopped but they still work.";
                        $this->sendResultForm($player, $error, "sendCEBooksMenu");
                        break;
                    default:
                        $this->sendShopMainMenu($player);
                        break;
                }
            }
        };
        $title = "§4§lCEBooks";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Your XP - §f" . number_format($user->getXP()), $buttons, $func);
    }

    public function sendShopMainMenu(Player $player) : void {
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        $buttons = ["§aBlocks", "§4Mana Shop", "§eMobCoin Shop", "§fEquipments", "§eFurniture", "§dItems", "§4CE Books", "§3Spawners", "§aPets", "§6Enchants", "§9Potions"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch (++$data) {
                    case 1:
                        $this->sendBlocksMenu($player);
                        break;
                    case 2:
                        $this->sendManaShopMenu($player);
                        break;
                    case 3:
                        $this->sendMobCoinShopMenu($player);
                        break;
                    case 4:
                        $this->sendEquipmentsMenu($player);
                        break;
                    case 5:
                        $this->sendFurnitureMenu($player);
                        break;
                    case 6:
                        $this->sendItemsMenu($player);
                        break;
                    case 7:
                        $this->sendCEBooksMenu($player);
                        break;
                    case 8:
                        $this->sendSpawnerMainMenu($player);
                        break;
                    case 9:
                        $this->sendPetMenu($player);
                        break;
                    case 10:
                        $this->sendEnchantMainMenu($player);
                        break;
                    case 11:
                        $this->sendPotionsMenu($player);
                        break;
                    default:
                        break;
                }
            }
        };
        $title = "§l§bShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Your money - §f" . number_format($user->getMoney()) . "$", $buttons, $func);
    }

    public function sendBlocksMenu(Player $player) : void {
        $buttons = ["Back", "Wood", "Stone", "Dirt", "Coarse Dirt", "Podzol", "Sand", "TNT", "Obsidian", "Hopper", "EnderChest", "Chest", "Furnace", "Clay", "Soul Sand", "Glowstone", "Bone Block", "Sea Lantern"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendWoodsMenu($player);
                        break;
                    case 2:
                        $this->sendStoneBlocksMenu($player);
                        break;
                    case 3:
                        $this->sendAmountWindow($player, "dirt");
                        break;
                    case 4:
                        $this->sendAmountWindow($player, "coarse_dirt");
                        break;
                    case 6:
                        $this->sendAmountWindow($player, "podzol");
                        break;
                    case 7:
                        $this->sendSandBlocksMenu($player);
                        break;
                    case 8:
                        $this->sendAmountWindow($player, "tnt");
                        break;
                    case 9:
                        $this->sendAmountWindow($player, "obsidian");
                        break;
                    case 10:
                        $this->sendAmountWindow($player, "hopper");
                        break;
                    case 11:
                        $this->sendAmountWindow($player, "ender_chest");
                        break;
                    case 12:
                        $this->sendAmountWindow($player, "chest");
                        break;
                    case 13:
                        $this->sendAmountWindow($player, "furnace");
                        break;
                    case 14:
                        $this->sendAmountWindow($player, "clay");
                        break;
                    case 15:
                        $this->sendAmountWindow($player, "soul_sand");
                        break;
                    case 16:
                        $this->sendAmountWindow($player, "glowstone");
                        break;
                    case 17:
                        $this->sendAmountWindow($player, "bone_block");
                        break;
                    case 18:
                        $this->sendAmountWindow($player, "sea_lantern");
                        break;
                    default:
                        $this->sendShopMainMenu($player);
                        break;
                }
            }
        };
        $title = "§l§bShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Category -", $buttons, $func);
    }

    public function sendWoodsMenu(Player $player) : void {
        $buttons = [
            "Back", "Oak Wood", "Spruce Wood", "Birch Wood", "Jungle Wood", "Acacia Wood", "Dark Oak Wood",
            "Mangrove Wood", "Cherry Wood", "Warped Stem", "Crimson Stem"
        ];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, "minecraft:oak_log");
                        break;
                    case 2:
                        $this->sendAmountWindow($player, "spruce_log");
                        break;
                    case 3:
                        $this->sendAmountWindow($player, "birch_log");
                        break;
                    case 4:
                        $this->sendAmountWindow($player, "jungle_log");
                        break;
                    case 5:
                        $this->sendAmountWindow($player, "acacia_log");
                        break;
                    case 6:
                        $this->sendAmountWindow($player, "dark_oak_log");
                        break;
                    case 7:
                        $this->sendAmountWindow($player, "mangrove_log");
                        break;
                    case 8:
                        $this->sendAmountWindow($player, "cherry_log");
                        break;
                    case 9:
                        $this->sendAmountWindow($player, "warped_stem");
                        break;
                    case 10:
                        $this->sendAmountWindow($player, "crimson_stem");
                        break;
                    default:
                        $this->sendBlocksMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Choose a wood type -", $buttons, $func);
    }

    public function sendAmountWindow(Player $player, /*String*/ $namespace, int $damage = 0) : void {
        if (is_numeric($namespace)) {
            try {
                $namespace = str_replace(" ", "_", LegacyStringToItemParser::getInstance()->parse($namespace . ":" . $damage)->getVanillaName());
            } catch (LegacyStringToItemParserException) {
                $this->pl->getLogger()->critical("the namespace \'" . $namespace . "\' is not a string or a legacy id int!");
                $player->sendMessage("§cERROR, please report this bug to staff! {0-" . $namespace . "}");

            }
        } elseif (!is_string($namespace)) {
            $this->pl->getLogger()->critical("the namespace \'" . $namespace . "\' is not a string or a legacy id int!");
            $player->sendMessage("§cERROR, please report this bug to staff! {0-" . $namespace . "}");
            return;
        }
        $item = StringToItemParser::getInstance()->parse($namespace);
        $form = new CustomForm(null);
        $title = "§b§lShop";
        if ($item instanceof Potion) {
            $item->setType(PotionTypeIdMap::getInstance()->fromId($damage));
        }
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $form->setTitle($title);
        if (!$item) {
            $player->sendMessage("§cERROR, please report this bug to staff! {1-" . $namespace . "}");
            return;
        }
        $form->addLabel("Please choose how much '" . $item->getName() . "' you want to buy?");
        $form->addInput("Amount -", "", "1");
        $form->setCallable(function(Player $player, ?array $data) use ($item, $namespace, $damage) : void {
            if ($data !== null) {
                if (!is_int((int) $data[1]) or empty($data[1])) {
                    $error = "§6Please enter a number!";
                    $this->sendResultForm($player, $error, "sendAmountWindow", [$namespace, $damage]);
                    return;
                }
                $data[1] = (int) $data[1];
                $amount = (int) $data[1];
                if ($amount < 1 or $amount > 2304) { // 2304 - full inv
                    $error = "§6Please enter a number greater than 0 and less than 2304!";
                    $this->sendResultForm($player, $error, "sendAmountWindow", [$namespace, $damage]);
                    return;
                }
                $this->sendConfirmWindow($player, $item, $amount);
            }
        }
        );
        $player->sendForm($form);
    }

    public function sendAmountWindowForItem(Player $player, Item $item) {
        $form = new CustomForm(null);
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $form->setTitle($title);
        $form->addLabel("Please choose how much '" . $item->getName() . "' you want to buy?");
        $form->addInput("Amount -", "", "1");
        $form->setCallable(function(Player $player, ?array $data) use ($item) : void {
            if ($data !== null) {
                if (!is_int((int) $data[1]) or empty($data[1])) {
                    $error = "§6Please enter a number!";
                    $this->sendResultForm($player, $error, "sendAmountWindowForItem", [$item]);
                    return;
                }
                $data[1] = (int) $data[1];
                $amount = (int) $data[1];
                if ($amount < 1 or $amount > 2304) { // 2304 - full inv
                    $error = "§6Please enter a number greater than 0 and less than 2304!";
                    $this->sendResultForm($player, $error, "sendAmountWindowForItem", [$item]);
                    return;
                }
                $this->sendConfirmWindow($player, $item, $amount);
            }
        }
        );
        $player->sendForm($form);
    }

    /**
     * @param string $name
     * @param int    $damage
     *
     * @return int|null
     */
    public function getShopMoneyData(string $name, int $damage = 0) : ?int {
        $namespace = "minecraft:" . strtolower($name);
        $namespace = str_replace(" ", "_", $namespace);

        $permoney = null;
        if ($damage === 0) {
            if ($this->pl->shop->exists($name)) {
                $permoney = $this->pl->shop->get($name, null);
            } elseif ($this->pl->shop->exists($namespace)) {
                $permoney = $this->pl->shop->get($namespace, null);
            } else {
                $this->pl->getLogger()->critical("Item in shop not found! {" . $namespace . "}");
                return 0;
            }
        } else {
            if ($this->pl->shop->exists($namespace)) {
                $permoney = $this->pl->shop->get($namespace, null);
            }
        }
        return $permoney;
    }

    public function sendConfirmWindow(Player $player, Item $item, int $amount) {
        $item = $item->setCount($amount);
        if (($permoney = $this->getShopMoneyData($item->getVanillaName())) === null) return;
        $money = $permoney * ($amount);
        if ($money == 0) {
            $player->sendMessage("§cERROR - Report this item to staff! {{$item->getVanillaName()} => price not found}");
            return;
        }
        $message = "Are you sure you wanna buy x" . $amount . " " . $item->getName() . " for " . $money . "$ (at $permoney$ each)";
        $func = function(Player $player, ?bool $data) use ($item, $amount, $money) : void {
            if ($data !== null) {
                if ($data) {
                    if ($player->getWorld()->getDisplayName() === "PvP") {
                        $player->sendMessage("§cYou can't shop here!");
                        return;
                    }
                    //                    $item = ItemFactory::getInstance()->get($id, $damage, $amount);
                    if (!$player->getInventory()->canAddItem($item)) {
                        $error = "§6Your Inventory is not empty enough to buy that much amount of Items!";
                        $this->sendResultForm($player, $error, "sendAmountWindowForItem", [$item]);
                        return;
                    }
                    $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                    if (!$user->removeMoney($money)) {
                        $error = "§6You don't have enough money to purchase that much! Required money: " . $money . "$ Check your money by /mymoney";
                        $this->sendResultForm($player, $error, "sendConfirmWindow", [$item, $amount]);
                        return;
                    }
                    $player->getInventory()->addItem($item);
                    $iname = $item->getName();
                    $label = "Transaction Successful! Successfully bought $iname x " . $amount . " for " . $money . "$!\nDo you want to continue shopping?";
                } else {
                    $label = "Do you want to continue shopping?";
                }
                $this->sendResultForm($player, $label, "sendShopMainMenu");
            }
        };
        $this->ff->sendModalForm($player, "§e§lCheckout:", $message, ["Yes", "No"], $func);
    }

    public function sendStoneBlocksMenu(Player $player) {
        $buttons = ["Back", "Stone", "Granite", "Diorite", "Andesite", "Gravel", "Blackstone", "Deepslate", "Basalt", "Tuff", "Cobbled Deepslate", "Mud"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, "stone");
                        break;
                    case 2:
                        $this->sendAmountWindow($player, "granite");
                        break;
                    case 3:
                        $this->sendAmountWindow($player, "diorite");
                        break;
                    case 4:
                        $this->sendAmountWindow($player, "andesite");
                        break;
                    case 5:
                        $this->sendAmountWindow($player, "gravel");
                        break;
                    case 6:
                        $this->sendAmountWindow($player, "blackstone");
                        break;
                    case 7:
                        $this->sendAmountWindow($player, "deepslate");
                        break;
                    case 8:
                        $this->sendAmountWindow($player, "basalt");
                        break;
                    case 9:
                        $this->sendAmountWindow($player, "tuff");
                        break;
                    case 10:
                        $this->sendAmountWindow($player, "cobbled_deepslate");
                        break;
                    case 11:
                        $this->sendAmountWindow($player, "mud");
                        break;
                    default:
                        $this->sendBlocksMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Choose a stone type -", $buttons, $func);
    }

    public function sendSandBlocksMenu(Player $player) {
        $buttons = ["Back", "Sand", "Red Sand", "Sand Stone"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, "sand");
                        break;
                    case 2:
                        $this->sendAmountWindow($player, "red_sand");
                        break;
                    case 3:
                        $this->sendAmountWindow($player, "sand_stone");
                        break;
                    default:
                        $this->sendBlocksMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Choose a sand type -", $buttons, $func);
    }

    public function sendMobCoinShopMenu(Player $player) : void {
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        $buttons = ["Back", "Surge Scroll", "Renew Scroll"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                $title = "Purchase Scrolls?";
                switch ($data) {
                    case 1:
                        $cost = 1000;
                        $info = "§aSurge Scroll -\n- This scroll will increase the max fix limit of an item by 1.";
                        $item = $this->pl->getScrolls("surge");
                        break;
                    case 2:
                        $cost = 5000;
                        $info = "§aRenew Scroll -\n- This scroll will renew the tool or armor to its default fix limits without affecting any enchants or stats.";
                        $item = $this->pl->getScrolls("renew");
                        break;
                    default:
                        $this->sendShopMainMenu($player);
                        return;
                }
                $this->sendMobCoinShopAmount($player, $title, $info, $cost, $item);
            }
        };
        $title = "§6§lMobCoin Shop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Your coins - §f" . number_format($user->getMobCoin()), $buttons, $func);
    }

    public function sendMobCoinShopAmount(Player $player, string $title, string $info, int $cost, Item $item) : void {
        $form = new CustomForm(null);
        $form->setTitle($title);
        $form->addLabel("Please enter Amount. 1 - 2304");
        $form->addInput("Amount -", "", "1");
        $form->setCallable(function(Player $player, ?array $data) use ($title, $info, $item, $cost) : void {
            if ($data !== null) {
                if (!is_int((int) $data[1]) or empty($data[1])) {
                    $player->sendMessage("§6Please enter a number in amount.");
                    return;
                }
                $data[1] = (int) $data[1];
                $amount = $data[1];
                if ($amount < 1 or $amount > 2304) { // 2304 - full inv
                    $player->sendMessage("§6Please enter a number greater than 0 and less than 2304.");
                    return;
                }
                $amount = $data[1];
                if ($item instanceof Item) {
                    $clone = clone $item;
                    $clone->setCount($amount);
                    $item = $clone;
                    $cost *= $amount; // necessary
                } else {
                    return;
                }
                $this->sendMobCoinConfirmMenu($player, $item, $title, $info, $cost);
            }
        }
        );
        $player->sendForm($form);
    }

    public function sendMobCoinConfirmMenu(Player $player, Item $item, string $title, string $info, int $cost) : void {
        $func = function(Player $player, ?bool $data) use ($item, $cost) : void {
            if ($data !== null) {
                if ($data) {
                    if (!$player->getInventory()->canAddItem($item)) {
                        $player->sendMessage("§cYou dont have enough space in your inventory to get that item.");
                        return;
                    }
                    $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                    if (!$user->hasMobCoin($cost)) {
                        $player->sendMessage("§cYou dont have §6$cost mob coins §crequired to purchase that item.");
                    } else {
                        $player->getInventory()->addItem($item);
                        $user->removeMobCoin($cost);
                        $player->sendMessage("- §aSuccessfully purchased item for §6" . number_format($cost) . " mob coins.");
                    }
                } else {
                    $this->sendMobCoinShopMenu($player);
                }
            }
        };
        $this->ff->sendModalForm($player, $title, $info . "\n§6Total Price - §f" . $cost . " coins", ["Purchase", "Go Back"], $func);
    }

    public function sendManaShopMenu(Player $player) : void {
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        $buttons = ["Back", "Scrolls", "OreGens", "Catalyst {FREE}", "AutoMiner", "AutoSeller - Money", "AutoSeller - XP"];
        //$buttons = ["Back", "OreGens"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendScrollManaMenu($player);
                        break;
                    case 2:
                        //                        $this->sendOregenManaMenu($player);
                        $this->sendOregenUpgrade($player);
                        break;
                    case 3:
                        $cost = 0;
                        $info = "§mCatalyst - \n§aPlace this near water with an air blocks surrounding it to generate blocks.\nThis block works as a 1:1 replacement for lava generators.";
                        $this->sendManaShopAmount($player, "Purchase Catalyst?", $info, $cost, $this->pl->getEvFunctions()->getCatalystBlock());
                    case 4:
                        $cost = 5000;
                        $info = "§aAutoMiner -\n- Auto Mines the ore beneath it and stores items in the chest above it, it works better if oregen's level matches with auto miner's level.\n- Auto adds the ore's mana and xp to the islands owner\n- Use /upgrade to uprade AutoMiner block.\n§eSmelting is inbuilt";
                        $this->sendManaShopAmount($player, "Purchase AutoMiner?", $info, $cost, $this->pl->getEvFunctions()->getAutoMinerBlock(1));
                        break;
                    case 5:
                        $cost = 10000;
                        $info = "§aAutoSeller -\nType - Money\n- Auto Sells items for money from the chest below.\n- Selling strength depends on level.\n- Auto adds the money to the islands receiver\n- Use /upgrade to uprade AutoSeller block.";
                        $this->sendManaShopAmount($player, "Purchase AutoSeller - Money?", $info, $cost, $this->pl->getEvFunctions()->getAutoSellerBlock(1));
                        break;
                    case 6:
                        $info = "§aAutoSeller -\nType - XP\n- Auto Sells items for XP from the chest below.\n- Selling strength depends on level.\n- Auto adds the XP to the islands receiver\n- Use /upgrade to uprade AutoSeller block.";
                        $cost = 10000;
                        $this->sendManaShopAmount($player, "Purchase AutoSeller - XP?", $info, $cost, $this->pl->getEvFunctions()->getAutoSellerBlock(1, AutoSellerTile::TAG_TYPE_XP));
                        break;
                    default:
                        $this->sendShopMainMenu($player);
                        break;
                }
            }
        };
        $title = "§e§lMana Shop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Your mana - §f" . number_format($user->getMana()), $buttons, $func);
    }

    public function sendScrollManaMenu(Player $player) : void {
        $buttons = ["Back", "Levelup", "Enchanter", "GOD", "Inferno", "Fixer", "Vulcan", "Carver"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                $title = "Purchase Scroll?";
                switch ($data) {
                    case 1:
                        $cost = 1000;
                        $info = "§aLevel Up Scroll -\n- This scroll Levels up your CE till Level 6. Use GOD Scroll after CE reaches Level 6 to make it Level 10.";
                        $item = $this->pl->getScrolls();
                        break;
                    case 2:
                        $cost = 1500;
                        $info = "§aEnchanter Scroll -\n- Use this scroll on a CE Book to increase its accuracy.";
                        $item = $this->pl->getScrolls("enchanter");
                        break;
                    case 3:
                        $cost = 2000;
                        $info = "§aGOD Scroll -\n- This scroll levels up your CE till max level 10. Only applicable if the CE is level 6, use Level up scroll to level up CE to 6";
                        $item = $this->pl->getScrolls("god");
                        break;
                    case 4:
                        $cost = 2000;
                        $info = "§aInferno Scroll -\n- This scroll levels up your vanilla enchants till max level 10.";
                        $item = $this->pl->getScrolls("inferno");
                        break;
                    case 5:
                        $cost = 1000;
                        $info = "§aFixer Scroll -\n- This scroll fixes the held item.";
                        $item = $this->pl->getScrolls("fixer");
                        break;
                    case 6:
                        $cost = 2500;
                        $info = "§aVulcan Scroll -\n- This scroll removes CE from tool and you get it as CE Book with random accuracy.";
                        $item = $this->pl->getScrolls("vulcan");
                        break;
                    case 7:
                        $cost = 2000;
                        $info = "§aCarver Scroll -\n- This scroll removes Vanilla ench from tool and you get it as Ench Orb.";
                        $item = $this->pl->getScrolls("carver");
                        break;
                    default:
                        $this->sendManaShopMenu($player);
                        return;
                }
                $this->sendManaShopAmount($player, $title, $info, $cost, $item);
            }
        };
        $title = "§e§lScroll Shop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Scroll type -", $buttons, $func);
    }

    public function sendManaShopAmount(Player $player, string $title, string $info, int $cost, Item $item) : void {
        $form = new CustomForm(null);
        $form->setTitle($title);
        $form->addLabel("Please enter Amount. 1 - 2304");
        $form->addInput("Amount -", "", "1");
        $form->setCallable(function(Player $player, ?array $data) use ($title, $info, $item, $cost) : void {
            if ($data !== null) {
                if (!is_int((int) $data[1]) or empty($data[1])) {
                    $player->sendMessage("§6Please enter a number in amount.");
                    return;
                }
                $data[1] = (int) $data[1];
                $amount = (int) $data[1];
                if ($amount < 1 or $amount > 2304) { // 2304 - full inv
                    $player->sendMessage("§6Please enter a number greater than 0 and less than 2304.");
                    return;
                }
                $amount = $data[1];
                if ($item instanceof Item) {
                    $clone = clone $item;
                    $clone->setCount($amount);
                    $item = $clone;
                    $cost *= $amount; // necessary
                } else {
                    return;
                }
                $this->sendManaConfirmMenu($player, $item, $title, $info, $cost);
            }
        }
        );
        $player->sendForm($form);
    }

    public function sendManaConfirmMenu(Player $player, Item $item, string $title, string $info, int $cost) : void {
        $func = function(Player $player, ?bool $data) use ($item, $cost) : void {
            if ($data !== null) {
                if ($data) {
                    if (!$player->getInventory()->canAddItem($item)) {
                        $player->sendMessage("§cYou dont have enough space in your inventory to get that item.");
                        return;
                    }
                    $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                    if (!$user->hasMana($cost)) {
                        $player->sendMessage("§cYou dont have §6$cost mana §crequired to purchase that item.");
                    } else {
                        $player->getInventory()->addItem($item);
                        $user->removeMana($cost);
                        $player->sendMessage("- §aSuccessfully purchased item for §6$cost mana.");
                    }
                } else {
                    $this->sendManaShopMenu($player);
                }
            }
        };
        $this->ff->sendModalForm($player, $title, $info . "\n§6Total Price - §f" . $cost . " mana", ["Purchase", "Go Back"], $func);
    }

    public function sendEquipmentsMenu(Player $player) : void {
        $buttons = ["Back", "Elytra", "Helmet", "ChestPlate", "Leggings", "Boots", "Shovel", "Pickaxe", "Axe", "Sword", "Hoe", "Bow", "Saddle"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, "elytra");
                        break;
                    case 2:
                        $this->sendHelmetMenu($player);
                        break;
                    case 3:
                        $this->sendChestplateMenu($player);
                        break;
                    case 4:
                        $this->sendLeggingsMenu($player);
                        break;
                    case 5:
                        $this->sendBootsMenu($player);
                        break;
                    case 6:
                        $this->sendShovelMenu($player);
                        break;
                    case 7:
                        $this->sendPickaxeMenu($player);
                        break;
                    case 8:
                        $this->sendAxeMenu($player);
                        break;
                    case 9:
                        $this->sendSwordMenu($player);
                        break;
                    case 10:
                        $this->sendHoeMenu($player);
                        break;
                    case 11:
                        $this->sendAmountWindow($player, "bow");
                        break;
                    case 12:
                        $this->sendAmountWindow($player, "saddle");
                        break;
                    default:
                        $this->sendShopMainMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Category -", $buttons, $func);
    }

    public function sendHelmetMenu(Player $player) : void {
        $buttons = ["Back", "Leather Helmet", "Golden Helmet", "Chainmail Helmet", "Iron Helmet", "Diamond Helmet"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, ItemTypeNames::LEATHER_HELMET);
                        break;
                    case 2:
                        $this->sendAmountWindow($player, ItemTypeNames::GOLDEN_HELMET);
                        break;
                    case 3:
                        $this->sendAmountWindow($player, ItemTypeNames::CHAINMAIL_HELMET);
                        break;
                    case 4:
                        $this->sendAmountWindow($player, ItemTypeNames::IRON_HELMET);
                        break;
                    case 5:
                        $this->sendAmountWindow($player, ItemTypeNames::DIAMOND_HELMET);
                        break;
                    default:
                        $this->sendEquipmentsMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Helmet type -", $buttons, $func);
    }

    public function sendChestplateMenu(Player $player) : void {
        $buttons = ["Back", "Leather Chestplate", "Golden Chestplate", "Chainmail Chestplate", "Iron Chestplate", "Diamond Chestplate"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, ItemTypeNames::LEATHER_CHESTPLATE);
                        break;
                    case 2:
                        $this->sendAmountWindow($player, ItemTypeNames::GOLDEN_CHESTPLATE);
                        break;
                    case 3:
                        $this->sendAmountWindow($player, ItemTypeNames::CHAINMAIL_CHESTPLATE);
                        break;
                    case 4:
                        $this->sendAmountWindow($player, ItemTypeNames::IRON_CHESTPLATE);
                        break;
                    case 5:
                        $this->sendAmountWindow($player, ItemTypeNames::DIAMOND_CHESTPLATE);
                        break;
                    default:
                        $this->sendEquipmentsMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Chestplate type -", $buttons, $func);
    }

    public function sendLeggingsMenu(Player $player) : void {
        $buttons = ["Back", "Leather Leggings", "Golden Leggings", "Chainmail Leggings", "Iron Leggings", "Diamond Leggings"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, ItemTypeNames::LEATHER_LEGGINGS);
                        break;
                    case 2:
                        $this->sendAmountWindow($player, ItemTypeNames::GOLDEN_LEGGINGS);
                        break;
                    case 3:
                        $this->sendAmountWindow($player, ItemTypeNames::CHAINMAIL_LEGGINGS);
                        break;
                    case 4:
                        $this->sendAmountWindow($player, ItemTypeNames::IRON_LEGGINGS);
                        break;
                    case 5:
                        $this->sendAmountWindow($player, ItemTypeNames::DIAMOND_LEGGINGS);
                        break;
                    default:
                        $this->sendEquipmentsMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Leggings type -", $buttons, $func);
    }

    public function sendBootsMenu(Player $player) : void {
        $buttons = ["Back", "Leather Boots", "Golden Boots", "Chainmail Boots", "Iron Boots", "Diamond Boots"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, ItemTypeNames::LEATHER_BOOTS);
                        break;
                    case 2:
                        $this->sendAmountWindow($player, ItemTypeNames::GOLDEN_BOOTS);
                        break;
                    case 3:
                        $this->sendAmountWindow($player, ItemTypeNames::CHAINMAIL_BOOTS);
                        break;
                    case 4:
                        $this->sendAmountWindow($player, ItemTypeNames::IRON_BOOTS);
                        break;
                    case 5:
                        $this->sendAmountWindow($player, ItemTypeNames::DIAMOND_BOOTS);
                        break;
                    default:
                        $this->sendEquipmentsMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Boots type -", $buttons, $func);
    }

    public function sendShovelMenu(Player $player) : void {
        $buttons = ["Back", "Wooden Shovel", "Golden Shovel", "Stone Shovel", "Iron Shovel", "Diamond Shovel"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, ItemTypeNames::WOODEN_SHOVEL);
                        break;
                    case 2:
                        $this->sendAmountWindow($player, ItemTypeNames::GOLDEN_SHOVEL);
                        break;
                    case 3:
                        $this->sendAmountWindow($player, ItemTypeNames::STONE_SHOVEL);
                        break;
                    case 4:
                        $this->sendAmountWindow($player, ItemTypeNames::IRON_SHOVEL);
                        break;
                    case 5:
                        $this->sendAmountWindow($player, ItemTypeNames::DIAMOND_SHOVEL);
                        break;
                    default:
                        $this->sendEquipmentsMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Shovel type -", $buttons, $func);
    }

    public function sendPickaxeMenu(Player $player) : void {
        $buttons = ["Back", "Wooden Pickaxe", "Golden Pickaxe", "Stone Pickaxe", "Iron Pickaxe", "Diamond Pickaxe"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, ItemTypeNames::WOODEN_PICKAXE);
                        break;
                    case 2:
                        $this->sendAmountWindow($player, ItemTypeNames::GOLDEN_PICKAXE);
                        break;
                    case 3:
                        $this->sendAmountWindow($player, ItemTypeNames::STONE_PICKAXE);
                        break;
                    case 4:
                        $this->sendAmountWindow($player, ItemTypeNames::IRON_PICKAXE);
                        break;
                    case 5:
                        $this->sendAmountWindow($player, ItemTypeNames::DIAMOND_PICKAXE);
                        break;
                    default:
                        $this->sendEquipmentsMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Pickaxe type -", $buttons, $func);
    }

    public function sendAxeMenu(Player $player) : void {
        $buttons = ["Back", "Wooden Axe", "Golden Axe", "Stone Axe", "Iron Axe", "Diamond Axe"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, ItemTypeNames::WOODEN_AXE);
                        break;
                    case 2:
                        $this->sendAmountWindow($player, ItemTypeNames::GOLDEN_AXE);
                        break;
                    case 3:
                        $this->sendAmountWindow($player, ItemTypeNames::STONE_AXE);
                        break;
                    case 4:
                        $this->sendAmountWindow($player, ItemTypeNames::DIAMOND_AXE);
                        break;
                    case 5:
                        $this->sendAmountWindow($player, 279);
                        break;
                    default:
                        $this->sendEquipmentsMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Axe type -", $buttons, $func);
    }

    public function sendSwordMenu(Player $player) : void {
        $buttons = ["Back", "Wooden Sword", "Golden Sword", "Stone Sword", "Iron Sword", "Diamond Sword"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, ItemTypeNames::WOODEN_SWORD);
                        break;
                    case 2:
                        $this->sendAmountWindow($player, ItemTypeNames::GOLDEN_SWORD);
                        break;
                    case 3:
                        $this->sendAmountWindow($player, ItemTypeNames::STONE_SWORD);
                        break;
                    case 4:
                        $this->sendAmountWindow($player, ItemTypeNames::IRON_SWORD);
                        break;
                    case 5:
                        $this->sendAmountWindow($player, ItemTypeNames::DIAMOND_SWORD);
                        break;
                    default:
                        $this->sendEquipmentsMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Sword type -", $buttons, $func);
    }

    public function sendHoeMenu(Player $player) : void {
        $buttons = ["Back", "Wooden Hoe", "Golden Hoe", "Stone Hoe", "Iron Hoe", "Diamond Hoe"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, ItemTypeNames::WOODEN_HOE);
                        break;
                    case 2:
                        $this->sendAmountWindow($player, ItemTypeNames::GOLDEN_HOE);
                        break;
                    case 3:
                        $this->sendAmountWindow($player, ItemTypeNames::STONE_HOE);
                        break;
                    case 4:
                        $this->sendAmountWindow($player, ItemTypeNames::IRON_HOE);
                        break;
                    case 5:
                        $this->sendAmountWindow($player, ItemTypeNames::DIAMOND_HOE);
                        break;
                    default:
                        $this->sendEquipmentsMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Hoe type -", $buttons, $func);
    }

    public function sendFurnitureMenu(Player $player) : void {
        $buttons = ["Back", "Sign", "Still Water", "Still Lava", "Item Frame", "Glow Item Frame", "Grass block", "Hay Bale", "Flowers Menu", "Brown Mushroom Block", "Red Mushroom Block", "Lit Redstone Lamp", "Stone Brick", "Dragon Egg", "Dragon Head", "NoteBlock", "JukeBox", "End Rod", "Sponge", "Wool", "Bookshelf", "Stained Clay", "Snow Block", "Prismarine", "Magma Block", "Glazed Terracotta", "Concrete", "Mycelium", "Stained Glass", "Stained Glass Pane", "Brick Block", "Nether Wart Block", "Purpur Block", "Leaves", "Ice Block", "Packed Ice", "Slime Block", "Nether Brick Block", "CobWeb"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, BlockTypeNames::STANDING_SIGN);
                        break;
                    case 2:
                        $this->sendAmountWindow($player, BlockTypeNames::WATER);
                        break;
                    case 3:
                        $this->sendAmountWindow($player, BlockTypeNames::LAVA);
                        break;
                    case 4:
                        $this->sendAmountWindow($player, BlockTypeNames::FRAME);
                        break;
                    case 5:
                        $this->sendAmountWindow($player, BlockTypeNames::GLOW_FRAME);
                        break;
                    case 6:
                        $this->sendAmountWindow($player, BlockTypeNames::GRASS_BLOCK);
                        break;
                    case 7:
                        $this->sendAmountWindow($player, BlockTypeNames::HAY_BLOCK);
                        break;
                    case 8:
                        $this->sendFlowerMenu($player);
                        break;
                    case 9:
                        $this->sendAmountWindow($player, BlockTypeNames::BROWN_MUSHROOM_BLOCK);
                        break;
                    case 10:
                        $this->sendAmountWindow($player, BlockTypeNames::RED_MUSHROOM_BLOCK);
                        break;
                    case 11:
                        $this->sendAmountWindow($player, BlockTypeNames::LIT_REDSTONE_LAMP);
                        break;
                    case 12:
                        $this->sendAmountWindow($player, BlockTypeNames::STONE_BRICKS);
                        break;
                    case 13:
                        $this->sendAmountWindow($player, BlockTypeNames::DRAGON_EGG);
                        break;
                    case 14:
                        $this->sendAmountWindow($player, "dragon_head");
                        break;
                    case 15:
                        $this->sendAmountWindow($player, BlockTypeNames::NOTEBLOCK);
                        break;
                    case 16:
                        $this->sendAmountWindow($player, BlockTypeNames::JUKEBOX);
                        break;
                    case 17:
                        $this->sendAmountWindow($player, BlockTypeNames::END_ROD);
                        break;
                    case 18:
                        $this->sendAmountWindow($player, BlockTypeNames::SPONGE);
                        break;
                    case 19:
                        $this->sendWoolBlocksMenu($player);
                        break;
                    case 20:
                        $this->sendAmountWindow($player, BlockTypeNames::BOOKSHELF);
                        break;
                    case 21:
                        $this->sendTerracottaMenu($player);
                        break;
                    case 22:
                        $this->sendAmountWindow($player, BlockTypeNames::SNOW);
                        break;
                    case 23:
                        $this->sendPrismarineMenu($player);
                        break;
                    case 24:
                        $this->sendAmountWindow($player, BlockTypeNames::MAGMA);
                        break;
                    case 25:
                        $this->sendGlazedTerracottaMenu($player);
                        break;
                    case 26:
                        $this->sendConcreteMenu($player);
                        break;
                    case 27:
                        $this->sendAmountWindow($player, BlockTypeNames::MYCELIUM);
                        break;
                    case 28:
                        $this->sendStainedGlassMenu($player);
                        break;
                    case 29:
                        $this->sendStainedGlassPaneMenu($player);
                        break;
                    case 30:
                        $this->sendAmountWindow($player, BlockTypeNames::BRICK_BLOCK);
                        break;
                    case 31:
                        $this->sendAmountWindow($player, BlockTypeNames::NETHER_WART);
                        break;
                    case 32:
                        $this->sendAmountWindow($player, BlockTypeNames::PURPUR_BLOCK);
                        break;
                    case 33:
                        $this->sendLeavesMenu($player);
                        break;
                    case 34:
                        $this->sendAmountWindow($player, BlockTypeNames::ICE);
                        break;
                    case 35:
                        $this->sendAmountWindow($player, BlockTypeNames::PACKED_ICE);
                        break;
                    case 36:
                        $this->sendAmountWindow($player, BlockTypeNames::SLIME);
                        break;
                    case 37:
                        $this->sendAmountWindow($player, BlockTypeNames::NETHER_BRICK);
                        break;
                    case 38:
                        $this->sendAmountWindow($player, "cobweb");
                        break;
                    default:
                        $this->sendShopMainMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Category -", $buttons, $func);
    }

    public function sendFlowerMenu(Player $player) : void {
        $buttons = ["Back", "Flower Pot", "Poppy", "Lily Pad", "Blue Orchid", "Allium", "Azure Bluet", "Red Tulip", "Orange Tulip", "White Tulip", "Pink Tulip", "Oxeye Daisy", "Dandelion", "Sunflower", "Lilac", "Grass", "Double Tallgrass", "Fern", "Large Fern", "Dead Bush", "Rose Bush", "Peony", "Vines", "Brown Mushroom", "Red Mushroom"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, 390);
                        break;
                    case 2:
                        $this->sendAmountWindow($player, 38);
                        break;
                    case 3:
                        $this->sendAmountWindow($player, 111);
                        break;
                    case 4:
                        $this->sendAmountWindow($player, 38, 1);
                        break;
                    case 5:
                        $this->sendAmountWindow($player, 38, 2);
                        break;
                    case 6:
                        $this->sendAmountWindow($player, 38, 3);
                        break;
                    case 7:
                        $this->sendAmountWindow($player, 38, 4);
                        break;
                    case 8:
                        $this->sendAmountWindow($player, 38, 5);
                        break;
                    case 9:
                        $this->sendAmountWindow($player, 38, 6);
                        break;
                    case 10:
                        $this->sendAmountWindow($player, 38, 7);
                        break;
                    case 11:
                        $this->sendAmountWindow($player, 38, 8);
                        break;
                    case 12:
                        $this->sendAmountWindow($player, 37);
                        break;
                    case 13:
                        $this->sendAmountWindow($player, 175);
                        break;
                    case 14:
                        $this->sendAmountWindow($player, 175, 1);
                        break;
                    case 15:
                        $this->sendAmountWindow($player, 31, 1);
                        break;
                    case 16:
                        $this->sendAmountWindow($player, 175, 2);
                        break;
                    case 17:
                        $this->sendAmountWindow($player, 31, 2);
                        break;
                    case 18:
                        $this->sendAmountWindow($player, 175, 3);
                        break;
                    case 19:
                        $this->sendAmountWindow($player, 32);
                        break;
                    case 20:
                        $this->sendAmountWindow($player, 175, 4);
                        break;
                    case 21:
                        $this->sendAmountWindow($player, 175, 5);
                        break;
                    case 22:
                        $this->sendAmountWindow($player, 106);
                        break;
                    case 23:
                        $this->sendAmountWindow($player, 39);
                        break;
                    case 24:
                        $this->sendAmountWindow($player, 40);
                        break;
                    default:
                        $this->sendFurnitureMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Flower -", $buttons, $func);
    }

    public function sendWoolBlocksMenu(Player $player) : void {
        $buttons = ["Back", "White Wool", "Orange Wool", "Magenta Wool", "Light Blue Wool", "Yellow Wool", "Lime Wool", "Pink Wool", "Gray Wool", "Light Gray Wool", "Cyan Wool", "Purple Wool", "Blue Wool", "Brown Wool", "Green Wool", "Red Wool", "Black Wool"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::WHITE())->asItem());
                        break;
                    case 2:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::ORANGE())->asItem());
                        break;
                    case 3:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::MAGENTA())->asItem());
                        break;
                    case 4:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::LIGHT_BLUE())->asItem());
                        break;
                    case 5:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::YELLOW())->asItem());
                        break;
                    case 6:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::LIME())->asItem());
                        break;
                    case 7:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::PINK())->asItem());
                        break;
                    case 8:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::GRAY())->asItem());
                        break;
                    case 9:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::LIGHT_GRAY())->asItem());
                        break;
                    case 10:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::CYAN())->asItem());
                        break;
                    case 11:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::PURPLE())->asItem());
                        break;
                    case 12:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::BLUE())->asItem());
                        break;
                    case 13:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::BROWN())->asItem());
                        break;
                    case 14:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::GREEN())->asItem());
                        break;
                    case 15:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::RED())->asItem());
                        break;
                    case 16:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::BLACK())->asItem());
                        break;
                    default:
                        $this->sendFurnitureMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Wool type -", $buttons, $func);
    }

    public function sendTerracottaMenu(Player $player) : void {
        $buttons = ["Back", "White Stained Clay", "Orange Stained Clay", "Magenta Stained Clay", "Light Blue Stained Clay", "Yellow Stained Clay", "Lime Stained Clay", "Pink Stained Clay", "Gray Stained Clay", "Light Gray Stained Clay", "Cyan Stained Clay", "Purple Stained Clay", "Blue Stained Clay", "Brown Stained Clay", "Green Stained Clay", "Red Stained Clay", "Black Stained Clay"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::WHITE())->asItem());
                        break;
                    case 2:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::ORANGE())->asItem());
                        break;
                    case 3:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::MAGENTA())->asItem());
                        break;
                    case 4:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::LIGHT_BLUE())->asItem());
                        break;
                    case 5:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::YELLOW())->asItem());
                        break;
                    case 6:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::LIME())->asItem());
                        break;
                    case 7:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::PINK())->asItem());
                        break;
                    case 8:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::GRAY())->asItem());
                        break;
                    case 9:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::LIGHT_GRAY())->asItem());
                        break;
                    case 10:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::CYAN())->asItem());
                        break;
                    case 11:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::PURPLE())->asItem());
                        break;
                    case 12:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::BLUE())->asItem());
                        break;
                    case 13:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::BROWN())->asItem());
                        break;
                    case 14:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::GREEN())->asItem());
                        break;
                    case 15:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::RED())->asItem());
                        break;
                    case 16:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::BLACK())->asItem());
                        break;
                    default:
                        $this->sendFurnitureMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Terracotta(Not glazed) type -", $buttons, $func);
    }

    public function sendPrismarineMenu(Player $player) : void {
        $buttons = ["Back", "Prismarine", "Prismarine Bricks", "Dark Prismarine"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, 168);
                        break;
                    case 2:
                        $this->sendAmountWindow($player, 168, 2);
                        break;
                    case 3:
                        $this->sendAmountWindow($player, 168, 1);
                        break;
                    default:
                        $this->sendFurnitureMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Prismarine type -", $buttons, $func);
    }

    public function sendGlazedTerracottaMenu(Player $player) : void {
        $buttons = ["Back", "Purple Glazed Terracotta", "White Glazed Terracotta", "Orange Glazed Terracotta", "Magenta Glazed Terracotta", "Light Blue Glazed Terracotta", "Yellow Glazed Terracotta", "Lime Glazed Terracotta", "Pink Glazed Terracotta", "Gray Glazed Terracotta", "Silver Glazed Terracotta", "Cyan Glazed Terracotta", "Blue Glazed Terracotta", "Brown Glazed Terracotta", "Green Glazed Terracotta", "Red Glazed Terracotta", "Black Glazed Terracotta"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::PURPLE)->asItem());
                        break;
                    case 2:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::WHITE)->asItem());
                        break;
                    case 3:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::ORANGE)->asItem());
                        break;
                    case 4:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::MAGENTA)->asItem());
                        break;
                    case 5:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::LIGHT_BLUE)->asItem());
                        break;
                    case 6:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::YELLOW)->asItem());
                        break;
                    case 7:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::LIME)->asItem());
                        break;
                    case 8:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::PINK)->asItem());
                        break;
                    case 9:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::GRAY)->asItem());
                        break;
                    case 10:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::LIGHT_GRAY)->asItem());
                        break;
                    case 11:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::CYAN)->asItem());
                        break;
                    case 12:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::BLUE)->asItem());
                        break;
                    case 13:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::BROWN)->asItem());
                        break;
                    case 14:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::GREEN)->asItem());
                        break;
                    case 15:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::RED)->asItem());
                        break;
                    case 16:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::BLACK)->asItem());
                        break;
                    default:
                        $this->sendFurnitureMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Glazed Terracotta type -", $buttons, $func);
    }

    public function sendConcreteMenu(Player $player) : void {
        $buttons = ["Back", "White Concrete", "Orange Concrete", "Magenta Concrete", "Light Blue Concrete", "Yellow Concrete", "Lime Concrete", "Pink Concrete", "Gray Concrete", "Light Gray Concrete", "Cyan Concrete", "Purple Concrete", "Blue Concrete", "Brown Concrete", "Green Concrete", "Red Concrete", "Black Concrete"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::WHITE)->asItem());
                        break;
                    case 2:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::ORANGE)->asItem());
                        break;
                    case 3:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::MAGENTA)->asItem());
                        break;
                    case 4:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::LIGHT_BLUE)->asItem());
                        break;
                    case 5:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::YELLOW)->asItem());
                        break;
                    case 6:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::LIME)->asItem());
                        break;
                    case 7:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::PINK)->asItem());
                        break;
                    case 8:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::GRAY)->asItem());
                        break;
                    case 9:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::LIGHT_GRAY)->asItem());
                        break;
                    case 10:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::CYAN)->asItem());
                        break;
                    case 11:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::PURPLE)->asItem());
                        break;
                    case 12:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::BLUE)->asItem());
                        break;
                    case 13:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::BROWN)->asItem());
                        break;
                    case 14:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::GREEN)->asItem());
                        break;
                    case 15:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::RED)->asItem());
                        break;
                    case 16:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::BLACK)->asItem());
                        break;
                    default:
                        $this->sendFurnitureMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Concrete type -", $buttons, $func);
    }

    public function sendStainedGlassMenu(Player $player) : void {
        $buttons = ["Back", "White Stained Glass", "Orange Stained Glass", "Magenta Stained Glass", "Light Blue Stained Glass", "Yellow Stained Glass", "Lime Stained Glass", "Pink Stained Glass", "Gray Stained Glass", "Light Gray Stained Glass", "Cyan Stained Glass", "Purple Stained Glass", "Blue Stained Glass", "Brown Stained Glass", "Green Stained Glass", "Red Stained Glass", "Black Stained Glass", "Glass Block"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE)->asItem());
                        break;
                    case 2:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::ORANGE)->asItem());
                        break;
                    case 3:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::MAGENTA)->asItem());
                        break;
                    case 4:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::LIGHT_BLUE)->asItem());
                        break;
                    case 5:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::YELLOW)->asItem());
                        break;
                    case 6:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::LIME)->asItem());
                        break;
                    case 7:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::PINK)->asItem());
                        break;
                    case 8:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GRAY)->asItem());
                        break;
                    case 9:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::LIGHT_GRAY)->asItem());
                        break;
                    case 10:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::CYAN)->asItem());
                        break;
                    case 11:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::PURPLE)->asItem());
                        break;
                    case 12:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLUE)->asItem());
                        break;
                    case 13:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BROWN)->asItem());
                        break;
                    case 14:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GREEN)->asItem());
                        break;
                    case 15:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED)->asItem());
                        break;
                    case 16:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK)->asItem());
                        break;
                    case 17:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::GLASS_PANE()->asItem());
                        break;
                    default:
                        $this->sendFurnitureMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Stained Glass type -", $buttons, $func);
    }

    public function sendStainedGlassPaneMenu(Player $player) : void {
        $buttons = ["Back", "White Stained Glass Pane", "Orange Stained Glass Pane", "Magenta Stained Glass Pane", "Light Blue Stained Glass Pane", "Yellow Stained Glass Pane", "Lime Stained Glass Pane", "Pink Stained Glass Pane", "Gray Stained Glass Pane", "Light Gray Stained Glass Pane", "Cyan Stained Glass Pane", "Purple Stained Glass Pane", "Blue Stained Glass Pane", "Brown Stained Glass Pane", "Green Stained Glass Pane", "Red Stained Glass Pane", "Black Stained Glass Pane", "Glass Pane"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::WHITE)->asItem());
                        break;
                    case 2:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::ORANGE)->asItem());
                        break;
                    case 3:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::MAGENTA)->asItem());
                        break;
                    case 4:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::LIGHT_BLUE)->asItem());
                        break;
                    case 5:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::YELLOW)->asItem());
                        break;
                    case 6:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::LIME)->asItem());
                        break;
                    case 7:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::PINK)->asItem());
                        break;
                    case 8:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY)->asItem());
                        break;
                    case 9:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::LIGHT_GRAY)->asItem());
                        break;
                    case 10:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::CYAN)->asItem());
                        break;
                    case 11:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::PURPLE)->asItem());
                        break;
                    case 12:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BLUE)->asItem());
                        break;
                    case 13:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BROWN)->asItem());
                        break;
                    case 14:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GREEN)->asItem());
                        break;
                    case 15:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::RED)->asItem());
                        break;
                    case 16:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BLACK)->asItem());
                        break;
                    case 17:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::GLASS_PANE()->asItem());
                        break;
                    default:
                        $this->sendFurnitureMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Stained Glass Pane type -", $buttons, $func);
    }

    public function sendLeavesMenu(Player $player) : void {
        $buttons = ["Back", "Oak Leaves", "Spruce Leaves", "Birch Leaves", "Jungle Leaves", "Acacia Leaves", "Dark Oak Leaves"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::OAK_LEAVES()->asItem());
                        break;
                    case 2:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::SPRUCE_LEAVES()->asItem());
                        break;
                    case 3:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::BIRCH_LEAVES()->asItem());
                        break;
                    case 4:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::JUNGLE_LEAVES()->asItem());
                        break;
                    case 5:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::ACACIA_LEAVES()->asItem());
                        break;
                    case 6:
                        $this->sendAmountWindowForItem($player, VanillaBlocks::DARK_OAK_LEAVES()->asItem());
                        break;
                    default:
                        $this->sendFurnitureMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Leaf type -", $buttons, $func);
    }

    public function sendItemsMenu(Player $player) : void {
        $buttons = ["Back", "Resources", "Food", "Farm", "Dyes", "Brewing items", "Arrow"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendResourceMenu($player);
                        break;
                    case 2:
                        $this->sendFoodMenu($player);
                        break;
                    case 3:
                        $this->sendFarmMenu($player);
                        break;
                    case 4:
                        $this->sendDyesMenu($player);
                        break;
                    case 5:
                        $this->sendBrewingItemsMenu($player);
                        break;
                    case 6:
                        $this->sendAmountWindow($player, 262);
                        break;
                    default:
                        $this->sendShopMainMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Category -", $buttons, $func);
    }

    public function sendResourceMenu(Player $player) : void {
        $buttons = ["Back", "Leather", "Coal", "Gold ingot", "Iron ingot", "Lapis Lazuli", "Diamond", "Prismarine Crystals", "Prismarine Shard", "Glow Ink Sac"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindowForItem($player, VanillaItems::LEATHER());
                        break;
                    case 2:
                        $this->sendAmountWindowForItem($player, VanillaItems::COAL());
                        break;
                    case 3:
                        $this->sendAmountWindowForItem($player, VanillaItems::GOLD_INGOT());
                        break;
                    case 4:
                        $this->sendAmountWindowForItem($player, VanillaItems::IRON_INGOT());
                        break;
                    case 5:
                        $this->sendAmountWindowForItem($player, VanillaItems::LAPIS_LAZULI());
                        break;
                    case 6:
                        $this->sendAmountWindowForItem($player, VanillaItems::DIAMOND());
                        break;
                    case 7:
                        $this->sendAmountWindowForItem($player, VanillaItems::PRISMARINE_CRYSTALS());
                        break;
                    case 8:
                        $this->sendAmountWindowForItem($player, VanillaItems::PRISMARINE_SHARD());
                        break;
                    case 9:
                        $this->sendAmountWindowForItem($player, VanillaItems::GLOW_INK_SAC());
                        break;
                    default:
                        $this->sendItemsMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Resource type -", $buttons, $func);
    }

    public function sendFoodMenu(Player $player) : void {
        $buttons = ["Back", "Golden Apple", "Golden Apple Enchanted"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindowForItem($player, VanillaItems::GOLDEN_APPLE());
                        break;
                    case 2:
                        $this->sendAmountWindowForItem($player, VanillaItems::ENCHANTED_GOLDEN_APPLE());
                        break;
                    default:
                        $this->sendItemsMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Food type -", $buttons, $func);
    }

    public function sendFarmMenu(Player $player) : void {
        $buttons = ["Back", "Beetroot seeds", "Tree Saplings", "Bone Meal", "Potato", "Cactus", "Carrot", "Melon seeds", "Pumpkin seeds", "Wheat seeds", "Sugarcane", "Egg"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, 458);
                        break;
                    case 2:
                        $this->sendTreeSaplingsMenu($player);
                        break;
                    case 3:
                        $this->sendAmountWindow($player, 351, 15);
                        break;
                    case 4:
                        $this->sendAmountWindow($player, 392);
                        break;
                    case 5:
                        $this->sendAmountWindow($player, 81);
                        break;
                    case 6:
                        $this->sendAmountWindow($player, 391);
                        break;
                    case 7:
                        $this->sendAmountWindow($player, 362);
                        break;
                    case 8:
                        $this->sendAmountWindow($player, 361);
                        break;
                    case 9:
                        $this->sendAmountWindow($player, 295);
                        break;
                    case 10:
                        $this->sendAmountWindow($player, 338);
                        break;
                    case 11:
                        $this->sendAmountWindow($player, 344);
                        break;
                    default:
                        $this->sendItemsMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a type -", $buttons, $func);
    }

    public function sendTreeSaplingsMenu(Player $player) : void {
        $buttons = ["Back", "Oak Sapling", "Spruce Sapling", "Birch Sapling", "Jungle Sapling"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, VanillaBlocks::OAK_SAPLING()->asItem());
                        break;
                    case 2:
                        $this->sendAmountWindow($player, VanillaBlocks::SPRUCE_SAPLING()->asItem(), 1);
                        break;
                    case 3:
                        $this->sendAmountWindow($player, VanillaBlocks::BIRCH_SAPLING()->asItem(), 2);
                        break;
                    case 4:
                        $this->sendAmountWindow($player, VanillaBlocks::JUNGLE_SAPLING()->asItem(), 3);
                        break;
                    default:
                        $this->sendItemsMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Sapling type -", $buttons, $func);
    }

    public function sendDyesMenu(Player $player) : void {
        $buttons = ["Back", "Black Dye", "Red Dye", "Green Dye", "Brown Dye", "Blue Dye", "Purple Dye", "Cyan Dye", "Light Gray Dye", "Gray Dye", "Pink Dye", "Lime Dye", "Yellow Dye", "Light Blue Dye", "Magenta Dye", "Orange Dye", "White Dye"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::BLACK));
                        break;
                    case 2:
                        $this->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::RED));
                        break;
                    case 3:
                        $this->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::GREEN));
                        break;
                    case 4:
                        $this->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::BROWN));
                        break;
                    case 5:
                        $this->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::BLUE));
                        break;
                    case 6:
                        $this->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::PURPLE));
                        break;
                    case 7:
                        $this->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::CYAN));
                        break;
                    case 8:
                        $this->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::LIGHT_GRAY));
                        break;
                    case 9:
                        $this->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::GRAY));
                        break;
                    case 10:
                        $this->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::PINK));
                        break;
                    case 11:
                        $this->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::LIME));
                        break;
                    case 12:
                        $this->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::YELLOW));
                        break;
                    case 13:
                        $this->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::LIGHT_BLUE));
                        break;
                    case 14:
                        $this->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::MAGENTA));
                        break;
                    case 15:
                        $this->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::ORANGE));
                        break;
                    case 16:
                        $this->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::WHITE));
                        break;
                    default:
                        $this->sendItemsMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Dye type -", $buttons, $func);
    }

    public function sendBrewingItemsMenu(Player $player) : void {
        $buttons = ["Back", "Ghast Tear", "Glistering Melon", "Magma Cream", "Blaze Powder", "Sugar", "Spider Eye", "Gun Powder", "Ferm Spider Eye", "Nether Wart"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, 370);
                        break;
                    case 2:
                        $this->sendAmountWindow($player, 383);
                        break;
                    case 3:
                        $this->sendAmountWindow($player, 378);
                        break;
                    case 4:
                        $this->sendAmountWindow($player, 377);
                        break;
                    case 5:
                        $this->sendAmountWindow($player, 353);
                        break;
                    case 6:
                        $this->sendAmountWindow($player, 375);
                        break;
                    case 7:
                        $this->sendAmountWindow($player, 289);
                        break;
                    case 8:
                        $this->sendAmountWindow($player, 376);
                        break;
                    case 9:
                        $this->sendAmountWindow($player, 372);
                        break;
                    default:
                        $this->sendItemsMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Brewing Item type -", $buttons, $func);
    }

    public function sendSpawnerMainMenu(Player $player) : void {
        $buttons = ["Back"];
        foreach ($this->pl->spawners as $name => $data) {
            $name = ucfirst($name);
            $buttons[] = "§f$name";
        }
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 7:
                        $spawner = "zombie";
                        $info = "Zombie Spawner - \nCost - " . number_format($this->pl->spawners[$spawner]['cost']) . "$";
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 6:
                        $spawner = "skeleton";
                        $info = "Skeleton Spawner - \nCost - " . number_format($this->pl->spawners[$spawner]['cost']) . "$";
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 5:
                        $spawner = "spider";
                        $info = "Spider Spawner - \nCost - " . number_format($this->pl->spawners[$spawner]['cost']) . "$";
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 9:
                        $spawner = "pigman";
                        $info = "Pigman/PigZombie Spawner - \nCost - " . number_format($this->pl->spawners[$spawner]['cost']) . "$";
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 8:
                        $spawner = "iron_golem";
                        $info = "Iron Golem Spawner - \nCost - " . number_format($this->pl->spawners["irongolem"]['cost']) . "$";
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 10:
                        $spawner = "blaze";
                        $info = "Blaze Spawner - \nCost - " . number_format($this->pl->spawners[$spawner]['cost']) . "$";
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 1:
                        $spawner = "pig";
                        $info = "Pig Spawner - \nCost - " . number_format($this->pl->spawners[$spawner]['cost']) . "$";
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 2:
                        $spawner = "cow";
                        $info = "Cow Spawner - \nCost - " . number_format($this->pl->spawners[$spawner]['cost']) . "$";
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 3:
                        $spawner = "chicken";
                        $info = "Chicken Spawner - \nCost - " . number_format($this->pl->spawners[$spawner]['cost']) . "$";
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 4:
                        $spawner = "squid";
                        $info = "Squid Spawner - \nCost - " . number_format($this->pl->spawners[$spawner]['cost']) . "$";
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 11:
                        $spawner = "camel";
                        $info = "Squid Spawner - \nCost - " . number_format($this->pl->spawners[$spawner]['cost']) . "$";
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 12:
                        $spawner = "glowsquid";
                        $info = "Squid Spawner - \nCost - " . number_format($this->pl->spawners[$spawner]['cost']) . "$";
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 13:
                        $spawner = "panda";
                        $info = "Squid Spawner - \nCost - " . number_format($this->pl->spawners[$spawner]['cost']) . "$";
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 14:
                        $spawner = "goat";
                        $info = "Squid Spawner - \nCost - " . number_format($this->pl->spawners[$spawner]['cost']) . "$";
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 15:
                        $spawner = "sheep";
                        $info = "Squid Spawner - \nCost - " . number_format($this->pl->spawners[$spawner]['cost']) . "$";
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 16:
                        $spawner = "polarbear";
                        $info = "Squid Spawner - \nCost - " . number_format($this->pl->spawners[$spawner]['cost']) . "$";
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 17:
                        $spawner = "silverfish";
                        $info = "Squid Spawner - \nCost - " . number_format($this->pl->spawners[$spawner]['cost']) . "$";
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    default:
                        $this->sendShopMainMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lSpawners";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§3Select a Spawner for info -", $buttons, $func);
    }

    public function sendSpawnerAmount(Player $player, string $spawner, string $info) : void {
        $form = new CustomForm(null);
        $form->setTitle("§6§lSpawner");
        $form->addLabel("Please choose how many " . ucfirst($spawner) . " Spawners you want to buy?\n");
        $form->addInput("Amount -", "", "1");
        $form->setCallable(function(Player $player, ?array $data) use ($spawner, $info) : void {
            if ($data !== null) {
                if (!is_int((int) $data[1]) or empty($data[1])) {
                    $error = "§6Please enter a number!";
                    $this->sendResultForm($player, $error, "sendSpawnerAmount", [$spawner, $info]);
                    return;
                }
                $data[1] = (int) $data[1];
                $amount = $data[1];
                if ($amount < 1 or $amount > 500) {
                    $error = "§6Please enter a number greater than 0 and less than 500!";
                    $this->sendResultForm($player, $error, "sendSpawnerAmount", [$spawner, $info]);
                    return;
                }
                $this->sendSpawnerInfo($player, $spawner, $info, $amount);
            }
        }
        );
        $player->sendForm($form);
    }

    public function sendSpawnerInfo(Player $player, string $spawner, string $info, int $amount) : void {
        $func = function(Player $player, ?bool $data) use ($spawner, $amount) : void {
            if ($data !== null) {
                if ($data) {
                    assert($spawner !== null);
                    if ($spawner == "iron_golem") $spawner = "irongolem";
                    $item = $this->pl->getEvFunctions()->getSpawnerBlock($this->pl->spawners[$spawner]['id'], 1, $amount);
                    if (!$player->getInventory()->canAddItem($item)) {
                        $error = "§6Your Inventory is full!";
                        $this->sendResultForm($player, $error, "sendSpawnerMainMenu");
                        return;
                    }
                    $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                    $mon = $this->pl->spawners[$spawner]["cost"] * $amount;
                    if (!$user->removeMoney($mon)) {
                        $error = "§6You don't have enough money to purchase that much! Required money: $mon$";
                        $this->sendResultForm($player, $error, "sendSpawnerMainMenu");
                        return;
                    }
                    $player->getInventory()->addItem($item);
                    $player->sendMessage("§8- §aSucceed bought x$amount of $spawner Spawners!");
                } else {
                    $this->sendSpawnerMainMenu($player);
                }
            }
        };
        $this->ff->sendModalForm($player, "Do you wanna buy x$amount of $spawner spawners?", $info, ["Yes", "No"], $func);
    }

    public function sendPetMenu(Player $player) : void {
        $buttons = ["Buy a Pet", "Choose your active Pet", "Change Pet Name", "Change Pet Size", "Clear Pet", "Toggle Follow", "Back"];
        $func = function(Player $player, ?int $data) {
            if ($data !== null) {
                switch ($data) {
                    case 0:
                        $this->sendPetSelectMenu($player);
                        break;
                    case 1:
                        $this->sendPetChooseMenu($player);
                        break;
                    case 2:
                        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                        if (!$user->hasSetPet()) {
                            $result = "You dont have an active pet to name! Choose a pet from /pets";
                            $this->sendResultForm($player, $result, "sendPetMenu");
                            return;
                        }
                        $this->sendPetChangeNameMenu($player);
                        break;
                    case 3:
                        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                        if (!$user->hasSetPet()) {
                            $result = "You dont have an active pet to size! Choose a pet from /pets";
                            $this->sendResultForm($player, $result, "sendPetMenu");
                            return;
                        }
                        $this->sendPetChangeSizeMenu($player);
                        break;
                    case 4:
                        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                        if ($user->hasSetPet()) {
                            $user->setPet();
                            $result = "Pet cleared!";
                            if (!empty($pet = $this->pl->getPetsFrom($player))) {
                                foreach ($pet as $p) {
                                    $this->pl->removePet($p);
                                }
                            }
                        } else {
                            $result = "You dont have an active pet to clear!";
                        }
                        $this->sendResultForm($player, $result, "sendPetMenu");
                        break;
                    case 5:
                        if (isset($this->pl->dontFollow[$player->getName()])) {
                            $player->sendMessage("§ePet will follow you now!");
                            unset($this->pl->dontFollow[$player->getName()]);
                        } else {
                            $player->sendMessage("§cPet wont follow you anymore!");
                            $this->pl->dontFollow[$player->getName()] = true;
                        }
                        break;
                    default:
                        $this->sendShopMainMenu($player);
                        break;
                }
            }
        };
        $title = "§a§lPets";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select an Option -", $buttons, $func);
    }

    public function sendPetSelectMenu(Player $player) : void {
        $buttons = $pets = [];
        foreach ($this->pl->pets as $name) {
            $buttons[] = $name;
            $pets[] = $name;
        }
        $func = function(Player $player, ?int $data) use ($pets) {
            if ($data !== null) {
                if (isset($pets[$data])) {
                    $sel = $pets[$data];
                    $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                    if ($user->hasPet($sel)) {
                        $result = "You have already unlocked that pet! Select it from /pets";
                        $this->sendResultForm($player, $result, "sendPetMenu");
                    } elseif ($sel === "SnowFox" && $this->pl->getEvFunctions()->hasStaffRank($user->getPlayer()->getName())) {
                        $user->addPet("SnowFox");
                        $result = "Unlocked SnowFox Pet! Select it from /pets";
                        $this->sendResultForm($player, $result, "sendPetMenu");
                    } else {
                        $this->sendPetInfo($player, $sel);
                    }
                } else {
                    $result = "Pet not found";
                    $this->sendResultForm($player, $result, "sendPetMenu");
                }
            }
        };
        $title = "§a§lPets";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Pet -", $buttons, $func);
    }

    public function sendPetInfo(Player $player, string $pet) : void {
        $properties = $this->pl->getPetProperties()->getPropertiesFor($pet);
        $type = (string) $properties["Type"];
        $content = "";
        $buttons = ["Yes", "No"];
        $price = 0;
        if ($type == 'Common' or $type == 'Rare') {
            $price = (string) $properties["Price"];
            $content = "§6Do you wanna unlock §a$pet §6pet for §e" . number_format($price) . "§6$?\n§fYou wont lose your old pet if you unlock a new pet.";
        } else {
            $buttons = ["Go Back", "Go Back"];
            if ($type == 'Premium') {
                $content = "§6Sorry, §a$pet §6Pet is §bPremium, §6you can only get it from §aour store! §bBuy Premium pets from shop.fallentech.io!";
            }
            if ($type == 'Exclusive') {
                $content = "§6Sorry, §a$pet §6Pet is §eExclusive, §6Only available via Giveaways or Events on Discord -> http://discord.fallentech.io";
            }
            if ($type == 'Staff') {
                $content = "§6Sorry, §a$pet §6Pet is for §eStaff, §6Only Staff can use it!";
            }
            if ($type == 'IslandChamp') {
                $content = "§6Sorry, §a$pet §6Pet is for §eIslandChamps, §6Only the winners of Skyblock seasons can recieve this pet!!";
            }
        }
        $func = function(Player $player, ?bool $data) use ($pet, $price, $type) {
            if ($data !== null) {
                if ($data) {
                    if ($type != "Common" and $type != "Rare") {
                        $this->sendPetSelectMenu($player);
                        return;
                    }
                    $this->sendPetNameMenu($player, $pet, $type, $price);
                } else $this->sendPetSelectMenu($player);
            }
        };
        $this->ff->sendModalForm($player, "§bUnlock Pet", $content, $buttons, $func);
    }

    public function sendPetNameMenu(Player $player, string $pet, string $type, string $price) : void {
        $form = new CustomForm(null);
        $form->setTitle("§eChoose Pet Name");
        $form->addInput("Enter the name you want to name your pet -", "", "Name");
        $form->setCallable(function(Player $player, ?array $data) use ($pet, $price) {
            if ($data !== null) {
                $name = (string) $data[0];
                if (!(ctype_alnum($name))) {
                    $result = "That name is not valid! You can only keep numbers and alphabets as pet name!";
                    $this->sendResultForm($player, $result, "sendPetSelectMenu");
                    return;
                }
                if (strlen($name) > 15) {
                    $result = "Pet name cannot have more than 15 letters!";
                    $this->sendResultForm($player, $result, "sendPetSelectMenu");
                    return;
                }
                $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                if (!$user->removeMoney($price)) {
                    $result = "You dont have enough money to unlock that pet! Require money: $price$";
                    $this->sendResultForm($player, $result, "sendPetSelectMenu");
                    return;
                }
                if (($pets = $this->pl->createPet($pet, $player, $name)) === null) {
                    $result = "Error creating pet!";
                    $this->sendResultForm($player, $result, "sendPetSelectMenu");
                    return;
                }
                $user->addPet($pet);
                $user->setPet($pet);
                $user->setPetName($name);
                $pets->spawnToAll();
                $pets->setDormant(false);
                $result = "§6You have successfully unlocked §a$pet §6pet! §a$pet §6is set as your active pet! §fPet Name: '§b$name'";
                $this->sendResultForm($player, $result, "sendPetSelectMenu");
            }
        }
        );
        $player->sendForm($form);
    }

    public function sendPetChooseMenu(Player $player) : void {
        $buttons = [];
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        foreach ($user->getUnlockedPets() as $pet) {
            $buttons[] = $pet;
        }
        $buttons[] = "Back";
        $func = function(Player $player, ?int $data) {
            if ($data !== null) {
                if ($player->getWorld()->getDisplayName() == "PvP") {
                    $player->sendMessage("§4[Error] §cYou can't select pets here!");
                    return;
                }
                if ($player->hasNoClientPredictions()) {
                    $player->sendMessage("§4[Error] §cYou are frozen!");
                    return;
                }
                $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                $pets = $user->getUnlockedPets();
                if (isset($pets[$data])) {
                    $pet = $pets[$data];
                    if ($user->hasPet($pet)) {
                        $user->setPet($pet);
                        $pets = $this->pl->createPet($pet, $player, $user->getPetName());
                        if (!is_null($pets)) {
                            $pets->spawnToAll();
                            $pets->setDormant(false);
                            $result = "You have selected your $pet pet!";
                            $this->sendResultForm($player, $result, "sendPetMenu");
                        }
                    } else {
                        $result = "You havent unlocked this pet yet! Buy it from /pets";
                        $this->sendResultForm($player, $result, "sendPetMenu");
                    }
                } else {
                    $this->sendPetMenu($player);
                }
            }
        };
        $title = "§a§lPets";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Choose an unlocked Pet -", $buttons, $func);
    }

    public function sendPetChangeNameMenu(Player $player) : void {
        $form = new CustomForm(null);
        $form->setTitle("§eChange Pet Name for 10000$");
        $form->addInput("Enter the name you want to name your pet -", "", "Name");
        $form->setCallable(function(Player $player, ?array $data) : void {
            if ($data !== null) {
                $name = (string) $data[0];
                if (!(ctype_alnum($name))) {
                    $result = "That name is not valid! You can only keep numbers and alphabets as pet name!";
                    $this->sendResultForm($player, $result, "sendPetMenu");
                    return;
                }
                if (strlen($name) > 15) {
                    $result = "Pet name cannot have more than 15 letters!";
                    $this->sendResultForm($player, $result, "sendPetMenu");
                    return;
                }
                $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                if (!$user->removeMoney(10000)) {
                    $result = "You dont have enough money to unlock that pet! Require money: 10,000$";
                    $this->sendResultForm($player, $result, "sendPetMenu");
                    return;
                }
                $user->setPetName($name);
                if (!empty($pet = $this->pl->getPetsFrom($player))) {
                    foreach ($pet as $p) {
                        $p->changeName($name);
                    }
                }
                $result = "§6You have successfully change your Pets name to '§b$name'";
                $this->sendResultForm($player, $result, "sendPetMenu");
            }
        }
        );
        $player->sendForm($form);
    }

    public function sendPetChangeSizeMenu(Player $player) : void {
        $form = new CustomForm(null);
        $form->setTitle("§eChange Pet Size");
        $form->addLabel("§fSelect the size you want your pet to be -");
        $form->addDropdown("§3Size -", ["Small", "Normal", "Large"], 1);
        $form->setCallable(function(Player $player, ?array $data) : void {
            if ($data !== null) {
                $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                $pet = $user->getSelectedPet();
                $properties = $this->pl->getPetProperties()->getPropertiesFor($pet);
                $normal = (float) $properties["Size"];
                $min = (float) $properties["Min-Size"];
                $max = (float) $properties["Max-Size"];
                $size = (int) $data[1];
                $sel = [];
                if (!empty($pet = $this->pl->getPetsFrom($player))) {
                    foreach ($pet as $p) {
                        $sel = $p;
                    }
                }
                if (is_array($sel)) {
                    return;
                }
                $s = "";
                switch ($size) {
                    case 0:
                        $s = "small";
                        $sel->setScale($min);
                        break;
                    case 1:
                        $s = "normal";
                        $sel->setScale($normal);
                        break;
                    case 2:
                        $s = "large";
                        $sel->setScale($max);
                        break;
                }
                $result = "§6You have successfully change your Pets size to '§b$s'";
                $this->sendResultForm($player, $result, "sendPetMenu");
            }
        }
        );
        $player->sendForm($form);
    }

    public function sendEnchantMainMenu(Player $player) : void {
        $buttons = ["Back", "Protection", "Fire Protection", "Feather Falling", "Blast Protection", "Projectile Protection", "Respiration", "Depth Strider", "Fortune", "SilkTouch", "Aqua Affinity", "Sharpness", "Smite", "Bane of Arthropods", "Knockback", "Fire Aspect", "Efficiency", "Unbreaking", "Infinity", "Looting"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendEnchantLevelMenu($player, "Protection", 0);
                        break;
                    case 2:
                        $this->sendEnchantLevelMenu($player, "FireProtection", 1);
                        break;
                    case 3:
                        $this->sendEnchantLevelMenu($player, "FeatherFalling", 2);
                        break;
                    case 4:
                        $this->sendEnchantLevelMenu($player, "BlastProtection", 3);
                        break;
                    case 5:
                        $this->sendEnchantLevelMenu($player, "ProjectileProtection", 4);
                        break;
                    case 6:
                        $this->sendEnchantLevelMenu($player, "Respiration", 6);
                        break;
                    case 7:
                        $this->sendEnchantLevelMenu($player, "DepthStrider", 7);
                        break;
                    case 8:
                        $this->sendEnchantLevelMenu($player, "Fortune", 18);
                        break;
                    case 9:
                        $this->sendEnchantLevelMenu($player, "SilkTouch", 16);
                        break;
                    case 10:
                        $this->sendEnchantLevelMenu($player, "AquaAffinity", 8);
                        break;
                    case 11:
                        $this->sendEnchantLevelMenu($player, "Sharpness", 9);
                        break;
                    case 12:
                        $this->sendEnchantLevelMenu($player, "Smite", 10);
                        break;
                    case 13:
                        $this->sendEnchantLevelMenu($player, "BaneOfArthropods", 11);
                        break;
                    case 14:
                        $this->sendEnchantLevelMenu($player, "Knockback", 12);
                        break;
                    case 15:
                        $this->sendEnchantLevelMenu($player, "FireAspect", 13);
                        break;
                    case 16:
                        $this->sendEnchantLevelMenu($player, "Efficiency", 15);
                        break;
                    case 17:
                        $this->sendEnchantLevelMenu($player, "Unbreaking", 17);
                        break;
                    case 18:
                        $this->sendEnchantLevelMenu($player, "Infinity", 22);
                        break;
                    case 19:
                        $this->sendEnchantLevelMenu($player, "Looting", 14);
                        break;
                    default:
                        $this->sendShopMainMenu($player);
                        break;
                }
            }
        };
        $title = "§6§lEnchants";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select an Enchant to buy -", $buttons, $func);
    }

    public function sendEnchantLevelMenu(Player $player, string $enchname, int $enchid) : void {
        $buttons = ["Back", "§f1", "§f2", "§f3", "§f4", "§f5", "§f6"];
        $func = function(Player $player, ?int $data) use ($enchname, $enchid) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendEnchantAmountMenu($player, $enchname, $enchid, 1);
                        break;
                    case 2:
                        $this->sendEnchantAmountMenu($player, $enchname, $enchid, 2);
                        break;
                    case 3:
                        $this->sendEnchantAmountMenu($player, $enchname, $enchid, 3);
                        break;
                    case 4:
                        $this->sendEnchantAmountMenu($player, $enchname, $enchid, 4);
                        break;
                    case 5:
                        $this->sendEnchantAmountMenu($player, $enchname, $enchid, 5);
                        break;
                    case 6:
                        $this->sendEnchantAmountMenu($player, $enchname, $enchid, 6);
                        break;
                    default:
                        $this->sendEnchantMainMenu($player);
                        break;
                }
            }
        };
        $title = "§6§lEnchants";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Level -", $buttons, $func);
    }

    public function sendEnchantAmountMenu(Player $player, string $name, int $id, int $level) : void {
        $form = new CustomForm(null);
        $form->setTitle("§6§lEnchants");
        $form->addLabel("Please choose how many " . ucfirst($name) . " ench orbs of level $level do you want to buy?\n");
        $form->addInput("Amount -", "", "1");
        $form->setCallable(function(Player $player, ?array $data) use ($name, $id, $level) : void {
            if ($data !== null) {
                if (!is_int((int) $data[1]) or empty($data[1])) {
                    $error = "§6Please enter a number!";
                    $this->sendResultForm($player, $error, "sendEnchantAmountMenu", [$name, $id, $level]);
                    return;
                }
                $data[1] = (int) $data[1];
                $amount = $data[1];
                if ($amount < 1 or $amount > 500) {
                    $error = "§6Please enter a number greater than 0 and less than 500!";
                    $this->sendResultForm($player, $error, "sendEnchantAmountMenu", [$name, $id, $level]);
                    return;
                }
                $this->sendEnchantInfoMenu($player, $name, $id, $level, $amount);
            }
        }
        );
        $player->sendForm($form);
    }

    public function sendEnchantInfoMenu(Player $player, string $enchname, int $enchid, int $level, int $amount) : void {
        $cost = $level * 12000 * $amount;
        $func = function(Player $player, ?bool $data) use ($level, $enchname, $enchid, $cost, $amount) : void {
            if ($data) {
                assert($enchname !== null && $enchid !== null);
                $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                if (!$user->removeMoney($cost)) {
                    $error = "§6You don't have enough money to purchase that much! Required money: $cost$";
                    $this->sendResultForm($player, $error, "sendEnchantMainMenu");
                    return;
                }
                //                $item = ItemFactory::getInstance()->get(381, 0, $amount);
                $item = LegacyStringToItemParser::getInstance()->parse(381)->setCount($amount);
                $item->setCustomName(TF::RESET . TF::BOLD . " §6$enchname §r§9Enchantment Orb \n §aLevel: §6$level \n §3ID: §6$enchid \n §eUse this on a tool or armor by /ench ");
                if (!$player->getInventory()->canAddItem($item)) {
                    $error = "§6Your Inventory is full!";
                    $this->sendResultForm($player, $error, "sendEnchantMainMenu");
                    return;
                }
                $player->getInventory()->addItem($item);
                $player->sendMessage("§eSucceed bought §7x§c$amount §a$enchname §eEnchantment orb of level §b$level §efor §6$cost$!");
            }
        };
        $this->ff->sendModalForm($player, "Checkout:", "Are you sure you wanna buy x$amount $enchname Enchant of Level $level? Total Price: $cost$!\n§7Unlock all Enchants by getting /enchant from shop.fallentech.io", ["Yes", "No"], $func);
    }

    public function sendPotionsMenu(Player $player) : void {
        $buttons = ["Back", "Glass Bottle", "Water Bottle", "Mundane Potion", "Awkward Potion", "NightVision Potion", "Invisibility Potion", "FireResistance Potion", "Swiftness Potion", "Slowness Potion", "WaterBreathing Potion", "Regeneration Potion", "Strength Potion", "Weakness Potion"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendAmountWindow($player, ItemTypeNames::GLASS_BOTTLE);
                        break;
                    case 2:
                        $this->sendAmountWindow($player, ItemTypeNames::POTION);
                        break;
                    case 3:
                        $this->sendAmountWindow($player, ItemTypeNames::POTION, 1);
                        break;
                    case 4:
                        $this->sendAmountWindow($player, ItemTypeNames::POTION, 4);
                        break;
                    case 5:
                        $this->sendAmountWindow($player, ItemTypeNames::POTION, 5);
                        break;
                    case 6:
                        $this->sendAmountWindow($player, ItemTypeNames::POTION, 7);
                        break;
                    case 7:
                        $this->sendAmountWindow($player, ItemTypeNames::POTION, 12);
                        break;
                    case 8:
                        $this->sendAmountWindow($player, 373, 14);
                        break;
                    case 9:
                        $this->sendAmountWindow($player, 373, 17);
                        break;
                    case 10:
                        $this->sendAmountWindow($player, 373, 19);
                        break;
                    case 11:
                        $this->sendAmountWindow($player, 373, 28);
                        break;
                    case 12:
                        $this->sendAmountWindow($player, 373, 31);
                        break;
                    case 13:
                        $this->sendAmountWindow($player, 373, 34);
                        break;
                    default:
                        $this->sendShopMainMenu($player);
                        break;
                }
            }
        };
        $title = "§6§lPotions";
        if ($this->pl->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        $this->ff->sendSimpleForm($player, $title, "§6Select a Potion to buy -", $buttons, $func);
    }

    public function sendOregenPref(Player $player) : void {
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        $island = $this->pl->getIslandManager()->getOnlineIsland($user->getIsland());
        $form = new CustomForm(function(Player $player, ?array $data) use ($island) {
            if (!$data or !$island) return;
            //            var_dump($data);
            $island->updateOreDataPref($data);
        }
        );
        $form->setTitle("§l§gOregen Pref");
        $form->addLabel("\n§l§aSet the ores that will spawn at your island. You get a max of §e20§a points to apply.\nEach point = §e5 percent §achance \nof that block forming.");
        if (!$island) return;
        foreach ($island->getOreDataPrefArray() as $key => $value) {
            if ($key != "name") {
                if ($key == "cobblestone") {
                    $vallst = [];
                    for ($x = 0; $x <= 20; $x++) {
                        $vallst[] = $x . "";
                    }
                    $form->addStepSlider("§l" . ucfirst($key), $vallst, intval($value), $key);
                } else {
                    $max = $island->getOreDataArray()[$key];
                    if ($max == 0) {
                        //$form->addLabel("§l§c".ucfirst($key)." not yet unlocked.\n");
                    } else {
                        $vallst = [];
                        for ($x = 0; $x <= intval($max); $x++) {
                            $vallst[] = $x . "";
                        }
                        $form->addStepSlider("§l" . ucfirst($key), $vallst, intval($value), $key);
                    }
                }
            }
        }
        $form->sendToPlayer($player);
    }

    public function oreIsUnlocked($island, $ore) : bool {
        return true;
    }

    public function oreToTexturePath($ore) : string {
        return match ($ore) {
            "coal" => "textures/blocks/coal_ore",
            "copper" => "textures/blocks/copper_ore",
            "iron" => "textures/blocks/iron_ore",
            "lapis" => "textures/blocks/lapis_ore",
            "gold" => "textures/blocks/gold_ore",
            "diamond" => "textures/blocks/diamond_ore",
            "emerald" => "textures/blocks/emerald_ore",
            "quartz" => "textures/blocks/quartz_ore",
            "netherite" => "textures/blocks/ancient_debris_side",
            "deep_coal" => "textures/blocks/deepslate/deepslate_coal_ore",
            "deep_copper" => "textures/blocks/deepslate/deepslate_copper_ore",
            "deep_iron" => "textures/blocks/deepslate/deepslate_iron_ore",
            "deep_lapis" => "textures/blocks/deepslate/deepslate_lapis_ore",
            "deep_gold" => "textures/blocks/deepslate/deepslate_gold_ore",
            "deep_diamond" => "textures/blocks/deepslate/deepslate_diamond_ore",
            "deep_emerald" => "textures/blocks/deepslate/deepslate_emerald_ore",
            "deep_quartz" => "textures/blocks/quartz_block_top",
            "deep_netherite" => "textures/blocks/netherite_block",
            default => "textures/blocks/cobblestone",
        };
    }

    public function getOrePrice($ore, $level = 0) : float|int {
        $price = match ($ore) {
            "coal" => [40, 30],
            "copper" => [1420, 70],
            "iron" => [4610, 110],
            "lapis" => [10520, 160],
            "gold" => [20200, 200],
            "diamond" => [34900, 300],
            "emerald" => [56000, 400],
            "quartz" => [84900, 600],
            "netherite" => [123500, 700],
            "deep_coal" => [188100, 1100],
            "deep_copper" => [255400, 1300],
            "deep_iron" => [338800, 1500],
            "deep_lapis" => [440800, 1700],
            "deep_gold" => [563700, 1900],
            "deep_diamond" => [710300, 2100],
            "deep_emerald" => [883500, 2300],
            "deep_quartz" => [1086000, 3000],
            "deep_netherite" => [1321000, 4000],
            default => [-1, 0],
        };
        return $price[0] + $price[1] * $level;
    }

    public function sendOregenRebuy(Player $player, $data) : void {
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        $island = $this->pl->getIslandManager()->getOnlineIsland($user->getIsland());
        $count = $island->getOreUpgradeCount($data);
        //        $intPrice = $this->getOrePrice($data);
        //        $price = floor($intPrice+(($intPrice*$count)*0.25));
        //        $nextPrice = floor($intPrice+(($intPrice*$count+1)*0.25));
        $price = $this->getOrePrice($data, $count);
        $nextPrice = $this->getOrePrice($data, $count + 1);
        $form = new SimpleForm(function(Player $player, $data) use ($user, $island, $nextPrice) {
            if ($data == "exit") return;
            if ($data == "buy") {
                $this->sendOregenUpgrade($player);
            } elseif ($this->getOrePrice($data) >= 0) {
                if ($this->getOrePrice($data) < 0) return;
                $user->removeMana($nextPrice);
                $island->updateAddOreData($data);
                $orePref = $island->getOreDataPrefArray();
                $orePref[$data] = $orePref[$data] + 1;
                $island->updateOreDataPref($orePref);
                $this->sendOregenRebuy($player, $data);
            }
        }
        );
        $form->setTitle("§l§gUpgrade Result §cLvl [{$island->getOreUpgradeCount($data)}/20]");

        if ($user->getMana() - $price >= 0 and $island->getOreDataArray()[$data] < 20) {
            $form->addButton("§aRe-Buy §e$data §aupgrade\n§dmana " . $nextPrice, 0, "textures/ui/recap_glyph_color_2x.png", $data);
        }
        $form->addButton("§aBack to Oregen Upgrades", 0, "textures/items/emerald", "buy");
        $form->addButton("§cExit", 0, "textures/blocks/barrier", "exit");
        $form->setContent("§aUpgrade Purchased for §d$price mana!");
        $form->setContent("§d{$user->getMana()} mana remaining.");
        $player->sendForm($form);
    }

    private function sendRebirthConfirm(Player $player) : void {
        $form = new SimpleForm(function(Player $player, $data) {
            if ($data == "confirmRebirth") {
                $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                $island = $this->pl->getIslandManager()->getOnlineIsland($user->getIsland());
                $island->updateOreData("coal", 0);
                $island->updateOreData("copper", 0);
                $island->updateOreData("iron", 0);
                $island->updateOreData("lapis", 0);
                $island->updateOreData("gold", 0);
                $island->updateOreData("diamond", 0);
                $island->updateOreData("emerald", 0);
                $island->updateOreData("quartz", 0);
                $island->updateOreData("netherite", 0);
                $island->updateOreData("deep_coal", 1);
                $data = $island->getOreDataArray();
                foreach ($data as $block) {
                    $data[$block] = 0;
                }
                $data["deep_coal"] = 1;
                $island->updateOreDataPref($data);
            }
            $this->sendOregenUpgrade($player);
        }
        );
        $form->setContent("§c§lIMPORTANT!!!§r§a clicking this upgrade will REMOVE all other upgrades and start you back that the beginning coal ore upgrade. Except this time its deepslate and will produce more items!");
        $form->addButton("§6§lConfirm", 0, "textures/items/totem", "confirmRebirth");
        $form->addButton("§cExit", 0, "textures/blocks/barrier");
        $player->sendForm($form);
    }


    public function sendOregenUpgrade(Player $player) : void {
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        $island = $this->pl->getIslandManager()->getOnlineIsland($user->getIsland());
        if ($island === null) {
            $player->sendMessage("§4[Error] §cYou need to have an island to run this command!");
            return;
        }
        $form = new SimpleForm(function(Player $player, $data) use ($island, $user) {
            echo($data . "\n");
            if (!$data) return;
            $oredata = $island->getOreDataArray();
            //            $intPrice = $this->getOrePrice($data);
            $count = $island->getOreUpgradeCount($data);
            //            $price = floor($intPrice+(($intPrice*$count)*0.25));
            //            $nextPrice = floor($intPrice+(($intPrice*($count+1))*0.25));
            $price = $this->getOrePrice($data, $count);
            $nextPrice = $this->getOrePrice($data, $count + 1);
            $resultForm = new SimpleForm(null);
            $resultForm->setCallable(function(Player $player, $data) use ($island, $nextPrice, $user) {
                if ($data == "buy") {
                    $this->sendOregenUpgrade($player);
                } elseif ($this->getOrePrice($data) >= 0) {
                    $user->removeMana($nextPrice);
                    $island->updateAddOreData($data);
                    $orePref = $island->getOreDataPrefArray();
                    $orePref[$data] = $orePref[$data] + 1;
                    $island->updateOreDataPref($orePref);
                    $this->sendOregenRebuy($player, $data);
                }
            }
            );
            if ($data == "rebirth") {
                //                $resultForm->setContent("§c§lIMPORTANT!!!§r§a clicking this upgrade will REMOVE all other upgrades and start you back that the beginning coal ore upgrade. Except this time its deepslate and will produce more items!");
                //                $resultForm->addButton("§6§lConfirm",0,"textures/items/totem","confirmRebirth");
                //                $resultForm->addButton("§cExit", 0,"textures/blocks/barrier");
                $this->sendRebirthConfirm($player);
                return;
            }
            $resultForm->setTitle("§l§gUpgrade Result §cLvl [" . ($island->getOreUpgradeCount($data) + 1) . "/20]");
            if (isset($island->getOreDataArray()[$data])) {
                if (($user->getMana() - ($price + $nextPrice)) >= 0 and $island->getOreDataArray()[$data] < 20) {
                    $resultForm->addButton("§aRe-Buy §e$data §aupgrade\n§dmana " . $nextPrice, 0, "textures/ui/recap_glyph_color_2x.png", $data);
                }
            }
            $resultForm->addButton("§aBack to Oregen Upgrades", 0, "textures/items/emerald", "buy");
            $resultForm->addButton("§cExit", 0, "textures/blocks/barrier");


            if ($data == "cant_buy") {
                $resultForm->setTitle("§l§gUpgrade Result §cLvl [" . $island->getOreUpgradeCount($data) . "/20]");
                $resultForm->setContent("§cNot Enough Mana!");
                $resultForm->sendToPlayer($player);
                return;
            }
            //            if(!$island) {
            //                $resultForm->setTitle("§l§gUpgrade Result §cLvl [" . $island->getOreUpgradeCount($data) . "/20]");
            //                $resultForm->setContent("§cYou need an island to do this!");
            //                $resultForm->sendToPlayer($player);
            //                return;
            //            }
            if (!array_key_exists($data, $oredata)) {
                $resultForm->setTitle("§l§gUpgrade Result §cLvl [" . $island->getOreUpgradeCount($data) . "/20]");
                $resultForm->setContent("§cThis upgrade is locked!");
                //                $resultForm->setContent("§cError with the upgrade! report this to a staff {{$data}}");
                $resultForm->sendToPlayer($player);
                return;
            }
            if ($island->getOreDataArray()[$data] >= 20) {
                $this->sendOregenUpgrade($player);
                return;
            }
            $resultForm->setContent("§aUpgrade Purchased for §d$price mana!");
            $user->removeMana($price);
            $island->updateOreData($data, $oredata[$data] + 1);
            $orePref = $island->getOreDataPrefArray();
            $orePref[$data] = $orePref[$data] + 1;
            $island->updateOreDataPref($orePref);
            $resultForm->sendToPlayer($player);
        }
        );
        $form->setTitle("§l§gOregen Upgrade");
        $form->setContent("§aClick to buy or upgrade an oregen.");
        $prev = 20;
        foreach ($island->getOreDataArray() as $ore => $count) {
            if ($ore != "name" and !(($island->getOreDataArray()["deep_coal"] <= 0 and str_starts_with($ore, "deep_")) or ($island->getOreDataArray()["deep_coal"] >= 1 and !str_starts_with($ore, "deep_")))) { //could have made this logic not so nasty to read but basically if the player is into the deepslate upgrades then don't show stone and if not at level 1 deepslate coal don't show any deepslate ores.
                $price = $this->getOrePrice($ore, $count);
                $name = ucfirst($ore);
                /**to change deepslate stuff to a correct name. cant put "Deepslate" in the name cuz too large*/
                if (str_starts_with($ore, "deep_")) {
                    $name = ucfirst(str_replace("Deep_", "", $name));
                }

                if (intval($count) < 20 and $this->oreIsUnlocked($island, $ore) and $prev >= 20) {
                    if ($user->getMana() - $price < 0) {
                        //mana is red when cant afford
                        $form->addButton($name . "  §cLvl [$count/20]" . "\n§4" . $price . " mana", 0, $this->oreToTexturePath($ore), "cant_buy");
                    } else {
                        /**this is the only real working buy button*/
                        $form->addButton($name . "  §cLvl [$count/20]" . "\n§d" . $price . " mana", 0, $this->oreToTexturePath($ore), $ore);
                    }
                } else if ($prev >= 20) {
                    $form->addButton($name . "  §cLvl [$count/20]" . "\n§aMaxed!", 0, $this->oreToTexturePath($ore), $ore);
                } else {
                    $form->addButton("§4[Locked!] §r" . $name . "  §cLvl [$count/20]" . "\n§4$price mana", 0, $this->oreToTexturePath($ore));
                }
                $prev = $count;
            }
        }
        if ($island->getOreDataArray()["netherite"] >= 20) {
            $form->addButton("§l§6Rebirth", 0, "textures/items/totem", "rebirth");
        }
        $form->sendToPlayer($player);
    }

}
