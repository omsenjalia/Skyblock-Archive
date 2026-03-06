<?php

namespace SkyBlock\command\npcs;

use pocketmine\command\CommandSender;
use pocketmine\item\PotionType;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\ShopUtil;

class AlchemistNPC extends BaseCommand {

    public const PREFIX = "§l§e[§aNicolas The Alchemist§e]§r ";
    public static array $interacting = [];

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'alchemistnpc');
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
        $this->sendPotionsMenu($player);
    }

    public function sendPotionsMenu(Player $player) : void {
        $buttons = [
            "Back", "Glass Bottle", "Water Bottle", "Mundane Potion", "Awkward Potion", "NightVision Potion",
            "Invisibility Potion", "FireResistance Potion", "Swiftness Potion", "Slowness Potion",
            "WaterBreathing Potion", "Regeneration Potion", "Strength Potion", "Weakness Potion"
        ];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::GLASS_BOTTLE());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::POTION()->setType(PotionType::WATER));
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::POTION()->setType(PotionType::MUNDANE));
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::POTION()->setType(PotionType::AWKWARD));
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::POTION()->setType(PotionType::NIGHT_VISION));
                        break;
                    case 6:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::POTION()->setType(PotionType::INVISIBILITY));
                        break;
                    case 7:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::POTION()->setType(PotionType::FIRE_RESISTANCE));
                        break;
                    case 8:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::POTION()->setType(PotionType::SWIFTNESS));
                        break;
                    case 9:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::POTION()->setType(PotionType::SLOWNESS));
                        break;
                    case 10:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::POTION()->setType(PotionType::WATER_BREATHING));
                        break;
                    case 11:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::POTION()->setType(PotionType::REGENERATION));
                        break;
                    case 12:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::POTION()->setType(PotionType::STRENGTH));
                        break;
                    case 13:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::POTION()->setType(PotionType::WEAKNESS));
                        break;
                    default:
                        break;
                }
            }
        };
        $title = "§6§lPotions";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Potion to buy -", $buttons, $func);
    }

}