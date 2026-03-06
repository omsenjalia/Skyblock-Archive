<?php


namespace SkyBlock\command\skyblock;


use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\Position;
use SkyBlock\Main;
use SkyBlock\user\User;

class SetSpawn extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'setspawn', 'Set your island spawn (wont change the center of the island)');
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (isset($args[1])) {
            $this->sendMessage($sender, "§cUsage: /is setspawn");
            return;
        }
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error]§c You need to be Island Owner or CoOwner to use that command");
            return;
        }
        $islandName = $user->getIsland();
        if (($island = $this->im->getOnlineIsland($islandName)) === null) {
            $this->sendMessage($sender, "§cIsland not online");
            return;
        }
        if (!$island->isCoowner($sender->getName()) && !$island->isOwner($sender->getName())) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be Island CoOwner to use that command.");
            return;
        }
        if ($sender->getPosition()->getWorld()->getDisplayName() != $island->getId()) {
            $this->sendMessage($sender, "§4[Error]§c You need to be on your island to run this command!");
            return;
        }
        $v = new Vector3(3, $sender->getPosition()->getY(), 2);
        if ($sender->getPosition()->distance($v) > $island->getRadius()) {
            $this->sendMessage($sender, "§4[Error]§c Cant set spawn outside of the radius");
            return;
        }
        $level = $sender->getWorld();
        $pos = $sender->getPosition();
        $level->setSpawnLocation(new Position((double) $pos->x, (double) $pos->y, (double) $pos->z, $level));
        $this->sendMessage($sender, "§eIsland's spawn point changed successfully!");
    }

}