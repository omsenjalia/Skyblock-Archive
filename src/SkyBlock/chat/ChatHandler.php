<?php

namespace SkyBlock\chat;

use pocketmine\player\Player;

class ChatHandler {

    /** @var Chat[] */
    private array $chats = [];

    /**
     * @param Player $player
     *
     * @return bool
     */
    public function isInChat(Player $player) : bool {
        foreach ($this->chats as $chat) {
            if (in_array($player, $chat->getMembers(), true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Player $player
     *
     * @return Chat|null
     */
    public function getPlayerChat(Player $player) : ?Chat {
        foreach ($this->chats as $chat) {
            if (in_array($player, $chat->getMembers(), true)) {
                return $chat;
            }
        }
        return null;
    }

    /**
     * @param Player $player
     * @param string $level
     */
    public function addPlayerToChat(Player $player, string $level) : void {
        if (!isset($this->chats[$level])) {
            $this->chats[$level] = new Chat($level);
        }
        $this->chats[$level]->addMember($player);
    }

    /**
     * @param string $level
     */
    public function setChatOffline(string $level) : void {
        if (isset($this->chats[$level])) unset($this->chats[$level]);
    }

    /**
     * @param Player $player
     */
    public function removePlayerFromChat(Player $player) : void {
        foreach ($this->chats as $chat) {
            if (in_array($player, $chat->getMembers(), true)) {
                $chat->tryRemoveMember($player);
            }
        }
    }

}