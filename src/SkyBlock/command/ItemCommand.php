<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class ItemCommand extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'item', 'Get any item', "", true, [], "core.item.give");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . "Usage: /item <name>");
            return;
        }
        $itemName = $args[0];
        $item = LegacyStringToItemParser::getInstance()->parse($itemName) ?? StringToItemParser::getInstance()->parse($itemName);
        if ($item === null || $item->getTypeId() === 0) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is an invalid item name!");
            return;
        }
        $count = $item->getMaxStackSize();
        if (isset($args[1])) {
            if (!is_int((int) $args[1]) || $args[0] < 1) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is an invalid amount!");
                return;
            }
            $count = (int) $args[1];
        }
        $item->setCount($count);
        $sender->getInventory()->addItem($item);
        $this->sendMessage($sender, TextFormat::YELLOW . "You have gave yourself " . $item->getCount() . " " . $item->getName() . "!");
    }
}