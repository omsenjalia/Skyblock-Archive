<?php

namespace SkyBlock\command\npcs;

use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\ShopUtil;

class FloristNPC extends BaseCommand {

    public const PREFIX = "§l§e[§aRose The Florist§e]§r ";
    public static array $interacting = [];

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'floristnpc');
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

        $player->sendMessage(self::PREFIX . "Flowers! Flowers! Flowers!");
        $this->sendFlowerMenu($player);
    }

    public function sendFlowerMenu(Player $player) {
        $buttons = [
            "Crimson Roots", "Warped Roots", "Dandelion", "Poppy", "Blue Orchid", "Allium",
            "Azure Bluet", "Red Tulip", "Orange Tulip", "White Tulip", "Pink Tulip", "Oxeye Daisy",
            "Cornflower", "Lily of the Valley", "Sunflower", "Lilac", "Rose Bush", "Peony",
            "Pitcher Plant", "Torchflower", "Pink Petals"
        ];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindow($player, "crimson_roots");
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindow($player, "warped_roots");
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::DANDELION()->asItem());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::POPPY()->asItem());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::BLUE_ORCHID()->asItem());
                        break;
                    case 6:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::ALLIUM()->asItem());
                        break;
                    case 7:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::AZURE_BLUET()->asItem());
                        break;
                    case 8:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::RED_TULIP()->asItem());
                        break;
                    case 9:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::ORANGE_TULIP()->asItem());
                        break;
                    case 10:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::WHITE_TULIP()->asItem());
                        break;
                    case 11:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::PINK_TULIP()->asItem());
                        break;
                    case 12:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::OXEYE_DAISY()->asItem());
                        break;
                    case 13:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CORNFLOWER()->asItem());
                        break;
                    case 14:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::LILY_OF_THE_VALLEY()->asItem());
                        break;
                    case 15:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::SUNFLOWER()->asItem());
                        break;
                    case 16:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::LILAC()->asItem());
                        break;
                    case 17:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::ROSE_BUSH()->asItem());
                        break;
                    case 18:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::PEONY()->asItem());
                        break;
                    case 19:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::PITCHER_PLANT()->asItem());
                        break;
                    case 20:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::TORCHFLOWER()->asItem());
                        break;
                    case 21:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::PINK_PETALS()->asItem());
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Flower -", $buttons, $func);

    }
}