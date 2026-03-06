<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Data;
use SkyBlock\Main;
use SkyBlock\util\Values;

class Heal extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'heal', 'Heal a player');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!Main::getInstance()->staffapi->hasStaffRank($sender->getName())) {
            if (!$sender->hasPermission("core.heal")) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy Myth rank on " . TextFormat::AQUA . "shop.fallentech.io");
                return;
            }
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
        if (!$user->hasMoney(Data::$commandHealCost)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You need " . Data::$commandHealCost . " to heal yourself!");
            return;
        }
        if (!isset($args[0])) {
            if (in_array($sender->getPosition()->getWorld()->getDisplayName(), Values::PVP_WORLDS, true)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot use that command here!");
                return;
            }
            $sender->heal(new EntityRegainHealthEvent($sender, $sender->getMaxHealth() - $sender->getHealth(), EntityRegainHealthEvent::CAUSE_CUSTOM));
            $user->removeMoney(Data::$commandHealCost);
            $this->sendMessage($sender, TextFormat::YELLOW . "You have healed yourself!");
            return;
        }
        $player = strtolower($args[0]);
        $user2 = Main::getInstance()->getUserManager()->getOnlineUser($player);
        if ($user2 === null) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
            return;
        }
        if (Main::getInstance()->isInCombat($user2->getPlayer())) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is in combat!");
            return;
        }
        if (in_array($user2->getPlayer()->getPosition()->getWorld()->getDisplayName(), Values::PVP_WORLDS, true)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is in a PvP world!");
            return;
        }
        $user2->getPlayer()->heal(new EntityRegainHealthEvent($user2->getPlayer(), $user2->getPlayer()->getMaxHealth() - $user2->getPlayer()->getHealth(), EntityRegainHealthEvent::CAUSE_CUSTOM));
        $user->sendMessage($user2->getPlayer(), TextFormat::YELLOW . "You have been healed by " . $sender->getName() . "!");
        $user->removeMoney(Data::$commandHealCost);
        $this->sendMessage($sender, TextFormat::YELLOW . "You have healed " . $user2->getName());
    }
}