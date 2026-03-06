<?php

namespace SkyBlock\command\npcs;

use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\ShopUtil;

class FoodCriticNPC extends BaseCommand {

    public const PREFIX = "§l§e[§aPhil The Food Critic§e]§r ";
    public static array $interacting = [];

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'foodcriticnpc');
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

        $player->sendMessage(self::PREFIX . "Hi there. I sell all types of food!");
        $this->sendFoodMenu($player);

    }

    public function sendFoodMenu(Player $player) : void {
        $buttons = ["Back", "Golden Apple", "Enchanted Golden Apple"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::GOLDEN_APPLE());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaItems::ENCHANTED_GOLDEN_APPLE());
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
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Food type -", $buttons, $func);
    }
}