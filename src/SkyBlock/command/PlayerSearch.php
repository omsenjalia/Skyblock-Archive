<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class PlayerSearch extends BaseCommand {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, "playersearch", "Search player username via id", "<id | name> [player id or username to search]", true, ["ps", "psearch"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!isset($args[1])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /playersearch <id | name> [id or username]");
            return;
        }
        $type = strtolower($args[0]);
        if ($type !== "id" && $type !== "username" && $type !== "name") {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Please enter a correct search type. 'id' and 'name' are the two choices!");
            return;
        }
        if ($type === "id") {
            if (!is_int((int) $args[1])) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is an invalid player id!");
                return;
            }
            $id = (int) $args[1];
            $name = Main::getInstance()->getDb()->getPlayerFromId($id);
            if (!$name !== null) {
                $this->sendMessage($sender, TextFormat::YELLOW . "The name of $id is $name!");
            } else {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player id was not found!");
            }
        } else {
            $name = strtolower($args[1]);
            if (!Main::getInstance()->getDb()->isNameUsed($name)) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player was not found!");
                return;
            }
            $id = Main::getInstance()->getDb()->getPlayerId($name);
            $this->sendMessage($sender, TextFormat::YELLOW . "The id of $name is $id!");
        }
    }
}