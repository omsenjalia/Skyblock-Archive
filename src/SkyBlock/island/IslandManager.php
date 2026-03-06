<?php

namespace SkyBlock\island;

use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class IslandManager {

    /** @var Main */
    private Main $plugin;

    /** @var Island[] */
    private array $islands = [];

    /** @var array */
    private array $pointer = []; // strtolower island name => world name

    /**
     * IslandManager constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * Return a unique island id
     * @return string
     */
    public static function genIslandId() : string {
        return "a" . floor(microtime(true)) . "-" . rand(1, 9999);
    }

    /**
     * @param Player $owner
     * @param User   $user
     * @param string $island
     *
     * @return Island
     */
    public function createIsland(Player $owner, User $user, string $island) : Island {
        $id = $this->genIslandId();
        $user->setIsland($island);
        if ($this->getOnlineIsland($island) === null) {
            $roles = ["farmers" => "", "miners" => "", "placers" => "", "builders" => "", "labourers" => "", "butchers" => ""];
            $this->islands[$id] = new Island($id, $island, "", strtolower($owner->getName()), [], [], [], [], "", 0, 0, 0, 0, 0, 0, 0, "false", 0, 0, 1, 10, "", [], 0, 0, "", $roles, ["coal" => 1, "copper" => 0, "iron" => 0, "lapis" => 0, "gold" => 0, "diamond" => 0, "emerald" => 0, "quartz" => 0, "netherite" => 0, "deep_coal" => 0, "deep_copper" => 0, "deep_iron" => 0, "deep_lapis" => 0, "deep_gold" => 0, "deep_diamond" => 0, "deep_emerald" => 0, "deep_quartz" => 0, "deep_netherite" => 0], ["cobblestone" => 19, "coal" => 1, "copper" => 0, "iron" => 0, "lapis" => 0, "gold" => 0, "diamond" => 0, "emerald" => 0, "quartz" => 0, "netherite" => 0, "deep_coal" => 0, "deep_copper" => 0, "deep_iron" => 0, "deep_lapis" => 0, "deep_gold" => 0, "deep_diamond" => 0, "deep_emerald" => 0, "deep_quartz" => 0, "deep_netherite" => 0], "");
            $this->pointer[strtolower($island)] = $id;
        }
        $this->plugin->getDb()->newIsland($island, $id, strtolower($owner->getName()));
        return $this->islands[$id];
    }

    /**
     * @return array
     */
    public function getOnlineIslands() : array {
        return $this->islands;
    }

    /**
     * @param Player      $player
     * @param string|null $id
     */
    public function checkPlayerIsland(Player $player, ?string $id) : void {
        if ($id === null) return;
        if (($island = $this->getOnlineIsland($id)) === null) {
            $db = $this->plugin->getDb();
            $coowners = $helper = $admin = $bansarr = [];
            $owner = $db->getIslandOwner($id);
            $ldata = $db->getIslandLevelData($id);
            $data = $db->getIslandInfoData($id);
            $data2 = $db->getIslandInfo2Data($id);
            $data4 = $db->getIslandInfo4Data($id);
            $data8 = $db->getIslandInfo8Data($id);
            $data8pref = $db->getIslandInfo8DataPref($id);
            $world = $db->getWorldName($id);
            $locked = $db->getIslandLocked($id);
            $money = $db->getIslandMoney($id);
            $motd = $db->getIslandMotd($id);
            $radius = $db->getIslandRadius($id);
            $home = $db->getHomeArray($id);
            $points = $ldata["points"];
            $level = $ldata["level"];
            $creator = $data2['creator'];
            $bans = $data2['bans'];
            $mining = $data2['mining'];
            $farming = $data2['farming'];
            $perms = $data['perms'];
            $spawner = $data['spawner'];
            $oregen = $data['oregen'];
            $islandData = $data["extradata"] ?? "";
            $autominer = $data['autominer'];
            $autoseller = $data['autoseller'];
            $hopper = $data['hopper'];
            $farm = $data['farm'];
            $vlimit = $data['vlimit'];
            $helpers = $data["helpers"];
            $admins = $data["admins"];
            $coownerstr = $data["coowners"];
            $receiver = $data["receiver"];
            if ($bans != "") $bansarr = explode(",", strtolower($bans));
            if ($helpers != "") $helper = explode(",", strtolower($helpers));
            if ($admins != "") $admin = explode(",", strtolower($admins));
            if ($coownerstr != "") $coowners = explode(",", strtolower($coownerstr));
            $server = $this->plugin->getServer();
            if (!$server->getWorldManager()->isWorldLoaded($world)) $server->getWorldManager()->loadWorld($world, true);
            $island = $this->islands[$world] = new Island($world, $id, $creator, $owner, $home, $helper, $admin, $coowners, $receiver, $spawner, $oregen, $autominer, $autoseller, $hopper, $farm, $vlimit, $locked, $money, $points, $level, $radius, $motd, $bansarr, $mining, $farming, $perms, $data4, $data8, $data8pref, $islandData);
            $this->pointer[strtolower($id)] = $world;
        }
        if ($island->hasMotd())
            $player->sendTitle("§a§l§oIS MOTD", "§r§e" . $island->getMotd());
    }

    public function update() : void {
        foreach ($this->islands as $island) {
            $island->update();
        }
    }

    /**
     * @param string $dir
     *
     * @return bool
     */
    public function deleteAllLevels(string $dir) : bool {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..' || $item == 'lobby' || $item == 'PvP') {
                continue;
            }

            if (!$this->deleteLevel($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }
        return rmdir($dir);
    }

    /**
     * @param string $dir
     *
     * @return bool
     */
    public function deleteLevel(string $dir) : bool {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteLevel($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }
        return rmdir($dir);
    }

    /**
     * @param string $world
     */
    public function removeIsland(string $world) : void {
        $server = $this->plugin->getServer();
        if ($server->getWorldManager()->isWorldLoaded($world)) {
            if (!is_null($level = $server->getWorldManager()->getWorldByName($world))) {
                foreach ($level->getEntities() as $entity) {
                    if (!$entity instanceof Player) $entity->close();
                }
                $path = $level->getProvider()->getPath();
                $server->getWorldManager()->unloadWorld($level);
                $this->deleteLevel($path);
            }
        }
    }

    /**
     * @param Player      $player
     * @param string|null $islandName
     */
    public function unloadByPlayer(Player $player, ?string $islandName) : void {
        $this->plugin->getChatHandler()->removePlayerFromChat($player);
        if ($islandName !== null) {
            if (($island = $this->getOnlineIsland($islandName)) === null) return;
            if ($island->getOnlineOwnerCount() <= 1) {
                $island->update();
                $id = $this->pointer[strtolower($islandName)] ?? null;
                $level = null;
                if ($id !== null) {
                    if (!is_null($level = $this->plugin->getServer()->getWorldManager()->getWorldByName($id))) {
                        foreach ($level->getEntities() as $entity) {
                            if (!$entity instanceof Player) $entity->close();
                        }
                    }
                    $this->plugin->getChatHandler()->setChatOffline($id);
                }
                $this->setIslandOffline($islandName);
                if (!is_null($level)) {
                    $this->plugin->getServer()->getWorldManager()->unloadWorld($level);
                }
            }
        }
    }

    /**
     * Return an online island
     *
     * @param string $id
     *
     * @return Island|null
     */
    public function getOnlineIsland(string $id) : ?Island {
        $id = strtolower($id);
        if (isset($this->pointer[$id])) {
            return $this->islands[$this->pointer[$id]] ?? null;
        }
        return null;
    }

    /**
     * Return an online island
     *
     * @param $world
     *
     * @return Island|null
     */
    public function getOnlineIslandByWorld($world) : ?Island {
        return $this->islands[$world] ?? null;
    }

    /**
     * Set an island offline
     *
     * @param string $id
     */
    public function setIslandOffline(string $id) : void {
        $id = strtolower($id);
        if (isset($this->pointer[$id])) {
            $world = $this->pointer[$id];
            unset($this->pointer[$id]);
            if (isset($this->islands[$world])) {
                unset($this->islands[$world]);
            }
        }
    }
}