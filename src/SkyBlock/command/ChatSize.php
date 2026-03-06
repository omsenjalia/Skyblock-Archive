<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\Main;

/**
 * @deprecated
 * */
class ChatSize extends BaseCommand {
    /**
     * ChatSize constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'chatsize', "Toggle your chat size for global chat", "", true, ['cs']);
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (isset($args[0]) or !$sender instanceof Player) {
            $this->sendUsage($sender, $commandLabel);
            return;
        }
        if (!$this->pl->staffapi->hasStaffRank($sender->getName())) {
            if (!$sender->hasPermission('core.chatsize')) {
                $this->sendMessage($sender, "§c> You dont have permission to use this command! §aBuy SkyHULK Rank from shop.fallentech.io now!");
                return;
            }
        }
        if (isset($this->pl->chatsize[strtolower($sender->getName())])) {
            unset($this->pl->chatsize[strtolower($sender->getName())]);
            $this->sendMessage($sender, "§eSmaller chats!");
            return;
        }
        $this->pl->chatsize[strtolower($sender->getName())] = strtolower($sender->getName());
        $this->sendMessage($sender, "§eBigger chats!");
    }
}