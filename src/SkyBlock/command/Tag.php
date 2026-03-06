<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\util\Values;

class Tag extends BaseCommand {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, "tag", "Tags Help", "help", true, ['title', 'tags', 'titles']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0])) {
            if ($sender->getPosition()->getWorld()->getDisplayName() === Values::PVP_WORLD) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot use that command here!");
                return;
            }
            Main::getInstance()->getFormFunctions()->sendTagMainMenu($sender);
        } else {
            if (!Main::getInstance()->hasOp($sender)) {
                $this->sendMessage($sender, self::NO_PERMISSION);
                return;
            }
            switch (strtolower($args[0])) {
                case "set":
                    if (!isset($args[2])) {
                        $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /tag set <player> <tag>");
                        return;
                    }
                    $playerName = strtolower($args[1]);
                    $player = Server::getInstance()->getPlayerByPrefix($playerName);
                    if (!$player instanceof Player) {
                        $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
                        return;
                    }
                    $name = $player->getName();
                    $user = Main::getInstance()->getUserManager()->getOnlineUser($name);
                    $tag = strtolower($args[2]);
                    if ($tag === "none") {
                        $user->setSelTag();
                        $this->sendMessage($sender, TextFormat::YELLOW . $name . "'s tag has been removed!");
                        $this->sendMessage($player, TextFormat::YELLOW . "Your tag has been removed!");
                        break;
                    }
                    $id = Main::getInstance()->getTagManager()->getTagId($tag);
                    if ($id !== -1) {
                        $user->addTag($id);
                        $user->setSelTag($id);
                        $this->sendMessage($sender, TextFormat::YELLOW . "$name's tag has been set to $tag!");
                        $this->sendMessage($player, TextFormat::YELLOW . "Your tag has been set to $tag!");
                    } else {
                        $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That tag was not found!");
                    }
                    break;
                default:
                    break;
            }
        }
    }
}