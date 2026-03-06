<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class Chatsize extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, "chatsize", "Toggle the chat size for Island Team Chat", ['cs']);
    }

    /**
     * @param Player $sender
     * @param User   $user
     * @param array  $args
     */
    public function execute(Player $sender, User $user, array $args) : void {
        if (isset($args[1])) {
            $this->sendMessage($sender, "§6Usage: /is cs");
            return;
        }
        if (isset($this->pl->ischatsize[$sender->getName()])) {
            unset($this->pl->ischatsize[$sender->getName()]);
            $this->sendMessage($sender, "§eBigger Island TeamChat!");
        } else {
            $this->pl->ischatsize[$sender->getName()] = true;
            $this->sendMessage($sender, "§eSmaller Island TeamChat!");
        }
    }

}