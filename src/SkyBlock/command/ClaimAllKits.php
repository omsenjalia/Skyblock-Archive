<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\util\Values;

class ClaimAllKits extends BaseCommand {
    /**
     * ClaimAllKits constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'claimallkits', 'Claim all kits at once', '', true, ['claimallkit']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender->hasPermission("core.claimallkit")) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy /claimallkits on " . TextFormat::AQUA . "shop.fallentech.io");
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
        $validKits = [];
        foreach (Main::getInstance()->kits as $name => $data) {
            if ($sender->hasPermission("core.kit." . strtolower($name)) && $data->getCoolDownLeft($sender) === null) {
                $validKits[$name] = $data;
            }
        }
        if (count($validKits) === 0) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " No kits found to claim. They might be on cooldown!"); // todo grammar
            return;
        }
        if (Main::getInstance()->getFunctions()->isInventoryFull($sender, count($validKits))) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You need to have " . count($validKits) . " empty slots in your inventory to claim all your kits!"); // todo fill until empty instead
            return;
        }
        foreach ($validKits as $kit) {
            $kit->addTo($sender, false);
        }
        $this->sendMessage($sender, TextFormat::YELLOW . "Successfully claimed all kits!");

    }
}