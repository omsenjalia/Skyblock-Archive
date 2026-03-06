<?php


namespace SkyBlock\command;


use pocketmine\block\BlockTypeIds;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Number extends BaseCommand {

    /**
     * Number constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, "number", "Shows held item details", "", true, ['id', "itemid"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        $inventory = $sender->getInventory();
        $heldItem = $inventory->getItemInHand();
        if ($heldItem->getTypeId() === BlockTypeIds::AIR) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You are holding nothing!");
            return;
        }
        $loreString = implode(", ", $heldItem->getLore());
        $cleanName = TextFormat::clean($heldItem->getCustomName());
        $this->sendMessage($sender, TextFormat::YELLOW . "Your player number is #" . Main::getInstance()->getDb()->getPlayerId($sender->getName()));
        $this->sendMessage($sender, TextFormat::YELLOW . "You are holding " . $heldItem->getVanillaName() . "\n" .
                                  "ID: minecraft:" . str_replace(" ", "_", $heldItem->getVanillaName()) . "\n" .
                                  "Count: " . $heldItem->getCount() .
                                  "Custom Name: " . $cleanName .
                                  "Extra: " . $loreString
        );
    }
}