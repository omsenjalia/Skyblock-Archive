<?php


namespace SkyBlock\command\sell;


use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class SH extends BaseCommand {
    /**
     * SH constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'sh', 'Sell held item', '', true, ['sellhand', 'sell']);
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if ($sender->isCreative()) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot sell items in creative mode!");
            return;
        }
        $item = $sender->getInventory()->getItemInHand();
        if ($item->getTypeId() === VanillaItems::AIR()) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You are not holding anything!");
            return;
        }
        if ($item->hasCustomName()) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot sell that item!");
            return;
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
        $sold = [];
        $data = Main::getInstance()->getEvFunctions()->sellItem($user, $item);
        if ($data !== null) {
            $key = $item->getTypeId() . ":" . $item->getStateId();
            $sold[$key] = ["count" => $item->getName(), "per" => $data[0], "boost" => $data[1]];
            $sold[$key]["name"] = Main::getInstance()->getEvFunctions()->getSellItemName($item);
            $sender->getInventory()->removeItem($item);
            $this->sendMessage($sender, Main::getInstance()->getEvFunctions()->getSellString($sold, "money"));
        } else {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot sell that item!");
        }
    }
}