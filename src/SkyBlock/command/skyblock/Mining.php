<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\perms\Permission;
use SkyBlock\user\User;

class Mining extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'mining', 'Switch to Mining mode of island', ['miningmode', 'mm']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be the Island Owner or CoOwner to use that command!");
            return;
        }
        $islandName = $user->getIsland();
        if (($island = $this->im->getOnlineIsland($islandName)) === null) {
            $this->sendMessage($sender, "§4[Error]§c Island not online");
            return;
        }
        if (!$island->isOwner($sender->getName())) {
            if (!$island->hasPerm($sender->getName(), Permission::CUSTOM_BLOCKS)) {
                $this->sendMessage($sender, "§4[Error]§c You dont have custom block perms on this island");
                return;
            }
        }
        $state = $island->getMiningMode();
        $nextstate = ($state === 0) ? 1 : 0;
        $island->setMiningMode($nextstate);
        $msg = ($nextstate == 0) ? "§cdeactivated" : "§aactivated";
        $this->sendMessage($sender, "§eIsland Mining Mode $msg!");
        if ($nextstate == 1) {
            $sender->sendMessage("§f> You wont be able to mine custom blocks(oregens etc.), use this command again to deactivate!");
            $msg = "§eactivated";
        } else $msg = "§cdeactivated";
        if (strtolower($sender->getName()) != strtolower($island->getOwner())) {
            if (($player = $this->pl->getServer()->getPlayerExact($island->getOwner())) instanceof Player) {
                $this->sendMessage($player, "§a{$sender->getName()} $msg Island Mining Mode on the Island.");
            }
        }
    }

}