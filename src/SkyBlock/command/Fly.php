<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\util\Values;

class Fly extends BaseCommand {
    /**
     * Fly constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'fly', 'Fly in a world');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset(Main::getInstance()->flycount[$sender->getName()])) {
            if (!Main::getInstance()->staffapi->hasStaffRank($sender->getName())) {
                if (!$sender->hasPermission("core.fly")) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy VIP rank on " . TextFormat::AQUA . "shop.fallentech.io");
                    return;
                }
            }
        } else {
            $timeLeft = Values::FLY_TIME - (time() - Main::getInstance()->flycount[$sender->getName()]);
            if ($timeLeft <= 0) {
                unset(Main::getInstance()->flycount[$sender->getName()]);
                $this->sendMessage($sender, TextFormat::RED . "Your flying time has expired. Vote again tomorrow to fly again!");
                return;
            } else {
                $this->sendMessage($sender, "Time left for /fly: " . $this->getTimeLeft($timeLeft));
            }
        }
        if (in_array($sender->getPosition()->getWorld()->getDisplayName(), Values::SERVER_WORLDS, true)) {
            $this->sendMessage($sender, TextFormat::RED . "You cannot use that command here!");
            return;
        }
        if ($sender->getAllowFlight()) {
            $sender->setAllowFlight(false);
            $sender->setFlying(false);
            $this->sendMessage($sender, TextFormat::YELLOW . "Disabled flight!");
        } else {
            $sender->setAllowFlight(true);
            $sender->setFlying(true);
            $this->sendMessage($sender, TextFormat::YELLOW . "Enabled flight!");
        }
    }

    private function getTimeLeft(int $seconds) : string {
        if ($seconds < 60) {
            return $seconds . " seconds";
        } else {
            $minutes = (int) ($seconds / 60);
            $secs = ($seconds % 60);
            if ($minutes < 60) {
                return $minutes . " minutes, " . $secs . " seconds";
            } else {
                $hours = (int) ($minutes / 60);
                $mins = ($minutes % 60);
                return $hours . " hours, " . $mins . " minutes";
            }
        }

    }
}