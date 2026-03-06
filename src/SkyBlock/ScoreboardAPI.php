<?php

namespace SkyBlock;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\player\Player;

class ScoreboardAPI {

    /** @var Player $player */
    private Player $player;
    /** @var bool */
    private bool $set = false;
    /** @var array */
    private array $lines = [];

    ////////// INIT CODE //////////

    /**
     * ScoreboardAPI constructor.
     *
     * @param Player $player
     */
    public function __construct(Player $player) {
        $this->player = $player;
    }

    ////////// API CODE //////////

    /**
     * @param string $title
     */
    public function new(string $title = '') : void {
        $pk = new SetDisplayObjectivePacket();
        $pk->displaySlot = 'sidebar';
        $pk->objectiveName = $this->player->getName();
        $pk->displayName = $title;
        $pk->criteriaName = 'dummy';
        $pk->sortOrder = 1;
        $this->player->getNetworkSession()->sendDataPacket($pk);
        $this->set = true;
    }

    public function remove() : void {
        $objectiveName = $this->player->getName();
        $pk = new RemoveObjectivePacket();
        $pk->objectiveName = $objectiveName;
        $this->player->getNetworkSession()->sendDataPacket($pk);
        $this->set = false;
    }

    /**
     * @return bool
     */
    public function hasScoreboard() : bool {
        return $this->set;
    }

    /**
     * @param int $score
     *
     * @return string|null
     */
    public function getLine(int $score) : ?string {
        return $this->lines[$score] ?? null;
    }

    /**
     * @param array $lines
     */
    public function setLines(array $lines) : void {
        foreach ($lines as $index => $line) {
            $this->setLine($index, $line);
        }
    }

    public function setLine(int $score, string $message) : void {
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $this->player->getName();
        $entry->type = $entry::TYPE_FAKE_PLAYER;
        $entry->customName = $message;
        $entry->score = $score;
        $entry->scoreboardId = $score;
        if (isset($this->lines[$score])) {
            $pk = new SetScorePacket();
            $pk->type = $pk::TYPE_REMOVE;
            $pk->entries[] = $entry;
            $this->player->getNetworkSession()->sendDataPacket($pk);
        }
        $pk = new SetScorePacket();
        $pk->type = $pk::TYPE_CHANGE;
        $pk->entries[] = $entry;
        $this->player->getNetworkSession()->sendDataPacket($pk);
        $this->lines[$score] = $message;
    }

    public function resetLines() : void {
        foreach ($this->lines as $score => $message) {
            if (isset($this->lines[$score])) {
                $pk = new SetScorePacket();
                $pk->type = $pk::TYPE_REMOVE;
                $pk->entries[] = new ScorePacketEntry();
                $this->player->getNetworkSession()->sendDataPacket($pk);
            }
        }
        $this->lines = [];
    }

}
