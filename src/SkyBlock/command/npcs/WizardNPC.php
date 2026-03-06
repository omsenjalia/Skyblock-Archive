<?php

namespace SkyBlock\command\npcs;

use jojoe77777\FormAPI\CustomForm;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class WizardNPC extends BaseCommand {

    public const PREFIX = "§l§e[§aHakim The Wizard§e]§r ";
    public static array $interacting = [];

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'wizardnpc');
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

        $player->sendMessage(self::PREFIX . "Greetings, mortal! I trade in wild, magical enchant books!");
        $this->sendCEBooksMenu($player);
    }


    public function sendCEBooksMenu(Player $player) : void {
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
        $buttons = ["Back", "§6§lCommon", "§6§lRare", "§6§lLegendary", "§b§lExclusive", "§1§lAncient", "§c§lVaulted"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendCEBooksAmount($player, "common", "Common Custom Enchant Book - \nPer Cost - 2,000 XP");
                        break;
                    case 2:
                        $this->sendCEBooksAmount($player, "rare", "Rare Custom Enchant Book - \nPer Cost - 5,000 XP");
                        break;
                    case 3:
                        $this->sendCEBooksAmount($player, "legendary", "Legendary Custom Enchant Book - \nPer Cost - 20,000 XP");
                        break;
                    case 4:
                        $this->sendCEBooksAmount($player, "exclusive", "Exclusive Custom Enchant Book - \nPer Cost - 500,000 XP");
                        break;
                    case 5:
                        $this->sendCEBooksAmount($player, "ancient", "Ancient Custom Enchant Book - \nPer Cost - 10,000,000 XP");
                        break;
                    case 6:
                        $error = "VAULTED CEs are only available via Godly Relic! They cant be obtained via Books or envoys, their production is stopped but they still work.";
                        $this->sendResultForm($player, $error, "sendCEBooksMenu");
                        break;
                    default:
                        break;
                }
            }
        };
        $title = "§4§lCEBooks";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Your XP - §f" . number_format($user->getXP()), $buttons, $func);
    }

    public function sendCEBooksAmount(Player $player, string $cebook, string $info) : void {
        $form = new CustomForm(null);
        $title = "§4§lCEBooks";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§rsmall";
        }
        $form->setTitle($title);
        $form->addLabel("Please choose how many " . ucfirst($cebook) . " CE books you want to buy?\n" . $info);
        $form->addInput("Amount -", "", "1");
        $form->setCallable(function(Player $player, ?array $data) use ($cebook, $info) : void {
            if ($data !== null) {
                if (!is_int((int) $data[1]) || empty($data[1])) {
                    $error = "§6Please enter a number!";
                    $this->sendResultForm($player, $error, "sendCEBooksAmount", [$cebook, $info]);
                    return;
                }
                $amount = (int) $data[1];
                if ($amount < 1 || $amount > 1000) {
                    $error = "§6Please enter a number greater than 0 and less than 1000!";
                    $this->sendResultForm($player, $error, "sendCEBooksAmount", [$cebook, $info]);
                    return;
                }
                $this->sendCEBooksInfo($player, $cebook, $amount);
            }
        }
        );
        $player->sendForm($form);
    }

    public function sendCEBooksInfo(Player $player, string $cebook, int $amount) : void {
        $func = function(Player $player, ?bool $data) use ($cebook, $amount) : void {
            if ($data !== null) {
                if ($data) {
                    $item = Main::getInstance()->getCEBook($cebook, $amount);
                    if (!$player->getInventory()->canAddItem($item)) {
                        $error = "§6Your Inventory is full!";
                        $this->sendResultForm($player, $error, "sendCEBooksMenu");
                        return;
                    }
                    if ($player->getXpManager()->getCurrentTotalXp() < ($cost = $this->getCost($cebook, $amount))) {
                        $error = "§6You don't have enough XP to purchase that much! Required XP: $cost! Check your XP by /myxp";
                        $this->sendResultForm($player, $error, "sendCEBooksMenu");
                        return;
                    }
                    $player->getXpManager()->addXp(-$cost, false);
                    $player->getInventory()->addItem($item);
                    $nam = ucfirst($cebook);
                    Main::getInstance()->getFormFunctions()->sendMessage($player, "§aSuccessfully bought x$amount of $nam Custom Enchant Book for $cost XP! Check your inventory for a $nam book and tap a block to redeem $nam Enchantment Book!");
                }
                $this->sendCEBooksMenu($player);
            }
        };
        Main::getInstance()->getFormFunctions()->sendModalForm($player, "Checkout", "Are you sure you wanna buy x$amount of " . ucfirst($cebook) . " CEBooks for {$this->getCost($cebook, $amount)}XP?", ["Yes", "No"], $func);
    }

    private function getCost($name, $amount) : int {
        return (int) match ($name) {
            'common' => 2000 * $amount,
            'rare' => 5000 * $amount,
            'legendary' => 20000 * $amount,
            'exclusive' => 500000 * $amount,
            'ancient' => 10000000 * $amount,
            default => 2000,
        };
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