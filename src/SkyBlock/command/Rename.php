<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Tool;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Rename extends BaseCommand {

    const MAX_CHARS = 40;

    /**
     * Rename constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'rename', 'Rename your held item/tool', '<new name>');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /rename <name>");
            return;
        }
        $item = $sender->getInventory()->getItemInHand();
        $name = implode(" ", $args);
        if ($item->getCount() !== 1) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can only rename tools and armor!");
            return;
        }
        if (!$item instanceof Armor && !$item instanceof Tool) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can only rename tools and armor!");
            return;
        }
        if (strlen(TextFormat::clean($name)) > self::MAX_CHARS) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " The name must be less than " . self::MAX_CHARS . " characters!");
            return;
        }
        if (strpos($name, "\n")) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That name is invalid!");
            return;
        }
        $sender->getInventory()->setItemInHand(Main::getInstance()->getFunctions()->renameItem($item, $name));
        $this->sendMessage($sender, TextFormat::YELLOW . "Your item was renamed to $name" . TextFormat::RESET . TextFormat::YELLOW . "!");
    }
}