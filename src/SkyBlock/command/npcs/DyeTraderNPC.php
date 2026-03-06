<?php

namespace SkyBlock\command\npcs;

use pocketmine\block\utils\DyeColor;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\ShopUtil;

class DyeTraderNPC extends BaseCommand {

    public const PREFIX = "§l§e[§aAhinadab The Dye Trader§e]§r ";
    public static array $interacting = [];

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'dyetradernpc');
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
        $this->sendDyeMenu($player);
    }

    public function sendDyeMenu(Player $player) : void {
        $buttons = ["Back", "Black Dye", "Red Dye", "Green Dye", "Brown Dye", "Blue Dye", "Purple Dye", "Cyan Dye", "Light Gray Dye", "Gray Dye", "Pink Dye", "Lime Dye", "Yellow Dye", "Light Blue Dye", "Magenta Dye", "Orange Dye", "White Dye"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::BLACK));
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::RED));
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::GREEN));
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::BROWN));
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::BLUE));
                        break;
                    case 6:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::PURPLE));
                        break;
                    case 7:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::CYAN));
                        break;
                    case 8:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::LIGHT_GRAY));
                        break;
                    case 9:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::GRAY));
                        break;
                    case 10:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::PINK));
                        break;
                    case 11:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::LIME));
                        break;
                    case 12:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::YELLOW));
                        break;
                    case 13:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::LIGHT_BLUE));
                        break;
                    case 14:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::MAGENTA));
                        break;
                    case 15:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::ORANGE));
                        break;
                    case 16:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DYE()->setColor(DyeColor::WHITE));
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
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Dye type -", $buttons, $func);
    }

}