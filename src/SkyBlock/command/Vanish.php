<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\util\Values;

class Vanish extends BaseCommand {
    /**
     * Vanish constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'vanish');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!Main::getInstance()->staffapi->hasStaffRank($sender->getName())) {
            if (!$sender->hasPermission("core.vanish")) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy SkyZEUS rank on " . TextFormat::AQUA . "shop.fallentech.io");
                return;
            }
        }
        if (in_array($sender->getPosition()->getWorld()->getDisplayName(), Values::PVP_WORLDS, true)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot use that command here!");
            return;
        }
        $opposite = !$sender->isInvisible();
        $sender->setInvisible($opposite);
        $this->sendMessage($sender, TextFormat::YELLOW . "You are " . ($opposite ? "now invisible!" : "no longer invisible!"));
    }
}