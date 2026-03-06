<?php

namespace SkyBlock\command;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class GivePet extends BaseCommand {
    /**
     * GivePet constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'givepet', 'Give pet to a player', '<player> <pet>', true, [], "core.givepet");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!Main::getInstance()->hasOp($sender)) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if ($sender instanceof Player && !Main::getInstance()->isTrusted($sender->getName())) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (!isset($args[0]) || !isset($args[1])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /givepet <player> <pet>");
            return;
        }
        $player = strtolower($args[0]);
        $pet = strtolower($args[1]);
        if (!Main::getInstance()->getDb()->isPlayerRegistered($player)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player has never connected!");
            return;
        }
        $pets = Main::getInstance()->getAllPets();
        $index = -1;
        foreach ($pets as $key => $p) {
            if (strtolower($p) === $pet) {
                $index = $key;
            }
        }
        if ($index === -1) {
            $this->sendMessage($sender, TextFormat::YELLOW . "That pet was not found. Use /pets to see available pets in 'Buy a Pet'");
            return;
        } else {
            $pet = $pets[$index];
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
        if ($user === null) {
            $data = Main::getInstance()->getDb()->getPetsData($player);
            $petString = $data["unlocked"];
            $unlockedPets = [];
            if ($petString !== "") {
                $unlockedPets = explode(",", $petString);
            }
            if (in_array($pet, $unlockedPets, true)) {
                $this->sendMessage($sender, TextFormat::GREEN . $player . " already has unlocked the " . $pet . " pet!");
            } else {
                $unlockedPets[] = $pet;
                Main::getInstance()->getDb()->setUserPets($player, implode(",", $unlockedPets));
                $this->sendMessage($sender, TextFormat::GREEN . "Gave " . $pet . " to " . $player . "!");
            }
            return;
        }
        if ($user->hasPet($pet)) {
            $user->setPet($pet);
            if ($user->getPlayer()->getWorld()->getDisplayName() !== "PvP") {
                $pe = Main::getInstance()->createPet($pet, $user->getPlayer(), $user->getPetName());
                if ($pe === null) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Error while creating pet!");
                    return;
                }
                $pe->spawnToAll();
                $pe->setDormant(false);
            }
            $this->sendMessage($sender, TextFormat::YELLOW . $player . " already had " . $pet . " pet. It has been set as their active pet!");
            $this->sendMessage($user->getPlayer(), TextFormat::YELLOW . $pet . " is not set as your active pet. Use /pets for customization!");
            return;
        }
        $user->addPet($pet);
        $user->setPet($pet);
        if ($user->getPlayer()->getWorld()->getDisplayName() !== "PvP") {
            $pe = Main::getInstance()->createPet($pet, $user->getPlayer(), $user->getPetName());
            if ($pe === null) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Error while creating pet!");
                return;
            }
            $pe->spawnToAll();
            $pe->setDormant(false);
        }
        $this->sendMessage($sender, TextFormat::YELLOW . "Gave $pet pet to $player!");
        $this->sendMessage($user->getPlayer(), TextFormat::YELLOW . "You have unlocked the $pet pet. $pet is now your active pet. Use /pets for customization!");
    }
}