<?php


namespace SkyBlock;


use pocketmine\player\Player;

class Goal {
    /** @var Main */
    public Main $pl;
    /** @var array */
    public array $goals;
    /** @var int */
    public int $maxlevel;
    /** @var string */
    const GOAL_PREFIX = "§l§a[§eGoals§l§a]§r§e> §f";

    public function __construct(Main $plugin) {
        $this->pl = $plugin;
        $this->setGoals();
    }

    public function sendMessage(Player $player, string $messge) {
        $player->sendMessage(self::GOAL_PREFIX . $messge);
    }

    public function setGoals() {
        $this->goals = [
            1  => ['content' => 'Break 10 Dirt/Grass Blocks', 'count' => 10, 'mana' => 25, 'level' => 1],
            2  => ['content' => 'Craft a Crafting Table', 'count' => 1, 'mana' => 25, 'level' => 1],
            3  => ['content' => 'Chop some Wood', 'count' => 3, 'mana' => 20, 'level' => 1],
            4  => ['content' => 'Plant a sapling', 'count' => 1, 'mana' => 10, 'level' => 1],
            5  => ['content' => 'Plant Beetroot seeds', 'count' => 2, 'mana' => 15, 'level' => 1],
            6  => ['content' => 'Make a Cobble Gen and mine cobble!', 'count' => 20, 'mana' => 50, 'level' => 1],
            7  => ['content' => 'Craft a Furnace', 'count' => 1, 'mana' => 20, 'level' => 1],
            8  => ['content' => 'Craft a Stone Pickaxe', 'count' => 1, 'mana' => 15, 'level' => 1],
            9  => ['content' => 'Plant SugarCane', 'count' => 2, 'mana' => 20, 'level' => 1],
            10 => ['content' => 'Buy and Place Coal OreGen from /manashop', 'count' => 1, 'mana' => 20, 'level' => 1],
            11 => ['content' => 'Mine some Coal', 'count' => 50, 'mana' => 25, 'level' => 2],
            12 => ['content' => 'Sell coal in your hand by /sell', 'count' => 1, 'mana' => 20, 'level' => 2],
            13 => ['content' => 'Farm the beetroots!', 'count' => 2, 'mana' => 20, 'level' => 2],
            14 => ['content' => 'Claim Starter Kit from /kit', 'count' => 1, 'mana' => 25, 'level' => 2],
            15 => ['content' => 'Claim PvP Kit from /kit', 'count' => 1, 'mana' => 20, 'level' => 2],
            16 => ['content' => 'Get a Kill, warp to pvp zones using /warp', 'count' => 1, 'mana' => 20, 'level' => 2],
            17 => ['content' => 'Vote using /vote to unlock /kit voter and claim it', 'count' => 1, 'mana' => 50, 'level' => 2],
            18 => ['content' => 'Open a Vote crate at /warp crates', 'count' => 1, 'mana' => 30, 'level' => 2],
            19 => ['content' => 'Sell an item for XP using /sellxp', 'count' => 1, 'mana' => 30, 'level' => 2],
            20 => ['content' => 'Eat 3 Apples', 'count' => 3, 'mana' => 35, 'level' => 2], // level 2 end
            21 => ['content' => 'Buy and Place Iron OreGen from /manashop', 'count' => 1, 'mana' => 100, 'level' => 3],
            22 => ['content' => 'Buy a Common Custom Enchant Book from /ceshop', 'count' => 1, 'mana' => 100, 'level' => 3],
            23 => ['content' => 'Combine a CE Book to a tool using /combiner', 'count' => 1, 'mana' => 100, 'level' => 3],
            24 => ['content' => 'Expand your island to expand radius and block limits.', 'count' => 1, 'mana' => 100, 'level' => 3],
            25 => ['content' => 'Equip a Tag using /tag.', 'count' => 1, 'mana' => 80, 'level' => 3],
            26 => ['content' => 'Get and place an AutoMiner from /manashop.', 'count' => 1, 'mana' => 100, 'level' => 3],
            27 => ['content' => 'Upgrade an OreGen using /upgrade.', 'count' => 1, 'mana' => 100, 'level' => 3],
            28 => ['content' => 'Buy a Vanilla Enchant orb from /enchants.', 'count' => 1, 'mana' => 150, 'level' => 3],
            29 => ['content' => 'Join a DUEL Queue using /match [It will clear your inventory so save it].', 'count' => 1, 'mana' => 100, 'level' => 3],
            30 => ['content' => 'Win Flip-a-Coin Casino at /warp casino once.', 'count' => 1, 'mana' => 150, 'level' => 3],
            31 => ['content' => 'Find 2 Envoys at /warp warzone.', 'count' => 2, 'mana' => 200, 'level' => 4],
            32 => ['content' => 'Open Legendary Crate at /warp crates.', 'count' => 1, 'mana' => 200, 'level' => 4],
            33 => ['content' => 'Sell something on Auction House using /ah', 'count' => 1, 'mana' => 200, 'level' => 4],
            34 => ['content' => 'Get Vaulted CE Book from a GODLY Relic', 'count' => 1, 'mana' => 700, 'level' => 4],
            35 => ['content' => 'Place a spawner', 'count' => 1, 'mana' => 500, 'level' => 4],
            36 => ['content' => 'Buy an enchant orb from /enchants and combine it with tool using /ench', 'count' => 1, 'mana' => 500, 'level' => 4],
            37 => ['content' => 'Buy a Fixer scroll from /manashop and fix an item', 'count' => 1, 'mana' => 300, 'level' => 4],
            38 => ['content' => 'Max a Vanilla enchant on your tool using /inferno, get Inferno Scroll from /manashop', 'count' => 1, 'mana' => 500, 'level' => 4],
            39 => ['content' => 'Remove a Vanilla enchant from your tool using /carver, get Carver Scroll from /manashop', 'count' => 1, 'mana' => 300, 'level' => 4],
            40 => ['content' => 'Open a GKit, Get GKits access from Relics or Mystic crate or buying from Fallentech store!', 'count' => 1, 'mana' => 500, 'level' => 4],
        ];
        $this->maxlevel = $this->getMaxLevel();
    }

    public function getMaxLevel() {
        $level = 1;
        foreach ($this->goals as $data) {
            if ($data['level'] > $level) $level = $data['level'];
        }
        return $level;
    }

    public function getGoalsByLevel(int $level = 1) : array {
        $goals = [];
        foreach ($this->goals as $id => $data) {
            if ($data['level'] == $level) $goals[$id] = $data;
        }
        return $goals;
    }

    public function isLevelCompleted(array $goals, int $level) : bool {
        $count = 0;
        $completed = 0;
        foreach ($this->goals as $id => $data) {
            if ($data['level'] == $level) {
                ++$count;
                if (!isset($goals[$id])) continue;
                if (!$this->isCompleted($id, $goals[$id])) continue;
                ++$completed;
            }
        }
        if ($completed == $count) return true;
        else return false;
    }

    public function getUserGoalLevelData(Player $player, int $level = 1) : array {
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        $goals = $user->getGoals();
        $completed = 0;
        $count = 0;
        foreach ($this->goals as $id => $data) {
            if ($data['level'] == $level) {
                ++$count;
                if (!isset($goals[$id])) continue;
                if (!$this->isCompleted($id, $goals[$id])) continue;
                ++$completed;
            }
        }
        if ($completed == $count) $done = true;
        else    $done = false;
        return [$completed, $count, $done];
    }

    public function getTotalGoalsCount() : int {
        return count($this->goals);
    }

    public function isGoalCompleted(Player $player, int $goal) : bool {
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        $goals = $user->getGoals();
        if (!isset($goals[$goal])) return false;
        else {
            if (!$this->isCompleted($goal, $goals[$goal])) return false;
            else return true;
        }
    }

    public function getGoalData(int $id) {
        return $this->goals[$id] ?? null;
    }

    public function getOfflineCompleteGoalsCount(string $name) : int {
        $goalarr = $this->explodeGoal($this->pl->getDb()->getPlayerGoals($name) ?? "");
        $total = 0;
        foreach ($goalarr as $goal => $count) {
            if ($this->isCompleted($goal, $count)) ++$total;
        }
        return $total;
    }

    public function explodeGoal(string $goalstr) : array {
        $parts = explode(",", $goalstr); // 0=>1,1=>2 ... goal id => count
        $goals = [];
        foreach ($parts as $dat) {
            if ($dat != "") {
                $part = explode("=>", $dat);
                $goals[$part[0]] = $part[1];
            }
        }
        return $goals;
    }

    public function isCompleted(int $goal, int $count) : bool {
        if ($count >= $this->goals[$goal]["count"]) return true;
        return false;
    }

    public function getCompleteGoalsCount(Player $player) : int {
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        $goals = $user->getGoals();
        $total = 0;
        foreach ($goals as $goal => $count) {
            if ($this->isCompleted($goal, $count)) ++$total;
        }
        return $total;
    }

    public function progress(Player $player, int $goal) {
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        $goals = $user->getGoals();
        if (!isset($goals[$goal])) return 0;
        return $goals[$goal];
    }

    public function isLevelUnlocked(array $goals, int $level) : bool {
        if ($level == 1) return true;
        if ($this->isLevelCompleted($goals, --$level)) return true;
        else return false;
    }

    public function add(Player $player, int $goal, int $count = 1) : bool {
        return false;
        /**This is here to disable the goals from being completed*/
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        $goals = $user->getGoals();
        $glevel = $this->goals[$goal]['level'];
        if ($this->isLevelUnlocked($goals, $glevel)) {
            if (!isset($goals[$goal])) $goals[$goal] = $count;
            else {
                if ($goals[$goal] < $this->goals[$goal]["count"]) $goals[$goal] += $count;
            }
            if ($this->goals[$goal]["count"] == $goals[$goal]) {
                $user->addMana($this->goals[$goal]["mana"]);
                $this->sendMessage($player, "You have completed and claimed §6{$this->goals[$goal]['mana']} mana §ffor Goal no. §e$goal!\n§bUse /mymana to see your mana");
                ++$goals[$goal];
                if ($this->isLevelCompleted($goals, $glevel)) {
                    $user->addMana($rmana = $glevel * 500);
                    $this->sendMessage($player, "§eYou have successfully completed Goal Level §f$glevel! §6Reward Mana - §f" . $rmana);
                }
                $user->setGoals($goals);
                return true;
            } else {
                $user->setGoals($goals);
                return false;
            }
        }
        return false;
    }

    public function remove(Player $player, int $goal, int $count = 1) {
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        $goals = $user->getGoals();
        if (isset($goals[$goal])) {
            if ($this->goals[$goal]["count"] > $goals[$goal]) {
                if ($goals[$goal] > 0) $goals[$goal] -= $count;
                else $goals[$goal] = 0;
            }
            $user->setGoals($goals);
        }
    }

    public function implodeGoal(array $goals) : string {
        $goalstr = "";
        if (!empty($goals)) {
            foreach ($goals as $key => $count) {
                $goalstr .= $key . "=>" . $count . ",";
            }
            $goalstr = substr($goalstr, 0, -1);
        }
        return $goalstr;
    }

}