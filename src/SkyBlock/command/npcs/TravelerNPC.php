<?php

namespace SkyBlock\command\npcs;

use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\ShopUtil;

class TravelerNPC extends BaseCommand {

    public const PREFIX = "§l§e[§aSteven The Dimensional Traveler§e]§r ";
    public static array $interacting = [];

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'travelernpc');
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

        $player->sendMessage(self::PREFIX . "Yes, please do enlighten me!");
        $this->sendDimensionMenu($player);
    }

    public function sendDimensionMenu(Player $player) : void {
        $buttons = ["Back", "Nether", "End", "Ocean"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendNetherMenu($player);
                        break;
                    case 2:
                        $this->sendEndMenu($player);
                        break;
                    case 3:
                        $this->sendOceanMenu($player);
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
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a dimension menu -", $buttons, $func);
    }

    public function sendNetherMenu(Player $player) : void {
        $buttons = [
            "Back", "Red Nether Bricks", "Nether Bricks", "Quartz Blocks", "Soul Soil", "Nether Wart Block", "Warped Wart Block",
            "Netherrack", "Obsidian", "Crying Obsidian", "Soul Sand", "Magma Block", "Basalt", "Ender Chest"
        ];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::RED_NETHER_BRICKS()->asItem());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::NETHER_BRICKS()->asItem());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::QUARTZ()->asItem());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::SOUL_SOIL()->asItem());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::NETHER_WART_BLOCK()->asItem());
                        break;
                    case 6:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::WARPED_WART_BLOCK()->asItem());
                        break;
                    case 7:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::NETHERRACK()->asItem());
                        break;
                    case 8:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::OBSIDIAN()->asItem());
                        break;
                    case 9:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CRYING_OBSIDIAN()->asItem());
                        break;
                    case 10:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::SOUL_SAND()->asItem());
                        break;
                    case 11:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::MAGMA()->asItem());
                        break;
                    case 12:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::BASALT()->asItem());
                        break;
                    case 13:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::ENDER_CHEST()->asItem());
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
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a nether block type -", $buttons, $func);
    }

    public function sendEndMenu(Player $player) : void {
        $buttons = ["Back", "Purpur Block", "End Stone"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::PURPUR()->asItem());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::END_STONE()->asItem());
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
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a end block type -", $buttons, $func);
    }

    public function sendOceanMenu(Player $player) : void {
        $buttons = ["Back", "Prismarine", "Dark Prismarine", "Sea Lantern", "Sponge", "Wet Sponge"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::PRISMARINE()->asItem());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::DARK_PRISMARINE()->asItem());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::SEA_LANTERN()->asItem());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::SPONGE()->asItem());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::SPONGE()->setWet(true)->asItem());
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
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select an ocean block type -", $buttons, $func);
    }


}