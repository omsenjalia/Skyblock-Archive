<?php

namespace SkyBlock\command\npcs;

use pocketmine\command\CommandSender;
use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\form\CustomForm;
use SkyBlock\Main;

class EnchanterNPC extends BaseCommand {

    public const PREFIX = "§l§e[§aNissan The Enchanter§e]§r ";
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
        $this->sendEnchantMenu($player);
    }

    public function sendEnchantMenu(Player $player) : void {
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
                        break;
                }
            }
        };
        $title = "§6§lEnchants";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select an Enchant to buy -", $buttons, $func);
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
                        $this->sendEnchantMenu($player);
                        break;
                }
            }
        };
        $title = "§6§lEnchants";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Level -", $buttons, $func);
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
                $item = StringToItemParser::getInstance()->parse(ItemTypeNames::ENDER_EYE)->setCount($amount);
                $item->setCustomName(TextFormat::RESET . TextFormat::BOLD . " §6$enchname §r§9Enchantment Orb \n §aLevel: §6$level \n §3ID: §6$enchid \n §eUse this on a tool or armor by /ench ");
                if (!$player->getInventory()->canAddItem($item)) {
                    $error = "§6Your Inventory is full!";
                    $this->sendResultForm($player, $error, "sendEnchantMainMenu");
                    return;
                }
                $player->getInventory()->addItem($item);
                $player->sendMessage("§eSucceed bought §7x§c$amount §a$enchname §eEnchantment orb of level §b$level §efor §6$cost$!");
            }
        };
        Main::getInstance()->getFormFunctions()->sendModalForm($player, "Checkout:", "Are you sure you wanna buy x$amount $enchname Enchant of Level $level? Total Price: $cost$!\n§7Unlock all Enchants by getting /enchant from shop.fallentech.io", ["Yes", "No"], $func);
    }

    public function sendResultForm(Player $player, string $message, string $func, array $args = []) : void {
        Main::getInstance()->getFormFunctions()->sendModalForm($player, "§6Result", $message, ["§2Go back", "§cExit"], function(Player $player, ?bool $data) use ($func, $args) {
            if ($data) {
                assert(method_exists($this, $func));
                array_unshift($args, $player);
                call_user_func_array([$this, $func], $args);
            }
        }
        );
    }

}