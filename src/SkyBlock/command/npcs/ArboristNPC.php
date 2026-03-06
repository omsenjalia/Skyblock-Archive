<?php

namespace SkyBlock\command\npcs;

use pocketmine\block\utils\DirtType;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\data\bedrock\block\BlockTypeNames;
use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\ShopUtil;

class ArboristNPC extends BaseCommand {

    public const PREFIX = "§l§e[§aGreta The Arborist§e]§r ";
    public static array $interacting = [];

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'arboristnpc');
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
        $this->sendNatureMenu($player);
    }

    public function sendNatureMenu(Player $player) : void {
        $buttons = ["Back", "Leaves", "Dirt", "Coarse Dirt", "Podzol", "Sand", "Clay", "Bone Block"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendLeavesMenu($player);
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::DIRT()->asItem());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::DIRT()->setDirtType(DirtType::COARSE)->asItem());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::PODZOL()->asItem());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::SAND()->asItem());
                        break;
                    case 6:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CLAY()->asItem());
                        break;
                    case 7:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::BONE_BLOCK()->asItem());
                        break;
                    case 8:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::FLOWERING_AZALEA_LEAVES()->asItem());
                        break;
                    case 9:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::AZALEA_LEAVES()->asItem());
                        break;
                    case 10:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::MANGROVE_LEAVES()->asItem());
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
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Block type -", $buttons, $func);
    }

    public function sendLeavesMenu(Player $player) : void {
        $buttons = [
            "Back", "Oak Leaves", "Spruce Leaves", "Birch Leaves", "Jungle Leaves", "Acacia Leaves", "Dark Oak Leaves",
            "Cherry Leaves", "Flowering Azalea Leaves", "Azalea Leaves", "Mangrove Leaves"
        ];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::OAK_LEAVES()->asItem());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::SPRUCE_LEAVES()->asItem());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::BIRCH_LEAVES()->asItem());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::JUNGLE_LEAVES()->asItem());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::ACACIA_LEAVES()->asItem());
                        break;
                    case 6:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::DARK_OAK_LEAVES()->asItem());
                        break;
                    case 7:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CHERRY_LEAVES()->asItem());
                        break;
                    case 8:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::FLOWERING_AZALEA_LEAVES()->asItem());
                        break;
                    case 9:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::AZALEA_LEAVES()->asItem());
                        break;
                    case 10:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::MANGROVE_LEAVES()->asItem());
                        break;
                    default:
                        $this->sendNatureMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Leaf type -", $buttons, $func);
    }

}