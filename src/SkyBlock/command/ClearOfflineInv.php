<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class ClearOfflineInv extends BaseCommand {

    /**
     * ClearOfflineInv constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'clearofflineinv', 'Clear inv from player data', '[player]', true, ['ciop'], "core.ciop");
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
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /clearofflineinv <player>");
            return;
        }
        $player = strtolower($args[0]);
        if (file_exists(Server::getInstance()->getDataPath() . "players/" . $player . ".dat")) {
            $namedTag = Server::getInstance()->getOfflinePlayerData($player);
            $namedTag->setTag("Inventory", new ListTag([], NBT::TAG_Compound));
            Server::getInstance()->saveOfflinePlayerData($player, $namedTag);
            $this->sendMessage($sender, TextFormat::YELLOW . $player . "'s inventory has been cleared!");
        }
    }
}