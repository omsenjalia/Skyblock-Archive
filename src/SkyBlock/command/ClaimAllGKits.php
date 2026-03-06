<?php

namespace SkyBlock\command;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\util\Values;

class ClaimAllGKits extends BaseCommand {
    /**
     * ClaimAllKits constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'claimallgkits', 'Claim all gkits at once', '<type>', true, ['claimallgkit']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender->hasPermission("core.claimallgkit")) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy /claimallgkits on " . TextFormat::AQUA . "shop.fallentech.io");
            return;
        }
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if ($sender->getPosition()->getWorld()->getDisplayName() === Values::PVP_WORLD) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't claim kits here!");
            return;
        }
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /claimallgkits <type>");
            return;
        }
        $type = $args[0];
        $gkitTypes = ["achilles", "theo", "cosmo", "arcadia", "artemis", "calisto"];
        if (!in_array($type, $gkitTypes)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " " . ucfirst($type) . " is not a valid gkit. Available gkits are: achilles, theo, cosmo, arcadia, artemis, and calisto!");
            return;
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
        $count = $user->getKitCount($type);
        $amount = 0;
        for ($i = 1; $i <= $count; $i++) {
            if ($sender->getInventory()->firstEmpty() === -1) {
                break;
            }
            if ($user->hasKit($type)) {
                $user->removeKitCount($type);
                Main::getInstance()->getFunctions()->giveKitChest($sender, $type);
                $amount++;
            }
        }
        $this->sendMessage($sender, "You claimed " . $amount . " " . ucfirst($type) . " GKits!");
    }
}