<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class AutoMiner extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'autominer', 'Toggle AutoMiners activity on Island', ['am']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (isset($args[1])) {
            $this->sendMessage($sender, "§6Usage: /is am");
            return;
        }
        if (!$user->hasIsland()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be Island Owner to use that command.");
            return;
        }
        $islandName = $user->getIsland();
        if (($island = $this->im->getOnlineIsland($islandName)) === null) {
            $this->sendMessage($sender, "§4[Error]§c Island not online");
            return;
        }
        if (isset($this->pl->autominer[$island->getId()])) {
            unset($this->pl->autominer[$island->getId()]);
            $this->sendMessage($sender, "§eAutoMiners on Island §6`{$island->getName()}` §aturning on...");
        } else {
            $this->pl->autominer[$island->getId()] = true;
            $this->sendMessage($sender, "§eAutoMiners on Island §6`{$island->getName()}` §cturning off...");
        }
    }

}