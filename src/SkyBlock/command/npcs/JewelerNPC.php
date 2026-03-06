<?php

namespace SkyBlock\command\npcs;

use pocketmine\command\CommandSender;
use pocketmine\item\PotionType;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\ShopUtil;

class JewelerNPC extends BaseCommand {

    public const PREFIX = "§l§e[§aTiffany The Jeweler§e]§r ";
    public static array $interacting = [];

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'jeweler');
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
        $this->sendJewelerMenu($player);
    }


    public function sendJewelerMenu(Player $player) : void {
        $buttons = [
            "Back", "Leather", "Coal", "Gold Ingot", "Iron Ingot", "Diamond", "Lapis Lazuli", "Prismarine Crystals",
            "Prismarine Shard", "Glow Ink Sac"
        ];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::LEATHER());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::COAL());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::GOLD_INGOT());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::IRON_INGOT());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::DIAMOND());
                        break;
                    case 6:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::LAPIS_LAZULI());
                        break;
                    case 7:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::PRISMARINE_CRYSTALS());
                        break;
                    case 8:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::PRISMARINE_SHARD());
                        break;
                    case 9:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::GLOW_INK_SAC());
                        break;
                    default:
                        break;
                }
            }
        };
        $title = "§6§lResources";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a resource to buy -", $buttons, $func);
    }

}