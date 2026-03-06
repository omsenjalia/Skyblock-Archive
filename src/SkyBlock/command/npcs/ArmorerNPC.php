<?php

namespace SkyBlock\command\npcs;

use pocketmine\command\CommandSender;
use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\ShopUtil;

class ArmorerNPC extends BaseCommand {

    public const PREFIX = "§l§e[§aEric The Armorer§e]§r ";
    public static array $interacting = [];

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'armorernpc');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        $player = $sender;
        if (!$sender instanceof Player) {
            $this->sendUsage($sender, $commandLabel);
            return;
        }
        assert($player instanceof Player);
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
        if ($user === null) {
            return;
        }

        if (isset(self::$interacting[$player->getName()])) {
            return;
        }

        $player->sendMessage(self::PREFIX . "Let there be light!!!");
        $this->sendEquipmentMenu($player);
    }

    public function sendEquipmentMenu(Player $player) : void {
        $buttons = ["Back", "Elytra", "Helmet", "ChestPlate", "Leggings", "Boots", "Shovel", "Pickaxe", "Axe", "Sword", "Hoe", "Bow", "Saddle"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindow($player, ItemTypeNames::ELYTRA);
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
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::BOW());
                        break;
                    case 12:
                        ShopUtil::getInstance()->sendAmountWindow($player, ItemTypeNames::SADDLE);
                        break;
                    default:
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Category -", $buttons, $func);
    }

    public function sendHelmetMenu(Player $player) : void {
        $buttons = ["Back", "Leather Helmet", "Golden Helmet", "Chainmail Helmet", "Iron Helmet", "Diamond Helmet"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::LEATHER_CAP());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::GOLDEN_HELMET());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::CHAINMAIL_HELMET());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::IRON_HELMET());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DIAMOND_HELMET());
                        break;
                    default:
                        $this->sendEquipmentMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Helmet type -", $buttons, $func);
    }

    public function sendChestplateMenu(Player $player) : void {
        $buttons = ["Back", "Leather Chestplate", "Golden Chestplate", "Chainmail Chestplate", "Iron Chestplate", "Diamond Chestplate"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::LEATHER_TUNIC());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::GOLDEN_CHESTPLATE());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::CHAINMAIL_CHESTPLATE());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::IRON_CHESTPLATE());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DIAMOND_CHESTPLATE());
                        break;
                    default:
                        $this->sendEquipmentMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Chestplate type -", $buttons, $func);
    }

    public function sendLeggingsMenu(Player $player) : void {
        $buttons = ["Back", "Leather Leggings", "Golden Leggings", "Chainmail Leggings", "Iron Leggings", "Diamond Leggings"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::LEATHER_PANTS());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::GOLDEN_LEGGINGS());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::CHAINMAIL_LEGGINGS());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::IRON_LEGGINGS());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DIAMOND_LEGGINGS());
                        break;
                    default:
                        $this->sendEquipmentMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Leggings type -", $buttons, $func);
    }

    public function sendBootsMenu(Player $player) : void {
        $buttons = ["Back", "Leather Boots", "Golden Boots", "Chainmail Boots", "Iron Boots", "Diamond Boots"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::LEATHER_BOOTS());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::GOLDEN_BOOTS());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::CHAINMAIL_BOOTS());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::IRON_BOOTS());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DIAMOND_BOOTS());
                        break;
                    default:
                        $this->sendEquipmentMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Boots type -", $buttons, $func);
    }

    public function sendShovelMenu(Player $player) : void {
        $buttons = ["Back", "Wooden Shovel", "Golden Shovel", "Stone Shovel", "Iron Shovel", "Diamond Shovel"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::WOODEN_SHOVEL());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::GOLDEN_SHOVEL());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::STONE_SHOVEL());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::IRON_SHOVEL());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DIAMOND_SHOVEL());
                        break;
                    default:
                        $this->sendEquipmentMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Shovel type -", $buttons, $func);
    }

    public function sendPickaxeMenu(Player $player) : void {
        $buttons = ["Back", "Wooden Pickaxe", "Golden Pickaxe", "Stone Pickaxe", "Iron Pickaxe", "Diamond Pickaxe"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::WOODEN_PICKAXE());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::GOLDEN_PICKAXE());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::STONE_PICKAXE());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::IRON_PICKAXE());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DIAMOND_PICKAXE());
                        break;
                    default:
                        $this->sendEquipmentMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Pickaxe type -", $buttons, $func);
    }

    public function sendAxeMenu(Player $player) : void {
        $buttons = ["Back", "Wooden Axe", "Golden Axe", "Stone Axe", "Iron Axe", "Diamond Axe"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::WOODEN_AXE());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::GOLDEN_AXE());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::STONE_AXE());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::IRON_AXE());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DIAMOND_AXE());
                        break;
                    default:
                        $this->sendEquipmentMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Axe type -", $buttons, $func);
    }

    public function sendSwordMenu(Player $player) : void {
        $buttons = ["Back", "Wooden Sword", "Golden Sword", "Stone Sword", "Iron Sword", "Diamond Sword"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::WOODEN_SWORD());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::GOLDEN_SWORD());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::STONE_SWORD());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::IRON_SWORD());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DIAMOND_SWORD());
                        break;
                    default:
                        $this->sendEquipmentMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Sword type -", $buttons, $func);
    }

    public function sendHoeMenu(Player $player) : void {
        $buttons = ["Back", "Wooden Hoe", "Golden Hoe", "Stone Hoe", "Iron Hoe", "Diamond Hoe"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::WOODEN_HOE());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::GOLDEN_HOE());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::STONE_HOE());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::IRON_HOE());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DIAMOND_HOE());
                        break;
                    default:
                        $this->sendEquipmentMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Hoe type -", $buttons, $func);
    }


}