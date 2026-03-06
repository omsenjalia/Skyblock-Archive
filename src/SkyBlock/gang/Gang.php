<?php

namespace SkyBlock\gang;

use pocketmine\player\Player;
use SkyBlock\db\RecordDB;
use SkyBlock\Main;

class Gang {
    /** @var Main */
    private Main $pl;
    /** @var string */
    private string $name;
    /** @var string */
    private string $motd;
    /** @var string */
    private string $leader;
    private int $level;
    private int $points;
    private array $online = [];
    /** @var array */
    private array $members;
    private array $kills;
    private array $deaths;

    public function __construct(Main $plugin, $name, $leader, $members, $kills, $deaths, $motd, $level, $points) {
        $this->pl = $plugin;
        $this->name = $name;
        $this->leader = $leader;
        $this->members = $members;
        $this->kills = $kills;
        $this->deaths = $deaths;
        $this->motd = $motd;
        $this->level = $level;
        $this->points = $points;
    }

    public function getPlugin() : Main {
        return $this->pl;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getLeader() : string {
        return $this->leader;
    }

    public function setLeader(string $leader) : void {
        $this->leader = strtolower($leader);
    }

    public function getLevel() : int {
        return $this->level;
    }

    public function setLevel(int $level = 1) : void {
        $this->level = $level;
    }

    public function getPoints() : int {
        return $this->points;
    }

    public function getPointsNeeded() : int {
        return $this->level * 150;
    }

    public function hardsetPoints(int $points) : void {
        $this->points = $points;
    }

    public function setPoints(int $points) : void {
        if ($points > 0) {
            while (abs($points) > 0) {
                if (($total = $this->points + $points) > ($needed = $this->level * 150)) {
                    $left = $total - $needed;
                    $this->points = 0;
                    $points = $left;
                    $new = $this->level + 1;
                    foreach ($this->online as $p) {
                        $user = $this->pl->getUserManager()->getOnlineUser($p);
                        $user->getPlayer()->sendTitle("§6Gang Level Up!", "§e{$this->level} §a-> §e$new");
                    }
                    $this->level = $new;
                    RecordDB::record(RecordDB::TOP_GANG, $this->getName(), $this->getLevel(), "level", ["leader" => $this->leader, "mem_count" => $this->getMembersCount()]);
                } else {
                    $this->points = $this->points + $points;
                    $points = 0;
                }
            }
        } else {
            $points = abs($points);
            if (($this->points - $points) < 0) {
                $this->points = 0;
            } else {
                $this->points = $this->points - $points;
            }
        }
    }

    public function getGangChatFormat(Player $player) : string {
        if ($this->isLeader($player->getName()))
            return "§r§o§d{$this->getName()} §f<Leader> §e{$player->getName()} §a-> "; else
            return "§r§o§d{$this->getName()} §f<Member> §e{$player->getName()} §a-> ";
    }

    public function sendGangChatMessage(Player $player, string $msg) : void {
        $format = $this->getGangChatFormat($player) . $msg;
        if (count($online = $this->pl->filterOnline($this->getMembers())) < 2) {
            $player->sendMessage("§cNo one except you is online in that gang!");
        } else {
            foreach ($online as $member) {
                $tchat = $format;
                if (isset($this->pl->gchatsize[$member->getName()])) {
                    $tchat = "§d§l➼" . $tchat;
                }
                $member->sendMessage($tchat);
            }
        }
    }

    public function getLeaderLowerCase() : string {
        return strtolower($this->leader);
    }

    public function getOnline() : array {
        return $this->online;
    }

    public function getOnlineString() : string {
        return implode(", ", $this->online);
    }

    public function getOnlineCount() : int {
        return count($this->online);
    }

    public function addOnline(string $online) : void {
        $this->online[] = strtolower($online);
    }

    public function removeOnline(string $offline) : void {
        $key = array_search(strtolower($offline), $this->online, true);
        unset($this->online[$key]);
    }

    public function getMotd() : string {
        return $this->motd . "§r";
    }

    public function setMotd(string $motd) : void {
        $this->motd = $motd;
    }

    public function getMembers() : array {
        return $this->members;
    }

    public function setMemberKill(string $member, int $kill = 0) : void {
        $this->kills[strtolower($member)] = $kill;
    }

    public function setMemberDeath(string $member, int $death = 0) : void {
        $this->deaths[strtolower($member)] = $death;
    }

    public function addMemberKill(string $member, int $kill = 1) : void {
        $this->kills[strtolower($member)] = $this->kills[strtolower($member)] + $kill;
    }

    public function addMemberDeath(string $member, int $death = 1) : void {
        $this->deaths[strtolower($member)] = $this->deaths[strtolower($member)] + $death;
    }

    public function removeMemberKill(string $member) : void {
        if (isset($this->kills[strtolower($member)]))
            unset($this->kills[strtolower($member)]);
    }

    public function removeMemberDeath(string $member) : void {
        if (isset($this->deaths[strtolower($member)]))
            unset($this->deaths[strtolower($member)]);
    }

    public function getMemberKills(string $member) {
        return $this->kills[strtolower($member)];
    }

    public function getMemberDeaths(string $member) {
        return $this->deaths[strtolower($member)];
    }

    public function getTotalKills() {
        $total = 0;
        foreach ($this->kills as $kills) {
            $total = $total + $kills;
        }
        return $total;
    }

    public function getTotalDeaths() {
        $total = 0;
        foreach ($this->deaths as $deaths) {
            $total = $total + $deaths;
        }
        return $total;
    }

    public function getKills() : array {
        return $this->kills;
    }

    public function getDeaths() : array {
        return $this->deaths;
    }

    public function getMemberString() : string {
        return implode(", ", $this->members);
    }

    public function isGangFull() : bool {
        if ($this->getMembersCount() >= $this->getMembersLimit())
            return true; else return false;
    }

    public function getMembersCount() : int {
        return count($this->members);
    }

    public function getMembersLimit() : int {
        $mem = 3;
        $rem = (int) ($this->level / 5);
        $mem = $mem + $rem;
        if ($mem > 50)
            $mem = 50;
        return $mem;
    }

    public function isMember(string $player) : bool {
        if (in_array(strtolower($player), $this->members, true))
            return true; else return false;
    }

    public function isLeader(string $player) : bool {
        if (strtolower($player) == strtolower($this->leader))
            return true; else return false;
    }

    public function isAMember(string $player) : bool {
        if ((in_array(strtolower($player), $this->members, true)) && (strtolower($player) !== strtolower($this->leader)))
            return true; else return false;
    }

    public function addMember(string $member) : void {
        $this->members[] = strtolower($member);
    }

    public function removeMember(string $member) : void {
        $key = array_search(strtolower($member), $this->members, true);
        unset($this->members[$key]);
    }

    public function update() : void {
        $this->pl->getDb()->updateGang($this->name, $this->leader, $this->members, $this->kills, $this->deaths, $this->motd, $this->level, $this->points);
    }
}