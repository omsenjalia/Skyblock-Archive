<?php


namespace SkyBlock\command;


use JsonException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Update extends BaseCommand {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'update', 'Enable/Disable update window', '[true/false]', true, ['updates']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!isset($args[0])) {
            if (!$sender instanceof Player) {
                $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /update <true/false>");
                return;
            }
            Main::getInstance()->getFormFunctions()->sendUpdateWindow($sender);
        } else {
            if (!Main::getInstance()->staffapi->isHarderStaff($sender->getName())) {
                $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /update");
                return;
            }
            $type = strtolower($args[0]);
            if ($type === "true") {
                Main::getInstance()->updates["enabled"] = true;
                Main::getInstance()->upd->set("enabled");
                $this->sendMessage($sender, TextFormat::YELLOW . "Update window has been enabled!");
            } elseif ($type == "false") {
                Main::getInstance()->updates["enabled"] = false;
                Main::getInstance()->upd->set("enabled", false);
                $this->sendMessage($sender, TextFormat::YELLOW . "Update window has been disabled!");
            } elseif ($type === "tutorial") {
                Main::getInstance()->fetchTutorial();
                $this->sendMessage($sender, TextFormat::YELLOW . "The tutorial has been refreshed!");
                return;
            } else {
                $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /update <enabled/tutorial>");
            }
            try {
                Main::getInstance()->upd->save();
            } catch (JsonException) {
            }
        }
    }
}