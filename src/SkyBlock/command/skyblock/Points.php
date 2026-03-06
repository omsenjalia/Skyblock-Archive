<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class Points extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'points', "Check your overall points");
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!isset($args[1])) {
            if (!$user->isIslandSet()) {
                $this->sendMessage($sender, "§4[Error]§e You do not own any island!");
                return;
            }
            $islandName = $user->getIsland();
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $this->sendMessage($sender, "§4[Error]§c Island not online");
                return;
            }
            $this->sendMessage($sender, "§aIsland total points till now: {$island->getTotalPoints()} Points!");
        }
    }

}