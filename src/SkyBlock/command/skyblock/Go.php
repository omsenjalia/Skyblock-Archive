<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class Go extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'go', "Teleport you to your island", ['join']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou don't have an island! Use /is create <island name> to make one");
        } else {
            $islandName = $user->getIsland();
            if (($island = $this->im->getOnlineIsland($islandName)) !== null) {
                if (!is_null($island->getWorldLevel())) {
                    $island->teleport($sender);
                    $this->sendMessage($sender, "§aYou were teleported to your island §e$islandName §aspawn successfully!\n§fIncrease island points by placing Placing ore blocks, mining ores and farming.");
                } else {
                    $this->sendMessage($sender, "§cError! World doesnt exist! Try /is delete");
                }
            } else {
                $this->sendMessage($sender, "§4[Error] §cIsland not online!");
            }
        }
    }

}