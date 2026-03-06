<?php

namespace SkyBlock\command\npcs;

use pocketmine\block\utils\FroglightType;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\ShopUtil;

class LightsNPC extends BaseCommand {

    public const PREFIX = "§l§e[§aHarry The Electrician§e]§r ";
    public static array $interacting = [];

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'lightsnpc');
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
        $this->sendLightsMenu($player);
    }

    public function sendLightsMenu(Player $player) : void {
        $buttons = [
            "Back", "Candles", "End Rod", "Glowstone",
            "Shroomlight", "Ochre Froglight", "Pearlescent Froglight", "Verdant Froglight",
            "Jack o'Lantern", "Lantern", "Redstone Lamp", "Soul Lantern", "Sea Lantern",
            "Sea Pickle", "Soul Torch", "Torch"
        ]; // todo add campfire/soulcampfire
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendCandleMenu($player);
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::END_ROD()->asItem());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLOWSTONE()->asItem());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::SHROOMLIGHT()->asItem());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::FROGLIGHT()->setFroglightType(FroglightType::OCHRE)->asItem());
                        break;
                    case 6:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::FROGLIGHT()->setFroglightType(FroglightType::PEARLESCENT)->asItem());
                        break;
                    case 7:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::FROGLIGHT()->setFroglightType(FroglightType::VERDANT)->asItem());
                        break;
                    case 8:
                        ShopUtil::getInstance()->sendAmountWindow($player, "jack_o_lantern");
                        break;
                    case 9:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::LANTERN()->asItem());
                        break;
                    case 10:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::REDSTONE_LAMP()->asItem());
                        break;
                    case 11:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::SOUL_LANTERN()->asItem());
                        break;
                    case 12:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::SEA_LANTERN()->asItem());
                        break;
                    case 13:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::SEA_PICKLE()->asItem());
                        break;
                    case 14:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::SOUL_TORCH()->asItem());
                        break;
                    case 15:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::TORCH()->asItem());
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
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Light block -", $buttons, $func);
    }

    public function sendCandleMenu(Player $player) : void {
        $buttons = [
            "Back", "Black Candle", "Brown Candle", "Candle",
            "Cyan Candle", "Green Candle", "Grey Candle", "Light Blue Candle",
            "Light Grey Candle", "Lime Candle", "Magenta Candle", "Orange Candle", "Pink Candle",
            "Purple Candle", "Red Candle", "White Candle", "Yellow Candle"
        ];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindow($player, "black_candle");
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindow($player, "brown_candle");
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindow($player, "candle");
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindow($player, "cyan_candle");
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindow($player, "green_candle");
                        break;
                    case 6:
                        ShopUtil::getInstance()->sendAmountWindow($player, "grey_candle");
                        break;
                    case 7:
                        ShopUtil::getInstance()->sendAmountWindow($player, "light_blue_candle");
                        break;
                    case 8:
                        ShopUtil::getInstance()->sendAmountWindow($player, "light_grey_candle");
                        break;
                    case 9:
                        ShopUtil::getInstance()->sendAmountWindow($player, "lime_candle");
                        break;
                    case 10:
                        ShopUtil::getInstance()->sendAmountWindow($player, "magenta_candle");
                        break;
                    case 11:
                        ShopUtil::getInstance()->sendAmountWindow($player, "orange_candle");
                        break;
                    case 12:
                        ShopUtil::getInstance()->sendAmountWindow($player, "pink_candle");
                        break;
                    case 13:
                        ShopUtil::getInstance()->sendAmountWindow($player, "purple_candle");
                        break;
                    case 14:
                        ShopUtil::getInstance()->sendAmountWindow($player, "red_candle");
                        break;
                    case 15:
                        ShopUtil::getInstance()->sendAmountWindow($player, "white_candle");
                        break;
                    case 16:
                        ShopUtil::getInstance()->sendAmountWindow($player, "yellow_candle");
                        break;
                    default:
                        $this->sendLightsMenu($player);
                        break;

                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Candle block -", $buttons, $func);
    }
}