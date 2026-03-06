<?php

namespace SkyBlock\db;

use SkyBlock\Main;
use SkyBlock\util\Util;

class SQLite3 {

    /** @var Main */
    private Main $pl;

    /**
     * SQlite3 constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->pl = $plugin;
    }

    public function resetHomes() : void {
        $this->pl->db->prepare("UPDATE player SET homes = '';")->execute();
    }

    /**
     * @param string $old
     * @param string $new
     */
    public function renameIsland(string $old, string $new) : void {
        $oldl = strtolower($old);
        $this->pl->db->prepare("UPDATE island SET name = '$new' WHERE lower(name)='$oldl';")->execute();
        $this->pl->db->prepare("UPDATE bank SET name = '$new' WHERE lower(name)='$oldl';")->execute();
        $this->pl->db->prepare("UPDATE expansion SET name = '$new' WHERE lower(name)='$oldl';")->execute();
        $this->pl->db->prepare("UPDATE motd SET name = '$new' WHERE lower(name)='$oldl';")->execute();
        $this->pl->db->prepare("UPDATE info SET name = '$new' WHERE lower(name)='$oldl';")->execute();
        $this->pl->db->prepare("UPDATE info2 SET name = '$new' WHERE lower(name)='$oldl';")->execute();
        $this->pl->db->prepare("UPDATE info4 SET name = '$new' WHERE lower(name)='$oldl';")->execute();
        $this->pl->db->prepare("UPDATE info8 SET name = '$new' WHERE lower(name)='$oldl';")->execute();
        $this->pl->db->prepare("UPDATE info8pref SET name = '$new' WHERE lower(name)='$oldl';")->execute();
        $this->pl->db->prepare("UPDATE lock SET name = '$new' WHERE lower(name)='$oldl';")->execute();
        $this->pl->db->prepare("UPDATE level SET name = '$new' WHERE lower(name)='$oldl';")->execute();
        $this->pl->db->prepare("UPDATE home SET name = '$new' WHERE lower(name)='$oldl';")->execute();
        RecordDB::rename("island", $old, $new);
    }

    /**
     * @param string $name
     */
    public function delIsland(string $name) : void {
        $name = strtolower($name);
        $this->pl->db->prepare("DELETE FROM island WHERE lower(name) = '$name';")->execute();
        $this->pl->db->prepare("DELETE FROM bank WHERE lower(name) = '$name';")->execute();
        $this->pl->db->prepare("DELETE FROM expansion WHERE lower(name) = '$name';")->execute();
        $this->pl->db->prepare("DELETE FROM motd WHERE lower(name) = '$name';")->execute();
        $this->pl->db->prepare("DELETE FROM info WHERE lower(name) = '$name';")->execute();
        $this->pl->db->prepare("DELETE FROM info2 WHERE lower(name) = '$name';")->execute();
        $this->pl->db->prepare("DELETE FROM info4 WHERE lower(name) = '$name';")->execute();
        $this->pl->db->prepare("DELETE FROM info8 WHERE lower(name) = '$name';")->execute();
        $this->pl->db->prepare("DELETE FROM info8pref WHERE lower(name) = '$name';")->execute();
        $this->pl->db->prepare("DELETE FROM lock WHERE lower(name) = '$name';")->execute();
        $this->pl->db->prepare("DELETE FROM level WHERE lower(name) = '$name';")->execute();
        $this->pl->db->prepare("DELETE FROM home WHERE lower(name) = '$name';")->execute();
    }

    public function renameGang(string $old, string $new) : void {
        $oldl = strtolower($old);
        $this->pl->db->prepare("UPDATE gang SET gang = '$new' WHERE lower(gang)='$oldl';")->execute();
        $this->pl->db->prepare("UPDATE creator SET gang = '$new' WHERE lower(gang)='$oldl';")->execute();
        RecordDB::rename("gang", $old, $new);
    }

    /**
     * @param string $name
     */
    public function delGang(string $name) : void {
        $name = strtolower($name);
        $this->pl->db->prepare("DELETE FROM gang WHERE lower(gang) = '$name';")->execute();
        $this->pl->db->prepare("DELETE FROM creator WHERE lower(gang) = '$name';")->execute();
    }

    /**
     * @param string $name
     * @param string $world
     * @param string $owner
     */
    public function newIsland(string $name, string $world, string $owner) : void {
        $owner = strtolower($owner);
        $stmt = $this->pl->db->prepare("INSERT INTO island (name, world) VALUES (:name, :world);");
        $stmt->bindValue(":name", $name);
        $stmt->bindValue(":world", $world);
        $stmt->execute();
        $stmt2 = $this->pl->db->prepare("INSERT INTO info (name, owner, helpers, admins, coowners, receiver, perms, spawner, oregen, autominer, autoseller, hopper, farm, vlimit, extradata) VALUES (:name, :owner, :helpers, :admins, :coowners, :receiver, :perms, :spawner, :oregen, :autominer, :autoseller, :hopper, :farm, :vlimit, :extradata);");
        $stmt2->bindValue(":name", $name);
        $stmt2->bindValue(":owner", $owner);
        $stmt2->bindValue(":helpers", "");
        $stmt2->bindValue(":admins", "");
        $stmt2->bindValue(":coowners", "");
        $stmt2->bindValue(":receiver", "");
        $stmt2->bindValue(":perms", "");
        $stmt2->bindValue(":spawner", 0);
        $stmt2->bindValue(":oregen", 0);
        $stmt2->bindValue(":autominer", 0);
        $stmt2->bindValue(":autoseller", 0);
        $stmt2->bindValue(":hopper", 0);
        $stmt2->bindValue(":farm", 0);
        $stmt2->bindValue(":vlimit", 0);
        $stmt2->bindValue(":extradata", "");
        $stmt2->execute();
        $stmt2 = $this->pl->db->prepare("INSERT INTO info2 (name, creator, bans, mining, farming) VALUES (:name, :creator, :bans, :mining, :farming);");
        $stmt2->bindValue(":name", $name);
        $stmt2->bindValue(":creator", $owner);
        $stmt2->bindValue(":bans", "");
        $stmt2->bindValue(":mining", 0);
        $stmt2->bindValue(":farming", 0);
        $stmt2->execute();
        $stmt2 = $this->pl->db->prepare("INSERT INTO info4 (name, miners, farmers, placers, builders, labourers, butchers) VALUES (:name, :miners, :farmers, :placers, :builders, :labourers, :butchers);");
        $stmt2->bindValue(":name", $name);
        $stmt2->bindValue(":miners", "");
        $stmt2->bindValue(":farmers", "");
        $stmt2->bindValue(":placers", "");
        $stmt2->bindValue(":builders", "");
        $stmt2->bindValue(":labourers", "");
        $stmt2->bindValue(":butchers", "");
        $stmt2->execute();
        $stmt4 = $this->pl->db->prepare("INSERT INTO lock (name, locked) VALUES (:name, :locked);");
        $stmt4->bindValue(":name", $name);
        $stmt4->bindValue(":locked", "false");
        $stmt4->execute();
        $stmt5 = $this->pl->db->prepare("INSERT INTO level (name, points, level) VALUES (:name, :points, :level);");
        $stmt5->bindValue(":name", $name);
        $stmt5->bindValue(":points", 0);
        $stmt5->bindValue(":level", 1);
        $stmt5->execute();
        $stmt6 = $this->pl->db->prepare("INSERT INTO bank (name, money) VALUES (:name, :money);");
        $stmt6->bindValue(":name", $name);
        $stmt6->bindValue(":money", 0);
        $stmt6->execute();
        $stmt7 = $this->pl->db->prepare("INSERT INTO expansion (name, radius) VALUES (:name, :radius);");
        $stmt7->bindValue(":name", $name);
        $stmt7->bindValue(":radius", 10);
        $stmt7->execute();
        $stmt8 = $this->pl->db->prepare("INSERT INTO motd (name, motd) VALUES (:name, :motd);");
        $stmt8->bindValue(":name", $name);
        $stmt8->bindValue(":motd", "");
        $stmt8->execute();
        $stmt9 = $this->pl->db->prepare("INSERT INTO info8 (name, coal, copper, iron, lapis, gold, diamond, emerald, quartz, netherite, deep_coal, deep_copper, deep_iron, deep_lapis, deep_gold, deep_diamond, deep_emerald, deep_quartz, deep_netherite) VALUES (:name, :coal, :copper, :iron, :lapis, :gold, :diamond, :emerald, :quartz, :netherite, :deep_coal, :deep_copper, :deep_iron, :deep_lapis, :deep_gold, :deep_diamond, :deep_emerald, :deep_quartz, :deep_netherite)");
        $stmt9->bindValue(":name", $name);
        $stmt9->bindValue(":coal", 1);
        $stmt9->bindValue(":copper", 0);
        $stmt9->bindValue(":iron", 0);
        $stmt9->bindValue(":lapis", 0);
        $stmt9->bindValue(":gold", 0);
        $stmt9->bindValue(":diamond", 0);
        $stmt9->bindValue(":emerald", 0);
        $stmt9->bindValue(":quartz", 0);
        $stmt9->bindValue(":netherite", 0);
        $stmt9->bindValue(":deep_coal", 0);
        $stmt9->bindValue(":deep_copper", 0);
        $stmt9->bindValue(":deep_iron", 0);
        $stmt9->bindValue(":deep_lapis", 0);
        $stmt9->bindValue(":deep_gold", 0);
        $stmt9->bindValue(":deep_diamond", 0);
        $stmt9->bindValue(":deep_emerald", 0);
        $stmt9->bindValue(":deep_quartz", 0);
        $stmt9->bindValue(":deep_netherite", 0);
        $stmt9->execute();
        $stmt10 = $this->pl->db->prepare("INSERT INTO info8pref (name, coal, copper, iron, lapis, gold, diamond, emerald, quartz, netherite, deep_coal, deep_copper, deep_iron, deep_lapis, deep_gold, deep_diamond, deep_emerald, deep_quartz, deep_netherite) VALUES (:name, :coal, :copper, :iron, :lapis, :gold, :diamond, :emerald, :quartz, :netherite, :deep_coal, :deep_copper, :deep_iron, :deep_lapis, :deep_gold, :deep_diamond, :deep_emerald, :deep_quartz, :deep_netherite)");
        $stmt10->bindValue(":name", $name);
        $stmt10->bindValue(":cobblestone", 19);
        $stmt10->bindValue(":coal", 1);
        $stmt10->bindValue(":copper", 0);
        $stmt10->bindValue(":iron", 0);
        $stmt10->bindValue(":lapis", 0);
        $stmt10->bindValue(":gold", 0);
        $stmt10->bindValue(":diamond", 0);
        $stmt10->bindValue(":emerald", 0);
        $stmt10->bindValue(":quartz", 0);
        $stmt10->bindValue(":netherite", 0);
        $stmt10->bindValue(":deep_coal", 0);
        $stmt10->bindValue(":deep_copper", 0);
        $stmt10->bindValue(":deep_iron", 0);
        $stmt10->bindValue(":deep_lapis", 0);
        $stmt10->bindValue(":deep_gold", 0);
        $stmt10->bindValue(":deep_diamond", 0);
        $stmt10->bindValue(":deep_emerald", 0);
        $stmt10->bindValue(":deep_quartz", 0);
        $stmt10->bindValue(":deep_netherite", 0);
        $stmt10->execute();
    }

    /**
     * @param string $player
     * @param bool   $flag
     */
    public function newUser(string $player, bool $flag) : void {
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("INSERT INTO player (player, money, mobcoin, xp, xpbank, mana, blocks, kills, deaths, killstreak, chips, won, bounty, seltag, tags, wm, homes, pref, extradata, quests) VALUES (:player, :money, :mobcoin, :xp, :xpbank, :mana, :blocks, :kills, :deaths, :killstreak, :chips, :won, :bounty, :seltag, :tags, :wm, :homes, :pref, :extradata, :quests);");
        $stmt->bindValue(":player", $player);
        $stmt->bindValue(":money", 3000);
        $stmt->bindValue(":mobcoin", 0);
        $stmt->bindValue(":xp", 0);
        $stmt->bindValue(":xpbank", 0);
        $stmt->bindValue(":mana", 50);
        $stmt->bindValue(":blocks", 0);
        $stmt->bindValue(":kills", 0);
        $stmt->bindValue(":deaths", 0);
        $stmt->bindValue(":killstreak", 0);
        $stmt->bindValue(":chips", 50);
        $stmt->bindValue(":won", 0);
        $stmt->bindValue(":bounty", 0);
        $stmt->bindValue(":seltag", -1);
        $stmt->bindValue(":tags", "");
        $stmt->bindValue(":wm", "true");
        $stmt->bindValue(":homes", "");
        $stmt->bindValue(":pref", "");
        $stmt->bindValue(":quests", "");
        $stmt->bindValue(":extradata", "");
        $stmt->execute();
        $stmt1 = $this->pl->db->prepare("INSERT INTO combat (player, level, exp) VALUES (:player, :level, :exp);");
        $stmt1->bindValue(":player", $player);
        $stmt1->bindValue(":level", 1);
        $stmt1->bindValue(":exp", 0);
        $stmt1->execute();
        $stmt2 = $this->pl->db->prepare("INSERT INTO mining (player, level, exp) VALUES (:player, :level, :exp);");
        $stmt2->bindValue(":player", $player);
        $stmt2->bindValue(":level", 1);
        $stmt2->bindValue(":exp", 0);
        $stmt2->execute();
        $stmt4 = $this->pl->db->prepare("INSERT INTO farming (player, level, exp) VALUES (:player, :level, :exp);");
        $stmt4->bindValue(":player", $player);
        $stmt4->bindValue(":level", 1);
        $stmt4->bindValue(":exp", 0);
        $stmt4->execute();
        $stmt5 = $this->pl->db->prepare("INSERT INTO gambling (player, level, exp) VALUES (:player, :level, :exp);");
        $stmt5->bindValue(":player", $player);
        $stmt5->bindValue(":level", 1);
        $stmt5->bindValue(":exp", 0);
        $stmt5->execute();
        $stmt8 = $this->pl->db->prepare("INSERT INTO timings (player, seconds) VALUES (:player, :seconds);");
        $stmt8->bindValue(":player", $player);
        $stmt8->bindValue(":seconds", 0);
        $stmt8->execute();
        $stmt9 = $this->pl->db->prepare("INSERT INTO kit (player, achilles, theo, cosmo, arcadia, artemis, calisto) VALUES (:player, :achilles, :theo, :cosmo, :arcadia, :artemis, :calisto);");
        $stmt9->bindValue(":player", $player);
        $stmt9->bindValue(":achilles", 0);
        $stmt9->bindValue(":theo", 0);
        $stmt9->bindValue(":cosmo", 0);
        $stmt9->bindValue(":arcadia", 0);
        $stmt9->bindValue(":artemis", 0);
        $stmt9->bindValue(":calisto", 0);
        $stmt9->execute();
        if ($flag) {
            $stmt10 = $this->pl->db4->prepare("INSERT INTO pets (player, name, unlocked, current) VALUES (:player, :name, :unlocked, :current);");
            $stmt10->bindValue(":player", $player);
            $stmt10->bindValue(":name", "Name");
            $stmt10->bindValue(":unlocked", "");
            $stmt10->bindValue(":current", "");
            $stmt10->execute();
        }
        $stmt1 = $this->pl->db->prepare("INSERT INTO goals (player, goal) VALUES (:player, :goal);");
        $stmt1->bindValue(":player", $player);
        $stmt1->bindValue(":goal", "");
        $stmt1->execute();
    }

    /**
     * @param string $gang
     * @param string $player
     */
    public function newGang(string $gang, string $player) : void {
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("INSERT INTO gang (player, gang, kills, deaths) VALUES (:player, :gang, :kills, :deaths);");
        $stmt->bindValue(":player", $player);
        $stmt->bindValue(":gang", $gang);
        $stmt->bindValue(":kills", 0);
        $stmt->bindValue(":deaths", 0);
        $stmt->execute();
        $stmt1 = $this->pl->db->prepare("INSERT INTO creator (gang, leader, level, points, motd) VALUES (:gang, :leader, :level, :points, :motd);");
        $stmt1->bindValue(":gang", $gang);
        $stmt1->bindValue(":leader", $player);
        $stmt1->bindValue(":level", 1);
        $stmt1->bindValue(":points", 0);
        $stmt1->bindValue(":motd", "");
        $stmt1->execute();
    }

    public function getPlayerFromId(int $id) : ?string {
        $stmt = $this->pl->db->prepare("SELECT player FROM player WHERE ROWID=:id;");
        $stmt->bindValue(":id", $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $array = $result->fetchArray(SQLITE3_ASSOC);
        if ($array === false) return null;
        return $array["player"];
    }

    public function getPlayerId(string $player) {
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("SELECT rowid FROM player WHERE lower(player)='$player';");
        $result = $stmt->execute();
        $array = $result->fetchArray(SQLITE3_ASSOC);
        return $array["rowid"];
    }

    public function setIslandLevel(string $name, int $level) {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("UPDATE level SET level = $level WHERE lower(name)='$name';");
        $stmt->execute();
    }

    public function getPlayerIslandRank(string $name, string $island) : string {
        $name = strtolower($name);
        $data = $this->getIslandInfoData($island);
        if ($name == strtolower($data['owner'])) return 'Owner';
        $coowners = [];
        $coownerstr = $data["coowners"];
        if ($coownerstr != "")
            $coowners = explode(",", $coownerstr);
        if (in_array($name, $coowners, true)) return 'CoOwner';
        $admins = [];
        $adminstr = $data["admins"];
        if ($adminstr != "")
            $admins = explode(",", $adminstr);
        if (in_array($name, $admins, true)) return 'Admin';
        $helpers = [];
        $helperstr = $data["helpers"];
        if ($helperstr != "")
            $helpers = explode(",", $helperstr);
        if (in_array($name, $helpers, true)) return 'Helper';
        return '-';
    }

    public function updateIsland(string $name, string $creator, string $player, array $home, string $helpers, string $admins, string $coowners, string $receiver, int $spawner, int $oregen, int $autominer, int $autoseller, int $hopper, int $farm, int $vlimit, string $locked, int $money, int $points, int $level, int $radius, string $motd, string $bans, int $mining, int $farming, string $perms, array $roles, array $oredata, array $oredatapref, string $islandData) : void {
        $og = $name;
        $name = strtolower($name);
        $player = strtolower($player);
        if (!empty($home)) {
            $stmt = $this->pl->db->prepare("SELECT ID, name FROM home WHERE lower(name)=:name;");
            $stmt->bindValue(":name", $name, SQLITE3_TEXT);
            $result = $stmt->execute();
            while ($resultArr = $result->fetchArray(SQLITE3_ASSOC)) {
                $id = $resultArr['ID'];
                if (!isset($home[$resultArr['name']])) $this->pl->db->prepare("DELETE FROM home WHERE lower(name) = '$name' AND ID = '$id';")->execute();
            }
            foreach ($home as $name2 => $data) {
                if (isset($data['id'])) {
                    $stmt = $this->pl->db->prepare("INSERT OR REPLACE INTO home (ID, name, x, y, z, home) VALUES (:ID, :name, :x, :y, :z, :home);");
                    $stmt->bindValue(":ID", $data['id']);
                } else {
                    $stmt = $this->pl->db->prepare("INSERT OR REPLACE INTO home (name, x, y, z, home) VALUES (:name, :x, :y, :z, :home);");
                }
                $stmt->bindValue(":name", $og);
                $stmt->bindValue(":x", $data['x']);
                $stmt->bindValue(":y", $data['y']);
                $stmt->bindValue(":z", $data['z']);
                $stmt->bindValue(":home", $name2);
                $stmt->execute();
            }
        } else    $this->pl->db->prepare("DELETE FROM home WHERE lower(name) = '$name';")->execute();
        $this->pl->db->prepare("UPDATE info SET owner = '$player', helpers = '$helpers', admins = '$admins', coowners = '$coowners', receiver = '$receiver', perms='$perms', spawner=$spawner, oregen=$oregen, autominer=$autominer, autoseller=$autoseller, hopper=$hopper, farm=$farm, vlimit=$vlimit, extradata='$islandData' WHERE lower(name)='$name';")->execute();
        $this->pl->db->prepare("UPDATE info2 SET creator = '$creator', bans = '$bans', mining = $mining, farming = $farming WHERE lower(name)='$name';")->execute();
        $this->pl->db->prepare("UPDATE info4 SET miners = '{$roles['miners']}', placers = '{$roles['placers']}', builders = '{$roles['builders']}', labourers = '{$roles['labourers']}', butchers = '{$roles['butchers']}', farmers = '{$roles['farmers']}' WHERE lower(name)='$name';")->execute();
        $this->pl->db->prepare("UPDATE bank SET money = $money WHERE lower(name)='$name';")->execute();
        $this->pl->db->prepare("UPDATE expansion SET radius = $radius WHERE lower(name)='$name';")->execute();
        $this->pl->db->prepare("UPDATE lock SET locked = '$locked' WHERE lower(name)='$name';")->execute();
        $this->pl->db->prepare("UPDATE level SET points = $points, level = $level WHERE lower(name)='$name';")->execute();
        $this->pl->db->prepare("UPDATE motd SET motd = '$motd' WHERE lower(name)='$name';")->execute();
        foreach ($oredata as $key => $value) {
            $this->pl->db->prepare("UPDATE info8 SET '$key' = '$value' WHERE lower(name)='$name';")->execute();
        }
        foreach ($oredatapref as $key => $value) {
            $this->pl->db->prepare("UPDATE info8pref SET '$key' = '$value' WHERE lower(name)='$name';")->execute();
        }
    }

    public function setIslandPerms(string $island, string $perms) {
        $name = strtolower($island);
        $this->pl->db->prepare("UPDATE info SET perms='$perms' WHERE lower(name)='$name';")->execute();
    }

    public function setUserPets(string $player, string $pets) {
        $player = strtolower($player);
        $this->pl->db4->prepare("UPDATE pets SET unlocked = '$pets' WHERE lower(player)='$player';")->execute();
    }

    public function updateUser(string $player, float $money, int $mobcoin, int $xp, int $xpbank, int $mana, int $blocks, int $kills, int $deaths, int $streak, int $chips, int $won, int $bounty, array $base, int $seconds, string $goals, array $kits, string $pets, string $pet, string $petname, int $seltag, string $tags, string $wm, string $homes, string $pref, string $extradata, string $quests) : void {
        $player = strtolower($player);
        $combat = $base["combat"];
        $gambling = $base["gambling"];
        $farming = $base["farming"];
        $mining = $base["mining"];
        if ($this->hasPets($player)) $this->pl->db4->prepare("UPDATE pets SET unlocked = '$pets', current = '$pet', name = '$petname' WHERE lower(player)='$player';")->execute();
        else {
            $stmt = $this->pl->db4->prepare("INSERT OR REPLACE INTO pets (player, name, unlocked, current) VALUES (:player, :name, :unlocked, :current);");
            $stmt->bindValue(":player", $player);
            $stmt->bindValue(":name", $petname);
            $stmt->bindValue(":unlocked", $pets);
            $stmt->bindValue(":current", $pet);
            $stmt->execute();
        }
        if ($this->hasTimings($player)) $this->pl->db->prepare("UPDATE timings SET seconds = $seconds WHERE lower(player)='$player';")->execute();
        else    $this->setTimings($player, $seconds);
        $this->pl->db->prepare("UPDATE player SET xp = $xp, xpbank = $xpbank, mana = $mana, blocks = $blocks, money = $money, mobcoin = $mobcoin, kills = $kills, deaths = $deaths, killstreak = $streak, chips = $chips, won = $won, bounty = $bounty, seltag = $seltag, tags='$tags', wm='$wm', homes='$homes', pref='$pref', extradata='$extradata', quests='$quests' WHERE lower(player)='$player';")->execute();
        $this->pl->db->prepare("UPDATE combat SET level = {$combat["level"]}, exp = {$combat["exp"]} WHERE lower(player)='$player';")->execute();
        $this->pl->db->prepare("UPDATE farming SET level = {$farming["level"]}, exp = {$farming["exp"]} WHERE lower(player)='$player';")->execute();
        $this->pl->db->prepare("UPDATE mining SET level = {$mining["level"]}, exp = {$mining["exp"]} WHERE lower(player)='$player';")->execute();
        $this->pl->db->prepare("UPDATE gambling SET level = {$gambling["level"]}, exp = {$gambling["exp"]} WHERE lower(player)='$player';")->execute();
        $this->pl->db->prepare("UPDATE kit SET achilles = {$kits["achilles"]}, theo = {$kits["theo"]}, cosmo = {$kits["cosmo"]}, arcadia = {$kits["arcadia"]}, artemis = {$kits["artemis"]}, calisto = {$kits["calisto"]} WHERE lower(player)='$player';")->execute();
        $this->pl->db->prepare("UPDATE goals SET goal = '$goals' WHERE lower(player)='$player';")->execute();
    }

    public function hasPets(string $player) : bool {
        $player = strtolower($player);
        $stmt = $this->pl->db4->prepare("SELECT player FROM pets WHERE lower(player)=:player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC) !== false;
    }

    public function hasTimings(string $player) : bool {
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("SELECT player FROM timings WHERE lower(player)=:player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC) !== false;
    }

    public function setTimings(string $player, int $seconds = 0) {
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("INSERT OR REPLACE INTO timings (player, seconds) VALUES (:player, :seconds);");
        $stmt->bindValue(":player", $player);
        $stmt->bindValue(":seconds", $seconds);
        $stmt->execute();
    }

    public function updateGang(string $gang, string $leader, array $members, array $kills, array $deaths, string $motd, int $level, int $points) {
        $lower = strtolower($gang);
        $mem[$leader] = "leader";
        foreach ($members as $memb) {
            if (!isset($mem[$memb])) $mem[$memb] = "member";
        }
        $stmt = $this->pl->db->prepare("SELECT player FROM gang WHERE lower(gang)=:gang;");
        $stmt->bindValue(":gang", $lower, SQLITE3_TEXT);
        $result = $stmt->execute();
        while ($resultArr = $result->fetchArray(SQLITE3_ASSOC)) {
            $player = strtolower($resultArr['player']);
            if (!isset($mem[$player])) $this->pl->db->prepare("DELETE FROM gang WHERE lower(gang) = '$lower' AND lower(player) = '$player';")->execute();
        }
        foreach ($mem as $player => $rank) {
            if ($this->isAGangMember($gang, $player)) {
                $kill = $kills[$player];
                $death = $deaths[$player];
                $this->pl->db->prepare("UPDATE gang SET kills = $kill, deaths = $death WHERE lower(gang)='$lower' AND lower(player)='$player';")->execute();
            } else {
                $stmt = $this->pl->db->prepare("INSERT OR REPLACE INTO gang (player, gang, kills, deaths) VALUES (:player, :gang, :kills, :deaths);");
                $stmt->bindValue(":player", $player);
                $stmt->bindValue(":gang", $gang);
                $stmt->bindValue(":kills", $kills[$player]);
                $stmt->bindValue(":deaths", $deaths[$player]);
                $stmt->execute();
            }
        }
        $this->pl->db->prepare("UPDATE creator SET leader = '$leader', level = $level, points = $points, motd = '$motd' WHERE lower(gang)='$lower';")->execute();
    }

    public function isAGangMember(string $name, string $player) : bool {
        $name = strtolower($name);
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("SELECT player FROM gang WHERE lower(gang)='$name' AND lower(player)=:player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC) !== false;
    }

    public function updateUserGang(string $player, string $gang, int $kills, int $deaths, bool $leader) {
        $player = strtolower($player);
        $lower = strtolower($gang);
        if ($leader) $this->pl->db->prepare("UPDATE creator SET leader = '$player' WHERE lower(gang)='$lower';")->execute();
        if ($this->isAGangMember($gang, $player)) {
            $this->pl->db->prepare("UPDATE gang SET kills = $kills, deaths = $deaths WHERE lower(gang)='$lower' AND lower(player)='$player';")->execute();
        } else {
            $this->deleteGangMember($player);
            $stmt = $this->pl->db->prepare("INSERT OR REPLACE INTO gang (player, gang, kills, deaths) VALUES (:player, :gang, :kills, :deaths);");
            $stmt->bindValue(":player", $player);
            $stmt->bindValue(":gang", $gang);
            $stmt->bindValue(":kills", $kills);
            $stmt->bindValue(":deaths", $deaths);
            $stmt->execute();
        }
    }

    public function deleteGangMember(string $player) {
        $player = strtolower($player);
        $this->pl->db->prepare("DELETE FROM gang WHERE lower(player) = '$player';")->execute();
    }

    public function isNameUsed(string $name) : bool {
        $name = strtolower($name);
        if ((str_contains($name, "'")) or (str_contains($name, '"'))) return false;
        $stmt = $this->pl->db->prepare("SELECT name FROM island WHERE lower(name)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC) !== false;
    }

    public function isGangNameUsed(string $name) : bool {
        $name = strtolower($name);
        if ((str_contains($name, "'")) or (str_contains($name, '"'))) return false;
        $stmt = $this->pl->db->prepare("SELECT gang FROM gang WHERE lower(gang)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC) !== false;
    }

    public function hasGang(string $player) : bool {
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("SELECT player FROM gang WHERE lower(player)=:player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC) !== false;
    }

    public function setUserMana(string $player, int $mana) {
        $player = strtolower($player);
        $this->pl->db->prepare("UPDATE player SET mana = $mana WHERE lower(player)='$player';")->execute();
    }

    public function setUserMobCoin(string $player, int $coin) {
        $player = strtolower($player);
        $this->pl->db->prepare("UPDATE player SET mobcoin = $coin WHERE lower(player)='$player';")->execute();
    }

    public function setUserMoney(string $player, float $money) {
        $player = strtolower($player);
        $this->pl->db->prepare("UPDATE player SET money = $money WHERE lower(player)='$player';")->execute();
    }

    public function setUserXp(string $player, int $xp) {
        $player = strtolower($player);
        $this->pl->db->prepare("UPDATE player SET xp = $xp WHERE lower(player)='$player';")->execute();
    }


    public function setGangLevel(string $name, int $level) {
        $name = strtolower($name);
        $this->pl->db->prepare("UPDATE creator SET level = $level WHERE lower(gang)='$name';")->execute();
    }

    public function setGangPoints(string $name, int $points) {
        $name = strtolower($name);
        $this->pl->db->prepare("UPDATE creator SET points = $points WHERE lower(gang)='$name';")->execute();
    }

    public function getVotes() {
        if ($this->hasVotes()) {
            $stmt = $this->pl->db->prepare("SELECT votes FROM votes WHERE server=:server;");
            $stmt->bindValue(":server", 'fallentech', SQLITE3_TEXT);
            $result = $stmt->execute();
            $resultArray = $result->fetchArray(SQLITE3_ASSOC);
            $votes = $resultArray["votes"];
        } else $votes = 30;
        return $votes;
    }

    public function hasVotes() : bool {
        $stmt = $this->pl->db->prepare("SELECT server FROM votes WHERE server=:server;");
        $stmt->bindValue(":server", 'fallentech', SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC) !== false;
    }

    public function setVotes(int $votes) {
        if ($this->hasVotes())
            $this->pl->db->prepare("UPDATE votes SET votes = $votes WHERE server='fallentech';")->execute();
        else {
            $stmt = $this->pl->db->prepare("INSERT OR REPLACE INTO votes (server, votes) VALUES (:server, :votes);");
            $stmt->bindValue(":server", 'fallentech');
            $stmt->bindValue(":votes", $votes);
            $stmt->execute();
        }
    }

    public function getPetsData(string $player) : bool|array {
        $player = strtolower($player);
        $stmt = $this->pl->db4->prepare("SELECT * FROM pets WHERE lower(player) = :player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function getTimePlayed(string $player) : string {
        return Util::getTimePlayed($this->getTimings(strtolower($player)));
    }

    public function getTimings(string $player) : int {
        $player = strtolower($player);
        if ($this->hasTimings($player)) {
            $stmt = $this->pl->db->prepare("SELECT seconds FROM timings WHERE lower(player) = :player;");
            $stmt->bindValue(":player", $player, SQLITE3_TEXT);
            $result = $stmt->execute();
            $resultArr = $result->fetchArray(SQLITE3_ASSOC);
            return (int) $resultArr["seconds"];
        } else return 0;
    }

    public function getPlayerXP(string $player) : int {
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("SELECT xp FROM player WHERE lower(player)=:player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["xp"];
    }

    public function getPlayerXPBank(string $player) : int {
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("SELECT xpbank FROM player WHERE lower(player)=:player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["xpbank"];
    }

    public function getPlayerMana(string $player) : int {
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("SELECT mana FROM player WHERE lower(player)=:player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["mana"];
    }

    public function addUserMana(string $player, int $mana) : void {
        $player = strtolower($player);
        $newmana = $this->getPlayerMana($player) + $mana;
        $this->pl->db->prepare("UPDATE player SET mana = $newmana WHERE lower(player)='$player';")->execute();
    }

    public function subtractUserMana(string $player, int $mana) : void {
        $player = strtolower($player);
        $newmana = $this->getPlayerMana($player) - $mana;
        $this->pl->db->prepare("UPDATE player SET mana = $newmana WHERE lower(player)='$player';")->execute();
    }

    public function addUserMobCoin(string $player, int $coin) {
        $player = strtolower($player);
        $newcoin = $this->getPlayerMobCoin($player) + $coin;
        $this->pl->db->prepare("UPDATE player SET mobcoin = $newcoin WHERE lower(player)='$player';")->execute();
    }

    public function getPlayerMobCoin(string $player) {
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("SELECT mobcoin FROM player WHERE lower(player)=:player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["mobcoin"];
    }

    public function subtractUserMobCoin(string $player, int $coin) {
        $player = strtolower($player);
        $newcoin = $this->getPlayerMobCoin($player) - $coin;
        $this->pl->db->prepare("UPDATE player SET mobcoin = $newcoin WHERE lower(player)='$player';")->execute();
    }

    public function addUserMoney(string $player, float $money) {
        $player = strtolower($player);
        $newmoney = $this->getPlayerMoney($player) + $money;
        $this->pl->db->prepare("UPDATE player SET money = $newmoney WHERE lower(player)='$player';")->execute();
    }

    public function getPlayerBounty(string $player) {
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("SELECT bounty FROM player WHERE lower(player)=:player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["bounty"];
    }

    public function getPlayerMoney(string $player) {
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("SELECT money FROM player WHERE lower(player)=:player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["money"];
    }

    public function subtractUserMoney(string $player, float $money) {
        $player = strtolower($player);
        $newmoney = $this->getPlayerMoney($player) - $money;
        $this->pl->db->prepare("UPDATE player SET money = $newmoney WHERE lower(player)='$player';")->execute();
    }

    public function addIslandMoney(string $name, int $money) {
        $name = strtolower($name);
        $newmoney = $this->getIslandMoney($name) + $money;
        $this->pl->db->prepare("UPDATE bank SET money = $newmoney WHERE lower(name)='$name';")->execute();
    }

    public function getIslandMoney(string $name) {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("SELECT money FROM bank WHERE lower(name)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["money"];
    }

    public function isPlayerRegistered(string $player) : bool {
        $player = strtolower($player);
        if ((str_contains($player, "'")) or (str_contains($player, '"'))) return false;
        $stmt = $this->pl->db->prepare("SELECT player FROM player WHERE lower(player)=:player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC) !== false;
    }

    public function getPlayerGang(string $player) : ?string {
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("SELECT gang FROM gang WHERE lower(player)=:player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["gang"] ?? null;
    }

    public function getPlayerGoals(string $player) : ?string {
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("SELECT goal FROM goals WHERE lower(player)=:player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["goal"];
    }

    public function getPlayerKits(string $player) : bool|array {
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("SELECT * FROM kit WHERE lower(player)=:player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        unset($resultArray["player"]);
        return $resultArray;
    }

    public function addKitCount(string $player, string $type, int $count = 1) {
        $player = strtolower($player);
        $newcount = $this->getKitCount($player, $type) + $count;
        $this->pl->db->prepare("UPDATE kit SET $type = $newcount WHERE lower(player)='$player';")->execute();
    }

    public function getKitCount(string $player, string $type) {
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("SELECT $type FROM kit WHERE lower(player)=:player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray[$type];
    }

    public function getIslandRadius(string $name) {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("SELECT radius FROM expansion WHERE lower(name)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["radius"];
    }

    public function getIslandMotd(string $name) {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("SELECT motd FROM motd WHERE lower(name)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["motd"];
    }

    public function getGangData(string $name) : bool|array {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("SELECT * FROM creator WHERE lower(gang)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function getGangMemberKills(string $name) : array {
        $name = strtolower($name);
        $kills = [];
        $stmt = $this->pl->db->prepare("SELECT gang, player, kills FROM gang WHERE lower(gang)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        while ($resultArr = $result->fetchArray(SQLITE3_ASSOC))
            $kills[$resultArr['player']] = $resultArr['kills'];
        return $kills;
    }

    public function getGangMemberDeaths(string $name) : array {
        $name = strtolower($name);
        $deaths = [];
        $stmt = $this->pl->db->prepare("SELECT gang, player, deaths FROM gang WHERE lower(gang)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        while ($resultArr = $result->fetchArray(SQLITE3_ASSOC))
            $deaths[$resultArr['player']] = $resultArr['deaths'];
        return $deaths;
    }

    public function getPlayerBase(string $player) : array {
        return [
            "combat"   => [
                "level" => ($cdata = $this->getBaseData($player, "combat"))["level"], "exp" => $cdata["exp"]
            ],
            "gambling" => [
                "level" => ($gdata = $this->getBaseData($player, "gambling"))["level"], "exp" => $gdata["exp"]
            ],
            "mining"   => [
                "level" => ($mdata = $this->getBaseData($player, "mining"))["level"], "exp" => $mdata["exp"]
            ],
            "farming"  => [
                "level" => ($fdata = $this->getBaseData($player, "farming"))["level"], "exp" => $fdata["exp"]
            ]
        ];
    }

    public function getBaseData(string $player, string $type) : bool|array {
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("SELECT * FROM {$type} WHERE lower(player)=:player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function getIslandLevelData(string $name) : bool|array {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("SELECT * FROM level WHERE lower(name)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function getWorldName(string $name) {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("SELECT world FROM island WHERE lower(name)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["world"];
    }

    public function getIslandLocked(string $name) {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("SELECT name, locked FROM lock WHERE lower(name)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["locked"];
    }

    public function getPlayerValues(string $player) : bool|array {
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("SELECT * FROM player WHERE lower(player)=:player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getHomeArray(string $name) : array {
        $name = strtolower($name);
        $homes = [];
        $stmt = $this->pl->db->prepare("SELECT ID, x, y, z, name, home FROM home WHERE lower(name)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        while ($resultArr = $result->fetchArray(SQLITE3_ASSOC))
            $homes[$resultArr['home']] = ['x' => $resultArr['x'], 'y' => $resultArr['y'], 'z' => $resultArr['z'], 'id' => $resultArr['ID']];
        return $homes;
    }

    public function getMemberKills(string $name, string $player) {
        $name = strtolower($name);
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("SELECT kills FROM gang WHERE lower(gang)=:name AND lower(player)=:player;");
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["kills"];
    }

    public function getMemberDeaths(string $name, string $player) {
        $name = strtolower($name);
        $player = strtolower($player);
        $stmt = $this->pl->db->prepare("SELECT deaths FROM gang WHERE lower(gang)=:name AND lower(player)=:player;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $stmt->bindValue(":player", $player, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["deaths"];
    }

    public function getTotalKills(string $name) {
        $name = strtolower($name);
        $total = 0;
        $stmt = $this->pl->db->prepare("SELECT gang, kills FROM gang WHERE lower(gang)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        while ($resultArr = $result->fetchArray(SQLITE3_ASSOC))
            $total = $total + $resultArr['kills'];
        return $total;
    }

    public function getTotalDeaths(string $name) {
        $name = strtolower($name);
        $total = 0;
        $stmt = $this->pl->db->prepare("SELECT gang, deaths FROM gang WHERE lower(gang)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        while ($resultArr = $result->fetchArray(SQLITE3_ASSOC))
            $total = $total + $resultArr['deaths'];
        return $total;
    }

    public function getIslandOwner(string $name) {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("SELECT owner FROM info WHERE lower(name)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["owner"];
    }

    public function getGangLeader(string $name) {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("SELECT leader FROM creator WHERE lower(gang)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["leader"];
    }

    public function getMembersCount(string $name) {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("SELECT COUNT(*) as count FROM gang WHERE lower(gang) = :name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        $number = $result->fetchArray();
        return $number['count'];
    }

    public function getMembersLimit(string $name) : int {
        $name = strtolower($name);
        $mem = 3;
        $rem = (int) ($this->getGangLevel($name) / 5);
        $mem = $mem + $rem;
        if ($mem > 50) $mem = 50;
        return $mem;
    }

    public function getGangLevel(string $name) {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("SELECT level FROM creator WHERE lower(gang)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["level"];
    }

    public function getIslandInfo2Data(string $name) : bool|array {
        $name = strtolower($name);
        if ($this->hasIslandInfo2($name)) {
            $stmt = $this->pl->db->prepare("SELECT * FROM info2 WHERE lower(name)=:name;");
            $stmt->bindValue(":name", $name, SQLITE3_TEXT);
            $result = $stmt->execute();
            return $result->fetchArray(SQLITE3_ASSOC);
        } else {
            $this->addIslandInfo2Data($name);
            return ['creator' => "", 'bans' => "", 'mining' => 0, 'farming' => 0];
        }
    }

    public function getIslandInfo4Data(string $name) : bool|array {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("SELECT * FROM info4 WHERE lower(name)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function addIslandInfo2Data(string $name) {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("INSERT INTO info2 (name, creator, bans, mining, farming) VALUES (:name, :creator, :bans, :mining, :farming);");
        $stmt->bindValue(":name", $name);
        $stmt->bindValue(":creator", "");
        $stmt->bindValue(":bans", "");
        $stmt->bindValue(":mining", 0);
        $stmt->bindValue(":farming", 0);
        $stmt->execute();
    }

    public function getIslandInfoData(string $name) : bool|array|null {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("SELECT * FROM info WHERE lower(name)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        if (($resp = $result->fetchArray(SQLITE3_ASSOC)) !== false) return $resp;
        else return null;
    }

    public function getHelpers(string $name) {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("SELECT helpers FROM info WHERE lower(name)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArray["helpers"];
    }

    public function getGangMembers(string $name) : array {
        $name = strtolower($name);
        $members = [];
        $stmt = $this->pl->db->prepare("SELECT player FROM gang WHERE lower(gang)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        while ($resultArr = $result->fetchArray(SQLITE3_ASSOC))
            $members[] = $resultArr['player'];
        return $members;
    }

    public function hasIslandInfo2(string $name) : bool {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("SELECT name FROM info2 WHERE lower(name)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC) !== false;
    }

    public function removeCoowner(string $player, string $name, array $coowners) {
        $player = strtolower($player);
        $key = array_search($player, $coowners, true);
        if ($key != false) {
            $user = $this->pl->getUserManager()->getOnlineUser($player);
            $user->setIsland();
            unset($coowners[$key]);
            $coowner = implode(",", $coowners);
            $this->setCoowner($name, $coowner);
        }
    }

    public function setCoowner(string $name, string $coowners) {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("UPDATE info SET coowners = '$coowners' WHERE lower(name)='$name';");
        $stmt->execute();
    }

    public function removeAdmin(string $player, string $name, array $admins) {
        $player = strtolower($player);
        $key = array_search($player, $admins, true);
        if ($key != false) {
            unset($admins[$key]);
            $admin = implode(",", $admins);
            $this->setAdmins($name, $admin);
        }
    }

    public function setAdmins(string $name, string $admins) {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("UPDATE info SET admins = '$admins' WHERE lower(name)='$name';");
        $stmt->execute();
    }

    public function removeHelper(string $player, string $name, array $helpers) {
        $player = strtolower($player);
        $key = array_search($player, $helpers, true);
        unset($helpers[$key]);
        $helper = implode(",", $helpers);
        $this->setHelpers($name, $helper);
    }

    public function setHelpers(string $name, string $helpers) {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("UPDATE info SET helpers = '$helpers' WHERE lower(name)='$name';");
        $stmt->execute();
    }

    /**
     * get the Owned Island (Owner or CoOwner)
     *
     * @param string $player
     *
     * @return string|null
     */
    public function getPlayerIsland(string $player) : ?string {
        $player = strtolower($player);
        $result = $this->pl->db->query("SELECT name FROM info WHERE lower(owner) = '$player';");
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        if ($resultArray !== false) return $resultArray["name"];
        $result = $this->pl->db->query("SELECT name FROM info WHERE lower(coowners) LIKE '$player,%' OR lower(coowners) LIKE '%,$player,%' OR lower(coowners) LIKE '%,$player' OR lower(coowners) LIKE '$player';");
        $resultArray = $result->fetchArray(SQLITE3_ASSOC);
        if ($resultArray !== false) return $resultArray["name"];
        return null;
    }

    public function getPlayerIslands(string $player) : array {
        $player = strtolower($player);
        $islands = [];
        $result = $this->pl->db->query("SELECT name FROM info WHERE lower(helpers) LIKE '$player,%' OR lower(helpers) LIKE '%,$player,%' OR lower(helpers) LIKE '%,$player' OR lower(helpers) LIKE '$player';");
        while ($resultArr = $result->fetchArray(SQLITE3_ASSOC)) {
            $islands[] = $resultArr['name'];
        }
        return $islands;
    }

    public function removePlayerGang(string $player, string $name) {
        $player = strtolower($player);
        $name = strtolower($name);
        $this->pl->db->prepare("DELETE FROM gang WHERE lower(player) = '$player' AND lower(gang) = '$name';")->execute();
    }

    public function getIslandInfo8Data(string $name) : bool|array {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("SELECT * FROM info8 WHERE lower(name)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function getIslandInfo8DataPref(string $name) : bool|array {
        $name = strtolower($name);
        $stmt = $this->pl->db->prepare("SELECT * FROM info8pref WHERE lower(name)=:name;");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

}