<?php

namespace SkyBlock\gang;

use pocketmine\player\Player;
use SkyBlock\Main;

class GangManager {

    /** @var Main */
    private Main $plugin;

    /** @var Gang[] */
    private array $gangs = [];

    /**
     * GangManager constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param Player $leader
     * @param string $gang
     */
    public function createGang(Player $leader, string $gang) : void {
        $lower = strtolower($gang);
        if ($this->getOnlineGang($lower) === null) {
            $this->gangs[$lower] = new Gang($this->plugin, $gang, strtolower($leader->getName()), [strtolower($leader->getName())], [strtolower($leader->getName()) => 0], [strtolower($leader->getName()) => 0], "", 1, 0);
            $this->gangs[$lower]->addOnline(strtolower($leader->getName()));
        }
        $this->plugin->getDb()->newGang($gang, strtolower($leader->getName()));
    }

    /**
     * @return array
     */
    public function getOnlineGangs() : array {
        return $this->gangs;
    }

    /**
     * @param Player $player
     */
    public function checkPlayerGang(Player $player) : void {
        $db = $this->plugin->getDb();
        $name = $player->getName();
        $lower = strtolower($player->getName());
        if (($gang = $db->getPlayerGang($name)) !== null) {
            if (($gangclass = $this->getOnlineGang($gang)) === null) {
                $leader = $db->getGangLeader($gang);
                $members = $db->getGangMembers($gang);  // include leader
                $data = $db->getGangData($gang);
                $motd = $data["motd"];
                $level = $data["level"];
                $points = $data["points"];
                $kills = $db->getGangMemberKills($gang);
                $deaths = $db->getGangMemberDeaths($gang);
                $this->gangs[strtolower($gang)] = new Gang($this->plugin, $gang, $leader, $members, $kills, $deaths, $motd, $level, $points);
                $this->gangs[strtolower($gang)]->addOnline($lower);
            } else {
                $gangclass->addOnline($lower);
            }
        }
    }

    /**
     * @param string $gang
     *
     * @return Gang|null
     */
    public function getOnlineGang(string $gang) : ?Gang {
        return $this->gangs[strtolower($gang)] ?? null;
    }

    public function update() : void {
        foreach ($this->gangs as $gang) {
            $gang->update();
        }
    }

    /**
     * @param Player $player
     */
    public function unloadByPlayer(Player $player) : void {
        $chandler = $this->plugin->getGangChatHandler();
        if (($user = $this->plugin->getUserManager()->getOnlineUser($player->getName())) !== null) {
            if ($user->hasGang()) {
                $name = $user->getGang();
                if (($gang = $this->getOnlineGang($name)) !== null) {
                    $gang->removeOnline(strtolower($player->getName()));
                    if (empty($gang->getOnline())) {
                        $gang->update();
                        $this->setGangOffline($name);
                        $chandler->setChatOffline($name);
                    } else {
                        $this->plugin->getDb()->updateUserGang($player->getName(), $name, $gang->getMemberKills($player->getName()), $gang->getMemberDeaths($player->getName()), $gang->isLeader($player->getName()));
                    }
                }
            } else {
                $this->plugin->getDb()->deleteGangMember($player->getName());
                $chandler->removePlayerFromChat($player);
            }
        }
    }

    /**
     * @param string $gang
     */
    public function setGangOffline(string $gang) : void {
        $gang = strtolower($gang);
        if (isset($this->gangs[$gang])) {
            unset($this->gangs[$gang]);
        }
    }
}