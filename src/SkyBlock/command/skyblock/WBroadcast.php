<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\user\User;

class WBroadcast extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'wbc', 'Broadcast a message to all your online workers wherever they are', ['wbroadcast', 'wbcast']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou don't have an island to broadcast on! Use /is create <island name> to make one");
            return;
        }
        if (!isset($args[1])) {
            $this->sendMessage($sender, "§cUsage: /is wbc <msg>");
            return;
        }
        $level = $sender->getWorld();
        $islandName = $user->getIsland();
        $island = $this->im->getOnlineIsland($islandName);
        if ($level->getDisplayName() !== $island->getId()) {
            $sender->sendMessage("§4[Error] §cYou have to be on your island to broadcast messages!");
            return;
        }
        /** @var User[] $oworkers */
        $oworkers = $island->getOnlineWorkers();
        $oworkers[] = $user;
        if (count($oworkers) < 2) {
            $this->sendMessage($sender, "§cThere's no Worker online to broadcast to!");
            return;
        }
        array_shift($args);
        $msg = implode(" ", $args);
        if (!$this->pl->isStringValid($msg)) {
            $sender->sendMessage("§4[Error] §cMessage not valid!");
            return;
        }
        $msg = TextFormat::clean($msg, true);
        if (str_replace(' ', '', $msg) === "") {
            $sender->sendMessage("§4[Error] §cEmpty Message not valid!");
            return;
        }
        foreach ($oworkers as $user) {
            $user->getPlayer()->sendMessage("§e»>\n" . TextFormat::GRAY . "[Worker Broadcast]»> §a" . $sender->getName() . " > " . TextFormat::YELLOW . $msg . "\n§e»>");
        }
    }

}