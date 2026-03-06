<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class ClearInv extends BaseCommand {

    /**
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'clearinventory', 'Clear your inventory', '', true, ['clearinv']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (isset($args[0])) {
            if (!Main::getInstance()->hasOp($sender)) {
                $this->sendMessage($sender, self::NO_PERMISSION);
                return;
            }
            $player = strtolower($args[0]);
            $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
            if ($user === null) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
                return;
            }
            if ($user->getPlayer()->getGamemode() === GameMode::SPECTATOR) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is in spectator mode!");
                return;
            }
            $user->getPlayer()->getInventory()->clearAll();
            $user->getPlayer()->getArmorInventory()->clearAll();
            $user->getPlayer()->getCursorInventory()->clearAll();
            $user->getPlayer()->getCraftingGrid()->clearAll();
            $this->sendMessage($user->getPlayer(), TextFormat::YELLOW . "Your inventory has been cleared by " . $sender->getName());
        } else {
            if ($sender->getGamemode() === GameMode::SPECTATOR) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You are in spectator mode!");
                return;
            }
            $sender->getInventory()->clearAll();
            $sender->getArmorInventory()->clearAll();
            $sender->getCursorInventory()->clearAll();
            $sender->getCraftingGrid()->clearAll();
            $this->sendMessage($sender, TextFormat::YELLOW . "Your inventory has been cleared!");
        }
    }

}