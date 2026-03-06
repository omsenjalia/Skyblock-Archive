<?php

namespace SkyBlock\chat;

use pocketmine\player\Player;

class GangChat {

    /** @var string */
    private string $gang;

    /** @var Player[] */
    private array $members = [];

    /**
     * GangChat constructor.
     *
     * @param string $gang
     */
    public function __construct(string $gang) {
        $this->gang = $gang;
    }

    /**
     * Return chat gang
     * @return string
     */
    public function getGang() : string {
        return $this->gang;
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