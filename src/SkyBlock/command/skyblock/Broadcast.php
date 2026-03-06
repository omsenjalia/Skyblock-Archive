<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\user\User;

class Broadcast extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'bc', 'Broadcast a message on your island, all players on your island will receive it', ['broadcast', 'bcast']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou don't have an island to broadcast on! Use /is create <island name> to make one");
            return;
        }
        if (!isset($args[1])) {
            $this->sendMessage($sender, "§cUsage: /is bc <msg>");
            return;
        }
        $islandName = $user->getIsland();
        if (($island = $this->im->getOnlineIsland($islandName)) === null) {
            $this->sendMessage($sender, "§4[Error] §cIsland not online!");
            return;
        }
        $level = $island->getWorldLevel();
        if (count($level->getPlayers()) < 2) {
            $this->sendMessage($sender, "§cThere's no one on your Island except you!");
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
        foreach ($level->getPlayers() as $player) {
            $player->sendMessage("§e»>\n" . TextFormat::GRAY . "[Island Broadcast]»> §a" . $sender->getName() . " > " . TextFormat::YELLOW . $msg . "\n§e»>");
        }
    }

}