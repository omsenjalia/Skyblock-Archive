<?php

namespace SkyBlock\chat;

use pocketmine\player\Player;

class Chat {

    /** @var string */
    private string $level;

    /** @var Player[] */
    private array $members = [];

    /**
     * Chat constructor.
     *
     * @param string $level
     */
    public function __construct(string $level) {
        $this->level = $level;
    }

    /**
     * Return chat level
     * @return string
     */
    public function getLevel() : string {
        return $this->level;
    }

    /**
     * @return Player[]
     */
    public function getMembers() : array {
        return $this->members;
    }

    /**
     * Add a player to the chat
     *
     * @param Player $player
     */
    public function addMember(Player $player) : void {
        $this->members[] = $player;
    }

    /**
     * Try to remove member
     *
     * @param Player $player
     */
    public function tryRemoveMember(Player $player) : void {
        if (in_array($player, $this->members, true))
            unset($this->members[array_search($player, $this->members, true)]);
    }

}