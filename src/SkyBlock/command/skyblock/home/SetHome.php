<?php


namespace SkyBlock\command\skyblock\home;


use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

class SetHome extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'sethome', 'Set an island home');
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!isset($args[1]) or isset($args[2])) {
            $this->sendMessage($sender, "§cUsage: /is sethome <home name>");
            return;
        }
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be Island owner/coowner to use that command.");
        } else {
            $islandName = $user->getIsland();
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $this->sendMessage($sender, "§4[Error] §cIsland not online!");
                return;
            }
            $level = $island->getId();
            if ($sender->getPosition()->getWorld()->getDisplayName() != $level) {
                $this->sendMessage($sender, "§4[Error] §cYou must be on your island §e$islandName §cto set home!");
                return;
            }
            if (!(ctype_alnum($args[1]))) {
                $this->sendMessage($sender, "§4[Error] §cHome names can only include letters or numbers");
                return;
            }
            if (strlen($args[1]) <= 2) {
                $this->sendMessage($sender, "§4[Error] §cName needs to be longer than 2 characters!");
                return;
            }
            if (strlen($args[1]) > 8) {
                $this->sendMessage($sender, "§4[Error] §cName needs to be smaller than 8 characters!");
                return;
            }
            if (!$island->hasHome($args[1]) and ($island->getHomesCount() >= $island->getHomesLimit())) {
                $this->sendMessage($sender, "§4[Error]§c You can only have {$island->getHomesLimit()} homes at your Island level! Try deleting homes by /is delhome or updating existing homes by /is sethome <old home>!");
                return;
            }
            $status = ($island->hasHome($args[1])) ? "updated" : "created";
            $this->sendMessage($sender, "§eIsland Home §a{$args[1]} §e$status");
            $island->setHome($args[1], $sender->getPosition());
        }
    }

}