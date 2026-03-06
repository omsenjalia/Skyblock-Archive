<?php


namespace SkyBlock\command\teleport;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class TPDeny extends BaseCommand {
    /**
     * TPDeny constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'tpdeny', 'Reject a teleport request');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!isset(Main::getInstance()->teleport[strtolower($sender->getName())])) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You dont have any teleport requests!");
            return;
        }
        $requester = implode("", array_keys(Main::getInstance()->teleport[strtolower($sender->getName())]));
        $user = Main::getInstance()->getUserManager()->getOnlineUser($requester);
        if ($user === null) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " The requester is no longer online. The request has been removed!");
            unset(Main::getInstance()->teleport[strtolower($sender->getName())]);
            return;
        }
        $time = Main::getInstance()->teleport[strtolower($sender->getName())][$requester]["time"];
        unset(Main::getInstance()->teleport[strtolower($sender->getName())]);
        if (time() - $time >= 60) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " The last request you received has expired. The request has been removed!");
            return;
        }
        $this->sendMessage($user->getPlayer(), $sender->getName() . " has denied your teleport request!");
        $this->sendMessage($sender, TextFormat::YELLOW . "The teleport request has been denied!");
    }
}