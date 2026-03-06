<?php


namespace SkyBlock\command\teleport;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\Values;

class TPA extends BaseCommand {
    /**
     * TPA constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'tpa', 'Send a teleport request to a player');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /tpa <player>");
            return;
        }
        $playerName = strtolower($args[0]);
        $player = Server::getInstance()->getPlayerByPrefix($playerName);
        if (!$player instanceof Player) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
            return;
        }
        if (strtolower($sender->getName()) === strtolower($player->getName())) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot send a teleport request to yourself!");
            return;
        }
        if (isset(Main::getInstance()->teleport[strtolower($player->getName())])) {
            foreach (Main::getInstance()->teleport[strtolower($player->getName())] as $requester => $data) {
                if ($requester !== strtolower($sender->getName())) {
                    unset(Main::getInstance()->teleport[strtolower($player->getName())][$requester]);
                }
            }
        }
        $island = Main::getInstance()->getIslandManager()->getOnlineIslandByWorld($player->getWorld()->getDisplayName());
        if ($island !== null) {
            if ($island->isLocked() && !$island->isMember($sender->getName())) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is on a locked island you are not a member of!");
                return;
            }
            if ($island->isBanned($sender->getName())) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is on an island where you are banned!");
                return;
            }
        }
        if ($sender->getWorld()->getDisplayName() === Values::PVP_WORLD) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot teleport to someone whilst in the PvP world!");
            return;
        }
        if ($player->getWorld()->getDisplayName() === Values::PVP_WORLD) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot teleport to someone in the PvP world!");
            return;
        }
        if (isset(Main::getInstance()->teleport[strtolower($player->getName())][strtolower($sender->getName())])) {
            $time = Main::getInstance()->teleport[strtolower($player->getName())][strtolower($sender->getName())]["time"];
            $currentTime = time();
            if ($currentTime - $time < 60) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You have already have a pending request sent to that player!"); // todo add seconds till
                return;
            } else {
                unset(Main::getInstance()->teleport[strtolower($player->getName())][strtolower($sender->getName())]);
            }
        }
        Main::getInstance()->teleport[strtolower($player->getName())][strtolower($sender->getName())]["time"] = time();
        Main::getInstance()->teleport[strtolower($player->getName())][strtolower($sender->getName())]["type"] = "tpa";
        $this->sendMessage($sender, TextFormat::YELLOW . "You have sent a teleport request to " . $player->getName() . " that will expire in one minute!");
        $this->sendMessage($player, TextFormat::YELLOW . $sender->getName() . " wants to teleport to you. Please use /tpaccept or /tpdeny or wait for the request to expire in one minute!");
    }
}