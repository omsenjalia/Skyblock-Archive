<?php

namespace SkyBlock\chat;

use pocketmine\player\Player;

class GangChatHandler {

    /** @var GangChat[] */
    private array $chats = [];

    /**
     * @param Player $player
     *
     * @return bool
     */
    public function isInChat(Player $player) : bool {
        foreach ($this->chats as $chat) {
            if (in_array($player, $chat->getMembers(), true))
                return true;
        }
        return false;
    }

    /**
     * @param Player $player
     *
     * @return GangChat|null
     */
    public function getPlayerChat(Player $player) : ?GangChat {
        foreach ($this->chats as $chat) {
            if (in_array($player, $chat->getMembers(), true)) {
                return $chat;
            }
        }
        return null;
    }

    /**
     * @param Player $player
     * @param string $gang
     */
    public function addPlayerToChat(Player $player, string $gang) : void {
        if (!isset($this->chats[$gang])) {
            $this->chats[$gang] = new GangChat($gang);
        }
        $this->chats[$gang]->addMember($player);
    }

    /**
     * @param string $gang
     */
    public function setChatOffline(string $gang) : void {
        if (isset($this->chats[$gang])) unset($this->chats[$gang]);
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