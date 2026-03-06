<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class AutoSeller extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'autoseller', 'Toggle AutoSellers activity on Island', ['as']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (isset($args[1])) {
            $this->sendMessage($sender, "§6Usage: /is as");
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
        if (isset($this->pl->autoseller[$island->getId()])) {
            unset($this->pl->autoseller[$island->getId()]);
            $this->sendMessage($sender, "§eAutoSellers on Island §6`{$island->getName()}` §aturning on...");
        } else {
            $this->pl->autoseller[$island->getId()] = true;
            $this->sendMessage($sender, "§eAutoSellers on Island §6`{$island->getName()}` §cturning off...");
        }
    }

}