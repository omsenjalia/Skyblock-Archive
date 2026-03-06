<?php

namespace SkyBlock\command\npcs;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\form\CustomForm;
use SkyBlock\Main;

class PetHandlerNPC extends BaseCommand {

    public const PREFIX = "§l§e[§aNala The Pet Handler§e]§r ";
    public static array $interacting = [];

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'pethandlernpc');
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
        $this->sendPetMenu($player);
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

    public function sendPetMenu(Player $player) : void {
        $buttons = ["Buy a Pet", "Choose your active Pet", "Change Pet Name", "Change Pet Size", "Clear Pet", "Toggle Follow", "Back"];
        $func = function(Player $player, ?int $data) {
            if ($data !== null) {
                switch ($data) {
                    case 0:
                        $this->sendPetSelectMenu($player);
                        break;
                    case 1:
                        $this->sendPetChooseMenu($player);
                        break;
                    case 2:
                        $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
                        if (!$user->hasSetPet()) {
                            $result = "You dont have an active pet to name! Choose a pet from /pets";
                            $this->sendResultForm($player, $result, "sendPetMenu");
                            return;
                        }
                        $this->sendPetChangeNameMenu($player);
                        break;
                    case 3:
                        $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
                        if (!$user->hasSetPet()) {
                            $result = "You dont have an active pet to size! Choose a pet from /pets";
                            $this->sendResultForm($player, $result, "sendPetMenu");
                            return;
                        }
                        $this->sendPetChangeSizeMenu($player);
                        break;
                    case 4:
                        $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
                        if ($user->hasSetPet()) {
                            $user->setPet();
                            $result = "Pet cleared!";
                            if (!empty($pet = Main::getInstance()->getPetsFrom($player))) {
                                foreach ($pet as $p) {
                                    Main::getInstance()->removePet($p);
                                }
                            }
                        } else {
                            $result = "You dont have an active pet to clear!";
                        }
                        $this->sendResultForm($player, $result, "sendPetMenu");
                        break;
                    case 5:
                        if (isset(Main::getInstance()->dontFollow[$player->getName()])) {
                            $player->sendMessage("§ePet will follow you now!");
                            unset(Main::getInstance()->dontFollow[$player->getName()]);
                        } else {
                            $player->sendMessage("§cPet wont follow you anymore!");
                            Main::getInstance()->dontFollow[$player->getName()] = true;
                        }
                        break;
                    default:
                        break;
                }
            }
        };
        $title = "§a§lPets";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select an Option -", $buttons, $func);
    }

    public function sendPetSelectMenu(Player $player) : void {
        $buttons = $pets = [];
        foreach (Main::getInstance()->pets as $name) {
            $buttons[] = $name;
            $pets[] = $name;
        }
        $func = function(Player $player, ?int $data) use ($pets) {
            if ($data !== null) {
                if (isset($pets[$data])) {
                    $sel = $pets[$data];
                    $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
                    if ($user->hasPet($sel)) {
                        $result = "You have already unlocked that pet! Select it from /pets";
                        $this->sendResultForm($player, $result, "sendPetMenu");
                    } elseif ($sel === "SnowFox" && Main::getInstance()->getEvFunctions()->hasStaffRank($user->getPlayer()->getName())) {
                        $user->addPet("SnowFox");
                        $result = "Unlocked SnowFox Pet! Select it from /pets";
                        $this->sendResultForm($player, $result, "sendPetMenu");
                    } else {
                        $this->sendPetInfo($player, $sel);
                    }
                } else {
                    $result = "Pet not found";
                    $this->sendResultForm($player, $result, "sendPetMenu");
                }
            }
        };
        $title = "§a§lPets";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Pet -", $buttons, $func);
    }

    public function sendPetInfo(Player $player, string $pet) : void {
        $properties = Main::getInstance()->getPetProperties()->getPropertiesFor($pet);
        $type = (string) $properties["Type"];
        $content = "";
        $buttons = ["Yes", "No"];
        $price = 0;
        if ($type == 'Common' or $type == 'Rare') {
            $price = (string) $properties["Price"];
            $content = "§6Do you wanna unlock §a$pet §6pet for §e" . number_format($price) . "§6$?\n§fYou wont lose your old pet if you unlock a new pet.";
        } else {
            $buttons = ["Go Back", "Go Back"];
            if ($type == 'Premium') {
                $content = "§6Sorry, §a$pet §6Pet is §bPremium, §6you can only get it from §aour store! §bBuy Premium pets from shop.fallentech.io!";
            }
            if ($type == 'Exclusive') {
                $content = "§6Sorry, §a$pet §6Pet is §eExclusive, §6Only available via Giveaways or Events on Discord -> http://discord.fallentech.io";
            }
            if ($type == 'Staff') {
                $content = "§6Sorry, §a$pet §6Pet is for §eStaff, §6Only Staff can use it!";
            }
            if ($type == 'IslandChamp') {
                $content = "§6Sorry, §a$pet §6Pet is for §eIslandChamps, §6Only the winners of Skyblock seasons can recieve this pet!!";
            }
        }
        $func = function(Player $player, ?bool $data) use ($pet, $price, $type) {
            if ($data !== null) {
                if ($data) {
                    if ($type != "Common" and $type != "Rare") {
                        $this->sendPetSelectMenu($player);
                        return;
                    }
                    $this->sendPetNameMenu($player, $pet, $price);
                } else $this->sendPetSelectMenu($player);
            }
        };
        Main::getInstance()->getFormFunctions()->sendModalForm($player, "§bUnlock Pet", $content, $buttons, $func);
    }

    public function sendPetNameMenu(Player $player, string $pet, string $price) : void {
        $form = new CustomForm(null);
        $form->setTitle("§eChoose Pet Name");
        $form->addInput("Enter the name you want to name your pet -", "", "Name");
        $form->setCallable(function(Player $player, ?array $data) use ($pet, $price) {
            if ($data !== null) {
                $name = (string) $data[0];
                if (!(ctype_alnum($name))) {
                    $result = "That name is not valid! You can only keep numbers and alphabets as pet name!";
                    $this->sendResultForm($player, $result, "sendPetSelectMenu");
                    return;
                }
                if (strlen($name) > 15) {
                    $result = "Pet name cannot have more than 15 letters!";
                    $this->sendResultForm($player, $result, "sendPetSelectMenu");
                    return;
                }
                $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
                if (!$user->removeMoney($price)) {
                    $result = "You dont have enough money to unlock that pet! Require money: $price$";
                    $this->sendResultForm($player, $result, "sendPetSelectMenu");
                    return;
                }
                if (($pets = Main::getInstance()->createPet($pet, $player, $name)) === null) {
                    $result = "Error creating pet!";
                    $this->sendResultForm($player, $result, "sendPetSelectMenu");
                    return;
                }
                $user->addPet($pet);
                $user->setPet($pet);
                $user->setPetName($name);
                $pets->spawnToAll();
                $pets->setDormant(false);
                $result = "§6You have successfully unlocked §a$pet §6pet! §a$pet §6is set as your active pet! §fPet Name: '§b$name'";
                $this->sendResultForm($player, $result, "sendPetSelectMenu");
            }
        }
        );
        $player->sendForm($form);
    }

    public function sendPetChooseMenu(Player $player) : void {
        $buttons = [];
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
        foreach ($user->getUnlockedPets() as $pet) {
            $buttons[] = $pet;
        }
        $buttons[] = "Back";
        $func = function(Player $player, ?int $data) {
            if ($data !== null) {
                if ($player->getWorld()->getDisplayName() == "PvP") {
                    $player->sendMessage("§4[Error] §cYou can't select pets here!");
                    return;
                }
                if ($player->hasNoClientPredictions()) {
                    $player->sendMessage("§4[Error] §cYou are frozen!");
                    return;
                }
                $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
                $pets = $user->getUnlockedPets();
                if (isset($pets[$data])) {
                    $pet = $pets[$data];
                    if ($user->hasPet($pet)) {
                        $user->setPet($pet);
                        $pets = Main::getInstance()->createPet($pet, $player, $user->getPetName());
                        if (!is_null($pets)) {
                            $pets->spawnToAll();
                            $pets->setDormant(false);
                            $result = "You have selected your $pet pet!";
                            $this->sendResultForm($player, $result, "sendPetMenu");
                        }
                    } else {
                        $result = "You havent unlocked this pet yet! Buy it from /pets";
                        $this->sendResultForm($player, $result, "sendPetMenu");
                    }
                } else {
                    $this->sendPetMenu($player);
                }
            }
        };
        $title = "§a§lPets";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Choose an unlocked Pet -", $buttons, $func);
    }

    public function sendPetChangeNameMenu(Player $player) : void {
        $form = new CustomForm(null);
        $form->setTitle("§eChange Pet Name for 10000$");
        $form->addInput("Enter the name you want to name your pet -", "", "Name");
        $form->setCallable(function(Player $player, ?array $data) : void {
            if ($data !== null) {
                $name = (string) $data[0];
                if (!(ctype_alnum($name))) {
                    $result = "That name is not valid! You can only keep numbers and alphabets as pet name!";
                    $this->sendResultForm($player, $result, "sendPetMenu");
                    return;
                }
                if (strlen($name) > 15) {
                    $result = "Pet name cannot have more than 15 letters!";
                    $this->sendResultForm($player, $result, "sendPetMenu");
                    return;
                }
                $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
                if (!$user->removeMoney(10000)) {
                    $result = "You dont have enough money to unlock that pet! Require money: 10,000$";
                    $this->sendResultForm($player, $result, "sendPetMenu");
                    return;
                }
                $user->setPetName($name);
                if (!empty($pet = Main::getInstance()->getPetsFrom($player))) {
                    foreach ($pet as $p) {
                        $p->changeName($name);
                    }
                }
                $result = "§6You have successfully change your Pets name to '§b$name'";
                $this->sendResultForm($player, $result, "sendPetMenu");
            }
        }
        );
        $player->sendForm($form);
    }

    public function sendPetChangeSizeMenu(Player $player) : void {
        $form = new CustomForm(null);
        $form->setTitle("§eChange Pet Size");
        $form->addLabel("§fSelect the size you want your pet to be -");
        $form->addDropdown("§3Size -", ["Small", "Normal", "Large"], 1);
        $form->setCallable(function(Player $player, ?array $data) : void {
            if ($data !== null) {
                $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
                $pet = $user->getSelectedPet();
                $properties = Main::getInstance()->getPetProperties()->getPropertiesFor($pet);
                $normal = (float) $properties["Size"];
                $min = (float) $properties["Min-Size"];
                $max = (float) $properties["Max-Size"];
                $size = (int) $data[1];
                $sel = [];
                if (!empty($pet = Main::getInstance()->getPetsFrom($player))) {
                    foreach ($pet as $p) {
                        $sel = $p;
                    }
                }
                if (is_array($sel)) {
                    return;
                }
                $s = "";
                switch ($size) {
                    case 0:
                        $s = "small";
                        $sel->setScale($min);
                        break;
                    case 1:
                        $s = "normal";
                        $sel->setScale($normal);
                        break;
                    case 2:
                        $s = "large";
                        $sel->setScale($max);
                        break;
                }
                $result = "§6You have successfully change your Pets size to '§b$s'";
                $this->sendResultForm($player, $result, "sendPetMenu");
            }
        }
        );
        $player->sendForm($form);
    }

}