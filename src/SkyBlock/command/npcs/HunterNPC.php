<?php

namespace SkyBlock\command\npcs;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\form\CustomForm;
use SkyBlock\Main;

class HunterNPC extends BaseCommand {

    public const PREFIX = "§l§e[§aHunter The Hunter§e]§r ";
    public static array $interacting = [];

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'hunternpc');
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

        $player->sendMessage(self::PREFIX . "I got all types of mob spawners for ya!");
        $this->sendSpawnerMenu($player);
    }

    public function sendSpawnerMenu(Player $player) : void {
        $buttons = ["Exit"];
        foreach (Main::getInstance()->spawners as $name => $data) {
            $name = ucfirst($name);
            $buttons[] = "§f$name";
        }
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $spawner = "chicken";
                        $info = "Chicken Spawner -\nCost - $" . number_format(Main::getInstance()->spawners[$spawner]["cost"]);
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 2:
                        $spawner = "pig";
                        $info = "Pig Spawner -\nCost - $" . number_format(Main::getInstance()->spawners[$spawner]["cost"]);
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 3:
                        $spawner = "cow";
                        $info = "Cow Spawner -\nCost - $" . number_format(Main::getInstance()->spawners[$spawner]["cost"]);
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 4:
                        $spawner = "sheep";
                        $info = "Sheep Spawner -\nCost - $" . number_format(Main::getInstance()->spawners[$spawner]["cost"]);
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 5:
                        $spawner = "squid";
                        $info = "Squid Spawner -\nCost - $" . number_format(Main::getInstance()->spawners[$spawner]["cost"]);
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 6:
                        $spawner = "goat";
                        $info = "Goat Spawner -\nCost - $" . number_format(Main::getInstance()->spawners[$spawner]["cost"]);
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 7:
                        $spawner = "glowsquid";
                        $info = "Glow Squid Spawner -\nCost - $" . number_format(Main::getInstance()->spawners[$spawner]["cost"]);
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 8:
                        $spawner = "camel";
                        $info = "Camel Spawner -\nCost - $" . number_format(Main::getInstance()->spawners[$spawner]["cost"]);
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 9:
                        $spawner = "panda";
                        $info = "Panda Spawner -\nCost - $" . number_format(Main::getInstance()->spawners[$spawner]["cost"]);
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 10:
                        $spawner = "spider";
                        $info = "Spider Spawner -\nCost - $" . number_format(Main::getInstance()->spawners[$spawner]["cost"]);
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 11:
                        $spawner = "pigman";
                        $info = "Zombie Pigman Spawner -\nCost - $" . number_format(Main::getInstance()->spawners[$spawner]["cost"]);
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 12:
                        $spawner = "zombie";
                        $info = "Zombie Spawner -\nCost - $" . number_format(Main::getInstance()->spawners[$spawner]["cost"]);
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 13:
                        $spawner = "skeleton";
                        $info = "Skeleton Spawner -\nCost - $" . number_format(Main::getInstance()->spawners[$spawner]["cost"]);
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 14:
                        $spawner = "polarbear";
                        $info = "Polar Bear Spawner -\nCost - $" . number_format(Main::getInstance()->spawners[$spawner]["cost"]);
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 15:
                        $spawner = "creeper";
                        $info = "Creeper Spawner -\nCost - $" . number_format(Main::getInstance()->spawners[$spawner]["cost"]);
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 16:
                        $spawner = "irongolem";
                        $info = "Iron Golem Spawner -\nCost - $" . number_format(Main::getInstance()->spawners[$spawner]["cost"]);
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 17:
                        $spawner = "silverfish";
                        $info = "Silverfish Spawner -\nCost - $" . number_format(Main::getInstance()->spawners[$spawner]["cost"]);
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    case 18:
                        $spawner = "blaze";
                        $info = "Blaze Spawner -\nCost - $" . number_format(Main::getInstance()->spawners[$spawner]["cost"]);
                        $this->sendSpawnerAmount($player, $spawner, $info);
                        break;
                    default:
                        break;
                }
            }
        };
        $title = "§b§lSpawners";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§3Select a Spawner for info -", $buttons, $func);
    }

    public function sendSpawnerAmount(Player $player, string $spawner, string $info) : void {
        $form = new CustomForm(null);
        $form->setTitle("§6§lSpawner");
        $form->addLabel("Please choose how many " . ucfirst($spawner) . " Spawners you want to buy?\n");
        $form->addInput("Amount -", "", "1");
        $form->setCallable(function(Player $player, ?array $data) use ($spawner, $info) : void {
            if ($data !== null) {
                if (!is_int((int) $data[1]) || empty($data[1])) {
                    $error = "§6Please enter a number!";
                    $this->sendResultForm($player, $error, "sendSpawnerAmount", [$spawner, $info]);
                    return;
                }
                $amount = $data[1];
                if ($amount < 1 || $amount > 500) {
                    $error = "§6Please enter a number greater than 0 and less than 500!";
                    $this->sendResultForm($player, $error, "sendSpawnerAmount", [$spawner, $info]);
                    return;
                }
                $this->sendSpawnerInfo($player, $spawner, $info, $amount);
            }
        }
        );
        $player->sendForm($form);
    }

    public function sendSpawnerInfo(Player $player, string $spawner, string $info, int $amount) : void {
        $func = function(Player $player, ?bool $data) use ($spawner, $amount) : void {
            if ($data !== null) {
                if ($data) {
                    if ($spawner === "iron_golem") {
                        $spawner = "irongolem";
                    }
                    $item = Main::getInstance()->getEvFunctions()->getSpawnerBlock(Main::getInstance()->spawners[$spawner]["id"], 1, $amount);
                    if (!$player->getInventory()->canAddItem($item)) {
                        $error = "§6Your Inventory is full!";
                        $this->sendResultForm($player, $error, "sendSpawnerMenu");
                        return;
                    }
                    $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
                    $money = Main::getInstance()->spawners[$spawner]["cost"] * $amount;
                    if ($user->removeMoney($money)) {
                        $error = "§6You don't have enough money to purchase that much! Required money: $$money";
                        $this->sendResultForm($player, $error, "sendSpawnerMenu");
                        return;
                    }
                    $player->getInventory()->addItem($item);
                    $player->sendMessage("§8- §aSucceed bought x$amount of $spawner Spawners!");
                } else {
                    $this->sendSpawnerMenu($player);
                }
            }
        };
        Main::getInstance()->getFormFunctions()->sendModalForm($player, "Do you wanna buy x$amount of $spawner spawners?", $info, ["Yes", "No"], $func);
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