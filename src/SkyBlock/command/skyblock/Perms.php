<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class Perms extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'permission', 'Change Island helper permissions.', ['permissions', 'perms', 'perm']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (isset($args[1])) {
            $this->sendMessage($sender, "§6Usage: /is perms");
            return;
        }
        if (!$user->hasIsland()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be the Island Owner to use this command!");
            return;
        }
        $this->pl->getFormFunctions()->sendIslandPermsMain($sender);
    }

}