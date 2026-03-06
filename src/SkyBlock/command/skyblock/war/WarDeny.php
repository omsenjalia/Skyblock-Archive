<?php


namespace SkyBlock\command\skyblock\war;


use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

/** @deprecated */
class WarDeny extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'wardeny');
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (isset($args[1])) {
            $this->sendMessage($sender, "§6Usage: /is wardeny");
            return;
        }
        if (!$user->hasIsland()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be an island owner to go at war with an island!");
            return;
        }
        if (!isset($this->pl->warreq[strtolower($sender->getName())])) {
            $this->sendMessage($sender, "§4[Error] §cYou haven't recieved any war requests!");
            return;
        }
        $requester = $this->pl->warreq[strtolower($sender->getName())]["requester"];
        if (($user2 = $this->um->getOnlineUser($requester)) === null) {
            unset($this->pl->warreq[strtolower($sender->getName())]);
            $this->sendMessage($sender, "§4[Error] §cRequester went offline!");
            return;
        }
        $time = $this->pl->warreq[strtolower($sender->getName())]["time"];
        $now = time();
        unset($this->pl->warreq[strtolower($sender->getName())]);
        if (($now - $time) > 60) {
            $this->sendMessage($sender, "§4[Error] §cRequest timed out!");
            return;
        }
        $this->sendMessage($sender, "§eWar request successfully declined!");
        $this->sendMessage($user2->getPlayer(), "§a{$sender->getName()} §edeclined your war request!");
    }

}