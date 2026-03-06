<?php

namespace SkyBlock\island;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\World;
use SkyBlock\db\RecordDB;
use SkyBlock\Main;
use SkyBlock\perms\Permission;
use SkyBlock\perms\PermissionManager;

class Island {

    /** @var int */
    private int $level;
    /** @var int */
    private int $points;
    /** @var int */
    private int $money, $spawner, $oregen, $autominer, $autoseller, $hopper, $farm, $mining, $farming, $vlimit;
    /** @var int */
    private int $radius;
    /** @var string */
    private string $name, $creator, $receiver;
    /** @var string */
    private string $motd;
    /** @var string */
    private string $owner;
    /** @var int */
    private int $warpoints = 0;
    /** @var array */
    private array $home;
    /** @var array */
    private array $jobs = ["miners", "farmers", "placers", "builders", "labourers", "butchers"];
    /** @var array */
    private array $jobdesc
        = [
            "Player will be able to mine/break all ores",
            "Player will be able to hoe grass, place seeds and harvest crops",
            "Player will be able to place any block that isnt an ore",
            "Player will be able to place/break any block except ores",
            "Player will be able to place/break any block and hoe grass, place seeds, kill mobs and harvest crops",
            "Player will be able to kill mobs at island",
        ];
    /** @var Permission[] */
    private array $perms = [];
    /** @var array */
    private array $helpers, $roles, $admins, $coowners, $bans;
    /** @var array */
    public array $builders = [], $placers = [], $miners = [], $farmers = [], $labourers = [], $butchers = [], $oredata = [], $oredatapref = [];
    /** @var string */
    private string $locked;
    private bool $war = false;
    private bool $freeze = false;
    private string $warisland;
    private string $id;
    private ?World $worldlevel;
    private IslandData $islandData;
    public const RADIUS_MAX = 500;
    public const COOWNER_MAX = 15;
    public const HOME_MAX = 50;
    public const ROLE_MAX = 20;
    public const HELPER_MAX = 30;
    public const SPAWNER_MAX = 10;
    public const OREGEN_MAX = 50000;
    public const AUTO_MINER_MAX = 30;
    public const AUTO_SELLER_MAX = 30;
    public const HOPPER_MAX = 50;
    public const FARM_MAX = 10000;

    public function __construct(string $world, $id, $creator, $owner, $home, $helper, $admin, $coowners, $receiver, $spawner, $oregen, $autominer, $autoseller, $hopper, $farm, $vlimit, $locked, $money, $points, $level, $radius, $motd, $bans, $mining, $farming, $perms, $roles, $oredata, $oredatapref, $islandData) {
        $this->id = $world;
        $this->name = $id;
        $this->creator = $creator;
        $this->owner = $owner;
        $this->home = $home;
        $this->helpers = $helper;
        $this->admins = $admin;
        $this->coowners = $coowners;
        $this->receiver = $receiver;
        $this->spawner = $spawner;
        $this->oregen = $oregen;
        $this->autominer = $autominer;
        $this->autoseller = $autoseller;
        $this->hopper = $hopper;
        $this->farm = $farm;
        $this->vlimit = $vlimit;
        $this->locked = $locked;
        $this->money = $money;
        $this->points = $points;
        $this->level = $level;
        $this->radius = $radius;
        $this->motd = $motd;
        $this->bans = $bans;
        $this->mining = $mining;
        $this->farming = $farming;
        $this->roles = $roles;
        $this->oredata = $oredata;
        $this->islandData = new IslandData($islandData);
        unset($this->oredata["name"]);
        $this->oredatapref = $oredatapref;
        unset($this->oredatapref["name"]);
        $this->initPerms((array) json_decode($perms, true));
        $this->initRoles($roles);
        $this->worldlevel = Server::getInstance()->getWorldManager()->getWorldByName($this->getId());
    }

    public function setVLimit(int $vlimit) : void {
        $this->vlimit = $vlimit;
    }

    public function getVLimit() : int {
        return $this->vlimit;
    }

    /**
     * @param array $permissions
     */
    public function initPerms(array $permissions) : void {
        foreach (PermissionManager::getPermissions() as $name => $permission) {
            $permission = clone $permission;
            if (isset($permissions[$name])) {
                $permission->setHolders($permissions[$name]);
                foreach ($permission->getHolders() as $holder) {
                    if (!$this->isMember($holder)) {
                        $permission->removeHolder($holder);
                    }
                }
            } else {
                if ($permission->isDefault()) $permission->setHolders($this->getHelpers());
            }
            $this->perms[$name] = $permission;
        }
    }

    /**
     * @param string $member
     * @param string $pname
     *
     * @return bool
     */
    public function hasPerm(string $member, string $pname) : bool {
        $member = strtolower($member);
        if ($member === $this->getOwnerLowerCase()) return true;
        return in_array($member, $this->perms[$pname]->getHolders(), true);
    }

    /**
     * @param string $pname
     * @param string $member
     * @param bool   $value
     */
    public function setPerm(string $member, string $pname, bool $value) : void {
        if ($value) $this->perms[$pname]->addHolder(strtolower($member));
        else $this->perms[$pname]->removeHolder(strtolower($member));
        Main::getInstance()->getDb()->setIslandPerms($this->getName(), json_encode($this->perms));
    }

    /**
     * @return array
     */
    public function getAllMembers() : array {
        $return = $this->helpers;
        $return[] = $this->owner;
        return $return;
    }

    /**
     * @return array
     */
    public function getJobs() : array {
        return $this->jobs;
    }

    public function getJobsTrimmed(bool $includeS = false) : string {
        if (!$includeS) {
            $array = array_map(function(string $job) {
                return substr($job, 0, -1);
            }, $this->jobs
            );
        } else $array = $this->jobs;
        return implode(", ", $array);
    }

    public function setFreeze(bool $freeze) {
        $this->freeze = $freeze;
    }

    public function getFreeze() : bool {
        return $this->freeze;
    }

    public function initRoles($roles) {
        foreach ($this->jobs as $job) {
            if ($roles[$job] != "")
                $this->$job = explode(",", strtolower($roles[$job]));
        }
    }

    public function getSpawner() : int {
        return $this->spawner;
    }

    public function getMiningMode() : int {
        return $this->mining;
    }

    public function getFarmingMode() : int {
        return $this->farming;
    }

    public function hasHomes() : bool {
        return !empty($this->home);
    }

    public function getCreator() : string {
        return ($this->creator == "") ? $this->owner : $this->creator;
    }

    public function getCatalyst() : int {
        return $this->oregen;
    }

    public function getAutoMiner() : int {
        return $this->autominer;
    }

    public function getAutoSeller() : int {
        return $this->autoseller;
    }

    public function getHopper() : int {
        return $this->hopper;
    }

    public function getFarm() : int {
        return $this->farm;
    }

    public function getRadiusMax() : int {
        return self::RADIUS_MAX;
    }

    public function addOreGen(int $oregen = 1) {
        $this->oregen += $oregen;
    }

    public function removeOreGen(int $oregen = 1) {
        if ($this->oregen > 0) $this->oregen -= $oregen;
    }

    public function addAutoMiner(int $autominer = 1) {
        $this->autominer += $autominer;
    }

    public function addAutoSeller(int $autoseller = 1) {
        $this->autoseller += $autoseller;
    }


    public function removeAutoMiner(int $autominer = 1) {
        if ($this->autominer > 0) $this->autominer -= $autominer;
    }

    public function removeAutoSeller(int $autoseller = 1) {
        if ($this->autoseller > 0) $this->autoseller -= $autoseller;
    }

    public function addHopper(int $hopper = 1) {
        $this->hopper += $hopper;
    }

    public function removeHopper(int $hopper = 1) {
        if ($this->hopper > 0) $this->hopper -= $hopper;
    }

    public function addFarm(int $farm = 1) {
        $this->farm += $farm;
    }

    public function removeFarm(int $farm = 1) {
        if ($this->farm > 0) $this->farm -= $farm;
    }

    public function addSpawner(int $spawner = 1) {
        $this->spawner += $spawner;
    }

    public function removeSpawner(int $spawner = 1) {
        if ($this->spawner > 0) $this->spawner -= $spawner;
    }

    public function getSpawnerLimit() : int {
        $limit = (int) ($this->radius / 10); // def radius = 10
        if (($max = $this->getSpawnerMax()) < $limit) return $max;
        else    return $limit;
    }

    public function getCatalystLimit() : int {
        //        $limit = (int)($this->radius / 2); // def radius = 10
        //        if (($max = $this->getOreGenMax()) < $limit) return $max;
        //        else    return $limit;
        // ^^^^ old code for oregens replaced with catalyst
        return $this->getCatalystMax();

    }

    public function getAutoMinerLimit() : int {
        $limit = (int) ($this->radius / 5); // def radius = 10
        if (($max = $this->getAutoMinerMax()) < $limit) return $max;
        else    return $limit;
    }

    public function getAutoSellerLimit() : int {
        $limit = (int) ($this->radius / 5); // def radius = 10
        if (($max = $this->getAutoSellerMax()) < $limit) return $max;
        else    return $limit;
    }

    public function getHopperLimit() : int {
        $limit = (int) ($this->radius / 10); // def radius = 10
        if (($max = $this->getHopperMax()) < $limit) return $max;
        else    return $limit;
    }

    public function getFarmLimit() : int {
        $limit = (int) ($this->radius * 20); // def radius = 10, max radius = 500
        if (($max = $this->getFarmMax()) < $limit) return $max;
        else    return $limit;
    }

    public function getHelperLimit() : int {
        $limit = (int) floor($this->level / 10) + 1;
        if (($max = $this->getHelperMax()) < $limit) {
            return $max;
        } else {
            return $limit;
        }
    }

    public function getCoownerLimit() : int {
        $limit = (int) ($this->level / 30);
        if (($max = $this->getCoownerMax()) < $limit) return $max;
        else    return $limit;
    }

    public function getCoownerMax() : int {
        return self::COOWNER_MAX;
    }

    public function getRoleLimit() : int {
        $limit = (int) (($this->level / 10));
        if (($max = $this->getRoleMax()) < $limit) return $max;
        else    return $limit;
    }

    public function getHomesLimit() : int {
        $limit = 3 + (int) ($this->level / 20);
        if (($max = $this->getHomesMax()) < $limit) return $max;
        else    return $limit;
    }

    public function getHomesMax() : int {
        return self::HOME_MAX;
    }

    public function getRoleMax() : int {
        return self::ROLE_MAX;
    }

    public function getHelperMax() : int {
        return self::HELPER_MAX;
    }

    public function getSpawnerMax() : int {
        return self::SPAWNER_MAX;
    }

    public function getCatalystMax() : int {
        return self::OREGEN_MAX;
    }

    public function getAutoMinerMax() : int {
        return self::AUTO_MINER_MAX;
    }

    public function getAutoSellerMax() : int {
        return self::AUTO_SELLER_MAX;
    }

    public function getHopperMax() : int {
        return self::HOPPER_MAX;
    }

    public function getFarmMax() : int {
        return self::FARM_MAX;
    }

    public function setWorldLevel($world) {
        $this->worldlevel = $world;
    }

    public function getSpawnX() : int {
        return $this->worldlevel->getSpawnLocation()->getFloorX();
    }

    public function getSpawnY() : int {
        return $this->worldlevel->getSpawnLocation()->getFloorY();
    }

    public function getSpawnZ() : int {
        return $this->worldlevel->getSpawnLocation()->getFloorZ();
    }

    /**
     * @return World|null
     */
    public function getWorldLevel() : ?World {
        return $this->worldlevel ?? Server::getInstance()->getWorldManager()->getWorldByName($this->getId());
    }

    public function getId() : string {
        return $this->id;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getOwner() : string {
        return $this->owner;
    }

    public function getReceiver() : string {
        return ($mr = $this->receiver) === "" ? $this->owner : $mr;
    }

    public function setOwner(string $owner) {
        $this->removeHelper($owner);
        $this->removeCoowner($owner);
        $this->addCoowner($this->owner);
        $this->addHelper($this->owner);
        $this->owner = strtolower($owner);
    }

    public function isJob(string $job) : bool {
        return in_array(strtolower($job), $this->jobs, true);
    }

    public function removeBan(string $player) {
        if (($key = array_search(strtolower($player), $this->bans, true)) !== false) {
            unset($this->bans[$key]);
        }
    }

    public function getJobDesc(string $job) {
        if (($key = array_search(strtolower($job), $this->jobs, true)) !== false) {
            return $this->jobdesc[$key];
        }
        return "";
    }

    public function removeHelper(string $helper) : void {
        if (($key = array_search(strtolower($helper), $this->helpers, true)) !== false) {
            $this->removeAllPerms($helper);
            unset($this->helpers[$key]);
        }
    }

    public function removeRole(string $worker, string $job) : void {
        if (($key = array_search(strtolower($worker), $this->$job, true)) !== false) {
            unset($this->$job[$key]);
        }
    }

    public function addCoowner(string $coowner) : void {
        $this->coowners[] = strtolower($coowner);
    }

    public function removeCoowner(string $coowner) : void {
        if (($key = array_search(strtolower($coowner), $this->coowners, true)) !== false) {
            unset($this->coowners[$key]);
        }
    }

    public function addAdmin(string $admin) : void {
        $this->admins[] = strtolower($admin);
    }

    public function removeAdmin(string $admin) : void {
        if (($key = array_search(strtolower($admin), $this->admins, true)) !== false) {
            unset($this->admins[$key]);
        }
    }

    public function addBan(string $player) : void {
        $this->bans[] = strtolower($player);
    }

    public function getBanCount() : int {
        return count($this->bans);
    }

    public function addRole(string $player, string $job) : void {
        $this->$job[] = strtolower($player);
    }

    public function addHelper(string $helper) : void {
        $this->setDefaultPerms(strtolower($helper));
        $this->helpers[] = strtolower($helper);
    }

    public function setDefaultPerms(string $member) : void {
        foreach ($this->perms as $perm) {
            if ($perm->isDefault()) $perm->addHolder($member);
            else $perm->removeHolder($member);
        }
    }

    public function removeAllPerms(string $member) : void {
        foreach ($this->perms as $perm) {
            $perm->removeHolder($member);
        }
    }

    public function getOwnerLowerCase() : string {
        return strtolower($this->owner);
    }

    public function getCoowners() : array {
        return $this->coowners;
    }

    public function getMoney() : int {
        return $this->money;
    }

    /**
     * @return IslandData
     */
    public function getIslandData() : IslandData {
        return $this->islandData ?? new IslandData();
    }

    public function setCreator(string $creator) {
        $this->creator = $creator;
    }

    public function getMotd() : string {
        return $this->motd . "§r";
    }

    public function hasMotd() : bool {
        return $this->motd !== "";
    }

    public function setMotd(string $motd) : void {
        $this->motd = $motd;
    }

    public function getWarPoints() : int {
        return $this->warpoints;
    }

    public function setWarPoints(int $warpoints = 0) : void {
        $this->warpoints = $warpoints;
    }

    public function getLocked() : string {
        return $this->locked;
    }

    public function setReceiver(string $receiver) : void {
        $this->receiver = $receiver;
    }

    public function setLocked(string $locked) : void {
        $this->locked = $locked;
    }

    public function isLocked() : bool {
        return $this->locked === "true";
    }

    public function getRadius() : int {
        return $this->radius;
    }

    public function getPointsNeeded() : float|int {
        return $this->level * 150;
    }

    public function getTeamChatFormat(Player $player) : string {
        if ($this->isOwner($player->getName()))
            return "§r§o§d{$this->getName()} §b<Owner> §e{$player->getName()} §a-> ";
        elseif ($this->isCoowner($player->getName()))
            return "§r§o§d{$this->getName()} §c<CoOwner> §e{$player->getName()} §a-> ";
        elseif ($this->isAdmin($player->getName()))
            return "§r§o§d{$this->getName()} §a<Admin> §e{$player->getName()} §a-> ";
        else
            return "§r§o§d{$this->getName()} §7<Helper> §e{$player->getName()} §a-> ";
    }

    public function sendTeamChatMessage(Player $player, string $msg) {
        $format = $this->getTeamChatFormat($player) . $msg;
        if (count($online = $this->getOnline()) < 2) {
            $player->sendMessage("§cNo one except you is online on that Island!");
        } else {
            foreach ($online as $member) {
                $tchat = $format;
                if (isset(Main::getInstance()->ischatsize[$member->getName()])) {
                    if (Main::getInstance()->chatpack) {
                        $tchat = "§f§f§f" . $tchat;
                    } else {
                        $tchat = "§d§l➼" . $tchat;
                    }
                }
                $member->sendMessage($tchat);
            }
        }
    }

    public function getRandomOnlineCoOwner() : ?Player {
        $on_co = Main::getInstance()->filterOnline($this->getCoowners());
        if (empty($on_co)) return null;
        return $on_co[array_rand($on_co)] ?? null;
    }

    public function teleport(Player $player) : void {
        $player->teleport(new Position($this->getSpawnX(), $this->getSpawnY(), $this->getSpawnZ(), $this->getWorldLevel()), 0.0, 0.0);
        if ($this->hasMotd())
            $player->sendTitle("§a§l§oIS MOTD", "§r§e" . $this->getMotd());
    }

    /**
     * @return array
     */
    public function getOnline() : array {
        return Main::getInstance()->filterOnline($this->getAllMembers());
    }

    public function getLockedState() : string {
        return $this->locked === "true" ? "Locked" : "Unlocked";
    }

    public function getHelpers() : array {
        return $this->helpers;
    }

    /**
     * @return int
     */
    public function getHomesCount() : int {
        return count($this->home);
    }

    public function setHome(string $home, Position $pos) {
        $home = strtolower($home);
        $this->home[$home]['x'] = $pos->getX();
        $this->home[$home]['y'] = $pos->getY();
        $this->home[$home]['z'] = $pos->getZ();
    }

    /**
     * @param string $home
     *
     * @return Position
     */
    public function getHomePosition(string $home) : Position {
        $home = strtolower($home);
        return new Position ($this->home[$home]['x'], $this->home[$home]['y'], $this->home[$home]['z'], $this->worldlevel);
    }

    /**
     * @return string
     */
    public function getHomesString() : string {
        $home = array_keys($this->home);
        return implode(", ", $home);
    }

    public function getLevel() : int {
        return $this->level;
    }

    public function setLevel(int $level) {
        $this->level = $level;
    }

    public function getPoints() : int {
        return $this->points;
    }

    public function getCoOwnerCount() : int {
        return count($this->coowners);
    }

    /**
     * @param string $home
     */
    public function removeHome(string $home) : void {
        unset($this->home[strtolower($home)]);
    }

    public function setPoints(int $points) : void {
        if ($points > 0) {
            while (abs($points) > 0) {
                if (($total = $this->points + $points) > ($needed = $this->level * 150)) {
                    $left = $total - $needed;
                    $this->points = 0;
                    $points = $left;
                    $new = $this->level + 1;
                    $world = Server::getInstance()->getWorldManager()->getWorldByName($this->id);
                    $crop = "";
                    foreach (Main::getInstance()->getCrops() as $data) {
                        if ($new === $data['level']) {
                            $crop = "\n§eUnlocked §b{$data['name']} §eCrop!";
                        }
                    }
                    foreach ($world->getPlayers() as $p) {
                        $p->sendTitle("§6Island Level Up!", "§e{$this->level} §a-> §e$new" . $crop);
                    }
                    $this->level = $new;
                    RecordDB::record(RecordDB::TOP_ISLAND, $this->getName(), $this->getLevel(), "level", ["creator" => $this->getCreator(), "owner" => $this->getOwner(), "mem_count" => $this->getHelperCount()]);
                } else {
                    $this->points = $this->points + $points;
                    $points = 0;
                }
            }
        } else {
            while (abs($points) > 0) {
                $points = abs($points);
                if (($this->points - $points) < 0) {
                    if ($this->level === 1) {
                        $this->points = 0;
                        break;
                    }
                    $points = $points - $this->points;
                    $this->level = $this->level - 1;
                    $this->points = $this->level * 150;
                } else {
                    $this->points = $this->points - $points;
                    $points = 0;
                }
            }
        }
    }

    public function getWarIsland() : string {
        return $this->warisland;
    }

    public function setWarIsland(string $warisland) {
        $this->warisland = $warisland;
    }

    public function setMiningMode(int $mining) {
        $this->mining = $mining;
    }

    public function setFarmingMode(int $farming) {
        $this->farming = $farming;
    }

    public function getAdminCount() : int {
        return count($this->admins);
    }

    public function getHelperCount() : int {
        return count($this->helpers);
    }

    public function getRoleCount() : int {
        $sum = 0;
        foreach ($this->jobs as $job) {
            $sum += count($this->$job);
        }
        return $sum;
    }

    public function getBankLimit() : float|int {
        return ($this->level * 25000);
    }

    public function getRoleHelpers() : array {
        $arr = [];
        foreach ($this->jobs as $job) {
            foreach ($this->$job as $val) {
                $arr[] = $val;
            }
        }
        return $arr;
    }

    public function getOnlineWorkers() : array {
        $arr = [];
        foreach ($this->jobs as $job) {
            foreach ($this->$job as $val) {
                if (($user = Main::getInstance()->getUserManager()->getOnlineUser($val)) !== null) $arr[] = $user;
            }
        }
        return $arr;
    }

    public function hasARole(string $player) : bool {
        foreach ($this->jobs as $job) {
            if ($this->hasRole($player, $job)) return true;
        }
        return false;
    }

    public function getRole(string $player) {
        foreach ($this->jobs as $job) {
            if ($this->hasRole($player, $job)) return $job;
        }
        return null;
    }

    public function hasRole(string $player, string $type) : bool {
        return in_array(strtolower($player), $this->$type, true);
    }

    public function isCoowner(string $player) : bool {
        return in_array(strtolower($player), $this->coowners, true);
    }

    public function isAdmin(string $player) : bool {
        return strtolower($player) === strtolower($this->owner) or in_array(strtolower($player), $this->admins, true);
    }

    public function isMember(string $player) : bool {
        return strtolower($player) === strtolower($this->owner) or in_array(strtolower($player), $this->helpers, true);
    }

    public function isOwner(string $player) : bool {
        return strtolower($player) === strtolower($this->owner);
    }

    public function isAnOwner(string $player) : bool {
        return strtolower($player) === strtolower($this->owner) or in_array(strtolower($player), $this->coowners, true);
    }

    public function isBanned(string $player) : bool {
        return in_array(strtolower($player), $this->bans, true);
    }

    public function isHelper(string $player) : bool {
        return in_array(strtolower($player), $this->helpers, true);
    }

    public function isAtWar() : bool {
        return $this->war;
    }

    /**
     * @param string $home
     *
     * @return bool
     */
    public function hasHome(string $home) : bool {
        return isset($this->home[strtolower($home)]);
    }

    public function hasPoints(int $points) : bool {
        $level = $this->level;
        $points2 = $this->points;
        while ($level > 1) {
            $level--;
            $points2 += ($level * 150);
        }
        return $points2 >= $points;
    }

    public function getTotalPoints() : int {
        $level = $this->level;
        $points = $this->points;
        while ($level > 1) {
            $level--;
            $points += ($level * 150);
        }
        return (int) $points;
    }

    public function hasMoney(int $money) : bool {
        return $this->money >= $money;
    }

    public function setAtWar() {
        $this->war = true;
    }

    public function unsetAtWar() {
        $this->war = false;
    }

    public function expandRadius() {
        $this->radius = $this->radius + 10;
    }

    public function addMoney(int $money) : void {
        $this->money += $money;
    }

    public function removeMoney(int $money) : bool {
        if ($this->money >= $money) {
            $this->money -= $money;
            return true;
        } else return false;
    }

    public function addWarPoints(int $points = 1) : void {
        $this->warpoints += $points;
    }

    public function removeWarPoints(int $points) : bool {
        if ($this->warpoints >= $points) {
            $this->warpoints -= $points;
            return true;
        } else return false;
    }

    public function isIslandFullForVisitors() : bool {
        if ($this->getVLimit() === 0) return false;
        $vcount = count(array_filter($this->getWorldLevel()->getPlayers(), function(Player $player) {
            return !$this->isMember($player->getName());
        }
                        )
        );
        return $vcount >= $this->getVLimit();
    }

    public function update() : void {
        $members = $this->getHelperString();
        $admins = $this->getAdminString();
        $coowners = $this->getCoownerString();
        Main::getInstance()->getDb()->updateIsland($this->name, $this->creator, $this->owner, $this->home, $members, $admins, $coowners, $this->receiver, $this->spawner, $this->oregen, $this->autominer, $this->autoseller, $this->hopper, $this->farm, $this->vlimit, $this->locked, $this->money, $this->points, $this->level, $this->radius, $this->motd, $this->getBanString(), $this->mining, $this->farming, json_encode($this->perms), $this->getRolesArray(), $this->getOreDataArray(), $this->getOreDataPrefArray(), json_encode($this->islandData));
    }

    public function getRolesArray() : array {
        foreach ($this->jobs as $job) {
            $this->roles[$job] = strtolower(implode(",", $this->$job));
        }
        return $this->roles;
    }

    public function getPlayerRank(string $name) : string {
        if ($this->isOwner($name)) return 'Owner';
        elseif ($this->isCoowner($name)) return 'CoOwner';
        elseif ($this->isAdmin($name)) return 'Admin';
        elseif ($this->isHelper($name)) return 'Helper';
        else return '';
    }

    public function getOnlineOwnerCount() : int {
        $count = 0;
        foreach ($this->coowners as $coown) {
            if (Server::getInstance()->getPlayerExact($coown) instanceof Player) ++$count;
        }
        if (Server::getInstance()->getPlayerExact($this->owner) instanceof Player) ++$count;
        return $count;
    }

    public function getOnlyHelperString() : string {
        $string = "";
        foreach ($this->helpers as $helper) {
            if (!$this->isAdmin($helper) and !$this->isCoowner($helper)) $string .= $helper . ",";
        }
        return substr($string, 0, -1);
    }

    public function getBanString() : string {
        return implode(",", $this->bans);
    }

    public function getCoownerString() : string {
        return implode(",", $this->coowners);
    }

    public function getAdminString() : string {
        return implode(",", $this->admins);
    }

    public function getHelperString() : string {
        return implode(",", $this->helpers);
    }

    public function getOreDataArray() : array {
        return $this->oredata;
    }

    public function getOreDataPrefArray() : array {
        return $this->oredatapref;
    }

    public function getOreUpgradeCount($data) {
        if (isset($this->oredata[$data])) {
            return $this->oredata[$data];
        }
        return null;
    }

    public function updateOreData(string $key, int $value) {
        if ($value > 20) {
            return false;
        }
        $this->oredata[$key] = $value;
        return true;
    }

    public function updateAddOreData(string $key) {
        if ($this->oredata[$key] < 20) {
            $this->oredata[$key] = $this->oredata[$key] + 1;
            return true;
        }
        return false;
    }

    public function updateOreDataPref(array $pref) {
        foreach ($pref as $key => $value) {
            $value = intval($value);
            if (array_key_exists($key, $this->oredatapref)) {
                if (array_key_exists($key, $this->oredata)) {
                    if ($value > $this->oredata[$key]) {
                        $value = $this->oredata[$key];
                    }
                }
                $this->oredatapref[$key] = $value;
            }
        }
        /**make sure only 20 points can be applied!*/
        $data = array_reverse($this->oredatapref);
        $counter = 0;
        foreach ($data as $key => $value) {
            $value = intval($value);
            if ($value > 20) {
                $value = 20;
            }
            if ($value < 0) {
                $value = 0;
            }

            if ($counter < 20) {
                if ($counter + $value <= 20) {
                    $this->oredatapref[$key] = $value;
                    $counter += $value;
                } else {
                    $this->oredatapref[$key] = (20 - $counter);
                    $counter = 20;
                }
            } else {
                $this->oredatapref[$key] = 0;
            }
        }
        if ($counter < 20) {
            $this->oredatapref["cobblestone"] = 20 - $counter;
        }
    }
}
