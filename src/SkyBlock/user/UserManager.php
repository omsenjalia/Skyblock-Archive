<?php

namespace SkyBlock\user;

use pocketmine\player\Player;
use SkyBlock\Main;

class UserManager {

    /** @var Main */
    private Main $pl;

    /** @var User[] */
    private array $users = [];

    /**
     * UserManager constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->pl = $plugin;
    }

    /**
     * @param Player $player
     *
     * @return string|null
     */
    public function checkPlayer(Player $player) : ?string {
        $db = $this->pl->getDb();
        $name = $player->getName();
        $pets = $tags = [];
        $island = null;
        if ($db->isPlayerRegistered($name)) {
            $goals = $this->pl->getGoalManager()->explodeGoal($db->getPlayerGoals($name) ?? "");
            $kits = $db->getPlayerKits($name);
            $values = $db->getPlayerValues($name);
            $mobcoin = $values["mobcoin"];
            $mana = $values["mana"];
            if ($values["tags"] != "")
                $tags = explode(",", $values["tags"]);
            $block = $values["blocks"];
            $wm = $values["wm"];
            $homes = (array) json_decode($values["homes"], true);
            $pref = $values["pref"] ?? "";
            $quests = $values["quests"] ?? "";
            $extradata = $values["extradata"] ?? "";
            $money = $values["money"];
            $xp = $values["xp"];
            $xpbank = $values["xpbank"];
            $kills = $values["kills"];
            $deaths = $values["deaths"];
            $seltag = $values["seltag"];
            $island = $this->getPlayerIsland($name);
            $islands = $this->getPlayerIslands($name);
            foreach ($islands as $i) {
                if (isset($this->pl->war[1])) {
                    if (in_array(strtolower($i), $this->pl->war[1], true)) {
                        $this->pl->warplayers[strtolower($player->getName())] = strtolower($i);
                        $enemy = $this->pl->war[1]["island1"] == strtolower($i) ? $this->pl->war[1]["island2"] : $this->pl->war[1]["island1"];
                        $player->sendMessage("§l§f[§a!§f] §r§a$i §eisland is at war with §a$enemy §eisland! Do /is wartp and kill for war points! Island with the most war points will win! War time - 5 mins!");
                    }
                }
            }
            $streak = $values["killstreak"];
            $chips = $values["chips"];
            $won = $values["won"];
            $bounty = $values["bounty"];
            if (!$db->hasPets($name)) {
                $pets = [];
                $pet = "";
                $petname = "Name";
            } else {
                $data = $db->getPetsData($name);
                $petstr = $data["unlocked"];
                $pet = $data["current"];
                $petname = $data["name"];
                if ($petstr != "")
                    $pets = explode(",", $petstr);
            }
            if (!$db->hasTimings($name)) $seconds = 0;
            else $seconds = $db->getTimings($name);
            $base = $db->getPlayerBase($name);
            $gang = $db->getPlayerGang($name) ?? "";
            $id = $db->getPlayerId($player->getName());
            $player->sendMessage("§aWelcome back, §ePlayer §7#§f$id");
            $this->users[strtolower($name)] = new User($player->getName(), $island ?? "", $money, $mobcoin, $xp, $xpbank, $mana, $block, $kills, $deaths, $streak, $chips, $won, $bounty, $islands, $base, $seconds, $gang, $goals, $kits, $pets, $pet, $petname, $seltag, $tags, $wm, $homes, $pref, $extradata, $quests);
        } else {
            $flag2 = false;
            $pets = [];
            $pet = "";
            $petname = "Name";
            if (!$db->hasPets($name)) $flag2 = true;
            else {
                $data = $db->getPetsData($name);
                $petstr = $data["unlocked"];
                $pet = $data["current"];
                $petname = $data["name"];
                if ($petstr != "")
                    $pets = explode(",", $petstr);
            }
            $base = ["combat" => ["level" => 1, "exp" => 0], "gambling" => ["level" => 1, "exp" => 0], "mining" => ["level" => 1, "exp" => 0], "farming" => ["level" => 1, "exp" => 0]];
            $kits = ['achilles' => 0, 'theo' => 0, 'cosmo' => 0, 'arcadia' => 0, 'artemis' => 0, 'calisto' => 0];
            $this->users[strtolower($name)] = new User($player->getName(), "", 3000, 0, 0, 0, 50, 0, 0, 0, 0, 50, 0, 0, [], $base, 0, "", [], $kits, $pets, $pet, $petname, -1, [], "true", [], "", "", "");
            $db->newUser($name, $flag2);
            $id = $db->getPlayerId($player->getName());
            $this->pl->getLogger()->info("§aAccount of player §e$name §anot found. §eCreating a new account #$id...");
            $player->sendMessage("§fWelcome to §bFallenTech §e{$this->pl->server}! §fYou are §aPlayer §7#§f$id");
        }
        return $island;
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function getPlayerIsland(string $name) : ?string {
        $name = strtolower($name);
        if (isset($this->pl->userIslandCache[$name])) {
            $islandName = $this->pl->userIslandCache[$name];
            if (($island = $this->pl->getIslandManager()->getOnlineIsland($islandName)) !== null) {
                if ($island->isAnOwner($name)) {
                    return $islandName;
                } else return null;
            }
        }
        return $this->pl->getDb()->getPlayerIsland($name);
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getPlayerIslands(string $name) : array {
        $name = strtolower($name);
        if (isset($this->pl->userHelperCache[$name])) {
            $islandNames = $this->pl->userHelperCache[$name];
            $islands = [];
            foreach ($islandNames as $islandName) {
                if (($island = $this->pl->getIslandManager()->getOnlineIsland($islandName)) !== null) {
                    if ($island->isMember($name)) {
                        $islands[] = $islandName;
                    }
                } else {
                    $data = $this->pl->getDb()->getIslandInfoData($islandName);
                    if ($data === null) continue;
                    if (($helperstr = $data['helpers']) !== "") {
                        $helpers = explode(",", strtolower($helperstr));
                        if (in_array($name, $helpers, true)) {
                            $islands[] = $islandName;
                        }
                    }
                }
            }
            return $islands;
        }
        return $this->pl->getDb()->getPlayerIslands($name);
    }

    public function update() : void {
        foreach ($this->users as $user) {
            $user->update();
        }
    }

    /**
     * @param Player $player
     *
     * @return string|null
     */
    public function unloadByPlayer(Player $player) : ?string {
        $name = $player->getName();
        $return = null;
        if (($user = $this->getOnlineUser($name)) !== null) {
            $user->setLastAttacker(null);
            if ($user->isIslandSet()) $return = $user->getIsland();
            $user->update();
            $this->setUserOffline($name);
        }
        return $return;
    }

    /**
     * Return an online user by username
     *
     * @param string $username
     *
     * @return User|null
     */
    public function getOnlineUser(string $username) : ?User {
        return $this->users[strtolower($username)] ?? null;
    }

    /**
     * Set a user offline by username
     *
     * @param string $username
     */
    public function setUserOffline(string $username) : void {
        unset($this->users[strtolower($username)]);
    }

}