<?php

namespace SkyBlock\user;

use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use SkyBlock\db\RecordDB;
use SkyBlock\Main;
use SkyBlock\ScoreboardAPI;
use SkyBlock\tasks\ScoreboardTask;
use SkyBlock\util\data_object\PlayerExtraData;
use SkyBlock\util\data_object\PrefData;
use SkyBlock\util\data_object\QuestData;
use SkyBlock\util\Util;
use SkyBlock\util\Values;

class User {

    /** @var string */
    private string $username;
    /** @var int */
    private int $kills;
    /** @var int */
    private int $deaths, $xp, $xpbank;
    /** @var string|null */
    private ?string $lastattacker = null;
    /** @var string */
    private string $island;
    /** @var array */
    private array $islands;
    /** @var int */
    private int $streak;
    /** @var int */
    private int $chips;
    /** @var int */
    private int $won, $bounty;
    /** @var int */
    private int $mobcoin, $mana, $blocks;
    private float $money;
    /** @var array */
    private array $base;
    /** @var int */
    private int $seltag;
    /** @var array */
    private array $goals, $homes;
    /** @var int */
    private int $seconds;
    /** @var string */
    private string $gang;
    /** @var array */
    private array $kits;
    /** @var array */
    private array $pets;
    /** @var array */
    private array $tags;
    /** @var string */
    private string $pet, $wm;
    /** @var string */
    private string $petname;
    /** @var ScoreboardTask */
    private ScoreboardTask $task;
    /** @var ScoreboardAPI */
    private ScoreboardAPI $scoreboard;
    /** @var PrefData */
    private PrefData $preferences;
    private QuestData $quests;
    /** @var PlayerExtraData */
    private PlayerExtraData $playerExtraData;

    public function __construct(string $username, $island, $money, $mobcoin, $xp, $xpbank, $mana, $blocks, $kills, $deaths, $streak, $chips, $won, $bounty, $islands, $base, $seconds, $gang, $goals, $kits, $pets, $pet, $petname, $seltag, $tags, $wm, $homes, $pref, $extradata, $quests) {
        $this->username = $username;
        $this->island = $island;
        $this->money = $money;
        $this->mobcoin = $mobcoin;
        $this->xp = $xp;
        $this->xpbank = $xpbank;
        $this->mana = $mana;
        $this->blocks = $blocks;
        $this->kills = $kills;
        $this->deaths = $deaths;
        $this->streak = $streak;
        $this->chips = $chips;
        $this->won = $won;
        $this->bounty = $bounty;
        $this->islands = $islands;
        $this->base = $base;
        $this->seconds = $seconds;
        $this->gang = $gang;
        $this->goals = $goals;
        $this->kits = $kits;
        $this->pets = $pets;
        $this->pet = $pet;
        $this->petname = $petname;
        $this->seltag = $seltag;
        $this->tags = $tags;
        $this->wm = $wm;
        $this->homes = $homes;

        $this->preferences = new PrefData($pref);
        $this->quests = new QuestData($quests);
        $this->playerExtraData = new PlayerExtraData($extradata);
        $this->scoreboard = new ScoreboardAPI($this->getPlayer());
        $this->task = new ScoreboardTask(Main::getInstance(), $this);
    }

    /**
     * @return PrefData
     */
    public function getPref() : PrefData {
        return $this->preferences ?? new PrefData();
    }


    /**
     * @return QuestData
     */
    public function getQuests() : QuestData {
        return $this->quests;
    }

    /**
     * @return PlayerExtraData
     */
    public function getExtraData() : PlayerExtraData {
        return $this->playerExtraData ?? new PlayerExtraData();
    }

    /**
     * @return string
     */
    public function getWm() : string {
        return $this->wm;
    }

    /**
     * @param string|null $lastattacker
     */
    public function setLastAttacker(?string $lastattacker) : void {
        $this->lastattacker = $lastattacker;
    }

    /**
     * @return Player|null
     */
    public function getLastAttacker() : ?Player {
        if ($this->lastattacker === null) return null;
        else return Server::getInstance()->getPlayerExact($this->lastattacker);
    }

    /**
     * @param string $wm
     */
    public function setWm(string $wm) {
        $this->wm = $wm;
    }

    public function getPlayer() : ?Player {
        return Server::getInstance()->getPlayerExact($this->username);
    }

    public function getMana() : int {
        return $this->mana;
    }

    public function addMana(int $mana, bool $addToLifetime = true) {
        $this->setMana($this->mana + $mana);
        if ($addToLifetime) $this->playerExtraData->lifetime_income_mana += $mana;
        RecordDB::record(RecordDB::TOP_MANA, $this->getName(), $this->mana, "mana");
    }

    public function setMana(int $mana) {
        if ($mana < 0) $mana = 0;
        $this->mana = $mana;
    }

    public function removeMana(int $mana) : bool {
        if (Main::getInstance()->isTrusted($this->getName())) return true;
        if ($this->mana < $mana) return false;
        else {
            $this->setMana($this->mana - $mana);
            return true;
        }
    }

    public function addBlocksBroken(int $blocks = 1) {
        $this->blocks += $blocks;
        RecordDB::record(RecordDB::TOP_BLOCKS_BROKEN, $this->getName(), $this->blocks, "blocks");
    }

    public function getBlocksBroken() : int {
        return $this->blocks;
    }

    public function sendMessage(Player $sender, $message) : void {
        $sender->sendMessage(Values::FT_PREFIX . $message);
    }

    public function getLowerCaseName() : string {
        return strtolower($this->username);
    }

    public function getScoreboard() : ScoreboardAPI {
        return $this->scoreboard;
    }

    public function getGangLowerCase() : string {
        return strtolower($this->gang);
    }

    public function getKits() : array {
        return $this->kits;
    }

    public function getUnlockedPets() : array {
        return $this->pets;
    }

    public function getSelectedPet() : string {
        return $this->pet;
    }

    public function getPetName() : string {
        return $this->petname;
    }

    public function setPetName(string $petname = "") {
        $this->petname = $petname;
    }

    public function getKitCount(string $kit) {
        return $this->kits[strtolower($kit)];
    }

    public function getIsland() : string {
        return $this->island;
    }

    public function setIsland(string $island = "") {
        $this->island = $island;
    }

    public function getIslands() : array {
        return $this->islands;
    }

    public function setIslands(array $islands = []) {
        $this->islands = $islands;
    }

    public function getPetsCount() : int {
        return count($this->pets);
    }

    public function hasPet(string $pet) : bool {
        if (in_array($pet, $this->pets, true)) return true;
        else return false;
    }

    public function hasSetPet() : bool {
        if ($this->pet != "") return true;
        else return false;
    }

    public function addPet(string $pet) {
        array_push($this->pets, $pet);
    }

    public function getGoals() : array {
        return $this->goals;
    }

    public function setGoals(array $goals) {
        $this->goals = $goals;
    }

    public function setPet(string $pet = "") {
        $this->pet = $pet;
    }

    public function addKitCount(string $kit, int $count = 1) {
        $this->kits[$kit] += $count;
    }

    public function removeKitCount(string $kit, int $count = 1) : bool {
        if ($this->hasKit($kit)) {
            $this->kits[$kit] -= $count;
            return true;
        } else return false;
    }

    public function hasKit(string $kit) : bool {
        return $this->kits[$kit] > 0;
    }

    public function setKitCount(string $kit, int $count = 0) {
        $this->kits[$kit] = $count;
    }

    public function getIslandsCount() : int {
        return count($this->islands);
    }

    public function getBase() : array {
        return $this->base;
    }

    public function getSeconds() : int {
        return $this->seconds;
    }

    public function getGang() : string {
        return $this->gang;
    }

    public function setGang(string $gang = "") {
        $this->gang = $gang;
    }

    public function getSetGang() : string {
        if ($this->hasGang()) return '§7<§f#§e' . $this->gang . '§7>§r';
        else return '';
    }

    public function hasGang() : bool {
        if ($this->gang != "") return true;
        else return false;
    }

    public function getMobCoin() : int {
        return $this->mobcoin;
    }

    public function setMobCoin(int $coin) : void {
        $this->mobcoin = $coin;
        RecordDB::record(RecordDB::TOP_MOB_COIN, $this->getName(), $this->mobcoin, "coins");
    }

    public function getMoney() : float {
        return $this->money;
    }

    public function setMoney(int|float $money) : void {
        $this->money = $money;
        RecordDB::record(RecordDB::TOP_MONEY, $this->getName(), (int) $this->money, "$");
    }

    public function getKills() : int {
        return $this->kills;
    }

    public function getDeaths() : int {
        return $this->deaths;
    }

    public function getStreak() : int {
        return $this->streak;
    }

    public function getChips() : int {
        return $this->chips;
    }

    public function getWon() : int {
        return $this->won;
    }

    public function getBounty() : int {
        return $this->bounty;
    }

    public function getSetBounty() : string {
        return ($this->hasBounty()) ? " §o§6Bounty: " . number_format($this->bounty) . "§f$" . "§r" : "";
    }

    public function addBounty(int $bounty) : void {
        $this->setBounty($this->bounty + $bounty);
    }

    public function setBounty(int $bounty) : void {
        $this->bounty = $bounty;
    }

    public function hasBounty() : bool {
        return $this->bounty > 0;
    }

    public function getPointsNeeded(string $type) : int {
        return (int) $this->base["$type"]["level"] * 100;
    }

    public function getLevel(string $type) {
        return $this->base["$type"]["level"];
    }

    public function getPoints(string $type) {
        return $this->base["$type"]["exp"];
    }

    public function setPoints(int $points, string $type) {
        $level = $this->base["$type"]["level"];
        $exp = $this->base["$type"]["exp"];
        if (($total = $exp + $points) > ($needed = $level * 100)) {
            $left = $total - $needed;
            $this->base["$type"]["exp"] = $left;
            $new = $level + 1;
            $this->getPlayer()->sendTitle("§e§lLevel Up!", "§r§6{$type} {$level} -> {$new}\n§aGet money perks every x levels!");
            $this->getPlayer()->sendActionBarMessage("=> Use /mcstats or /mctop <=");
            $this->getPlayer()->sendActionBarMessage("=> Use /mcstats or /mctop <=");
            $this->base["$type"]["level"] = $new;
        } else {
            if ($exp + $points >= 0) {
                $this->base["$type"]["exp"] = $exp + $points;
            }
        }
    }

    public function getKDR() : string {
        if ($this->kills > 0 && $this->deaths > 0)
            return (string) round($this->kills / $this->deaths);
        else
            return 'N/A';
    }

    public function getTimePlayed(bool $spaced = true) : string {
        return Util::getTimePlayed($this->seconds, $spaced);
    }

    public function hasMana(int $mana) : bool {
        if (Main::getInstance()->isTrusted($this->getName())) return true;
        return $this->mana >= $mana;
    }

    public function hasMobCoin(int $coin) : bool {
        if (Main::getInstance()->isTrusted($this->getName())) return true;
        return $this->mobcoin >= $coin;
    }

    public function hasXPBank(int $xp) : bool {
        return $this->xpbank >= $xp;
    }

    public function hasMoney(int|float $money) : bool {
        if (Main::getInstance()->isTrusted($this->getName())) return true;
        return $this->money >= $money;
    }

    public function hasChips(int $chips) : bool {
        if (Main::getInstance()->isTrusted($this->getName())) return true;
        return $this->chips >= $chips;
    }

    /**
     * Returns true if user is owner or coowner of island
     * @return bool
     */
    public function isIslandSet() : bool {
        return $this->island !== "";
    }

    /**
     * Only returns true if owner of island
     * @return bool
     */
    public function hasIsland() : bool {
        if (!$this->isIslandSet()) return false;
        else {
            if (($island = Main::getInstance()->getIslandManager()->getOnlineIsland($this->island)) !== null) {
                if ($island->isOwner($this->getName())) return true;
            }
            return false;
        }
    }

    public function getIslandRankInStars() : string {
        if ($this->island === "") return '';
        else {
            if (($island = Main::getInstance()->getIslandManager()->getOnlineIsland($this->island)) !== null) {
                $name = $this->getName();
                if ($island->isOwner($name)) return '**';
                elseif ($island->isCoowner($name)) return '*';
                else return '';
            }
            return '';
        }
    }

    public function getIslandRank() : string {
        if ($this->isIslandSet()) {
            if (($island = Main::getInstance()->getIslandManager()->getOnlineIsland($this->island)) !== null) {
                return $island->getPlayerRank($this->getName());
            }
        }
        return '';
    }

    public function getSetTag() : string {
        if ($this->hasSetTag()) return '§l§b[' . Main::getInstance()->getTagManager()->getTagString($this->seltag) . '§b]§r';
        else return '';
    }

    public function getTags() : array {
        return $this->tags;
    }

    public function addTag(int $id) {
        if (!$this->hasTag($id)) array_push($this->tags, (string) $id);
    }

    public function setSelTag(int $tag = -1) {
        $this->seltag = $tag;
    }

    public function hasSetTag() : bool {
        return $this->seltag != -1;
    }

    public function getXP() : int {
        return $this->xp;
    }

    public function getXPBank() : int {
        return $this->xpbank;
    }

    public function setXP(int $xp) : void {
        $this->xp = $xp;
    }

    public function setXPBank(int $xp) : void {
        $this->xpbank = $xp;
        RecordDB::record(RecordDB::TOP_XP_BANK, $this->getName(), $this->xp, "XP");
    }

    public function hasTag(int $id) : bool {
        return in_array((string) $id, $this->tags, true);
    }

    public function addMobCoin(int $coin, bool $addToLifetime = true) : void {
        $this->setMobCoin($this->mobcoin + $coin);
        if ($addToLifetime) $this->playerExtraData->lifetime_income_mc += $coin;
    }

    /**
     * @param float $money
     * @param bool  $addToLifetime
     */
    public function addMoney(int|float $money, bool $addToLifetime = true) : void {
        $this->setMoney($this->money + $money);
        if ($addToLifetime) $this->playerExtraData->lifetime_income += $money;
    }

    public function addXPBank(int $xp) : void {
        $this->setXPBank($this->xpbank + $xp);
    }

    public function subtractXPBank(int $xp) : void {
        $this->setXPBank($this->xpbank - $xp);
    }

    public function hasIslands() : bool {
        return !empty($this->islands);
    }

    public function addIsland(string $island) {
        array_push($this->islands, strtolower($island));
    }

    public function addKill(int $kill = 1) {
        $this->kills = $this->kills + $kill;
        RecordDB::record(RecordDB::TOP_KILLS, $this->getName(), $this->kills, "kills");
    }

    public function addDeath(int $death = 1) {
        $this->deaths = $this->deaths + $death;
    }

    public function addSeconds(int $seconds = 1) : void {
        $this->seconds = $this->seconds + $seconds;
        RecordDB::record(RecordDB::TOP_TIME_PLAYED, $this->getName(), $this->seconds);
    }

    public function addWon(int $won = 1) {
        $this->won = $this->won + $won;
    }

    public function addChips(int $chips) {
        $this->chips = $this->chips + $chips;
    }

    public function addStreak(int $streak = 1) : void {
        $this->streak = $this->streak + $streak;
        RecordDB::record(RecordDB::TOP_KILL_STREAK, $this->getName(), $this->streak, "kills");
    }

    public function removeStreak() {
        $this->streak = 0;
    }

    public function removeMobCoin(int $coin) : bool {
        if (Main::getInstance()->isTrusted($this->getName())) return true;
        if ($this->mobcoin < $coin) return false;
        else {
            $this->setMobCoin($this->mobcoin - $coin);
            return true;
        }
    }

    public function removeMoney(int|float $money) : bool {
        if (Main::getInstance()->isTrusted($this->getName())) return true;
        if ($this->money < $money) return false;
        else {
            $this->setMoney($this->money - $money);
            return true;
        }
    }

    public function removeChips(int $chips) : bool {
        if (Main::getInstance()->isTrusted($this->getName())) return true;
        if ($this->chips < $chips) return false;
        else {
            $this->chips = $this->chips - $chips;
            return true;
        }
    }

    public function removeIsland(string $island) {
        if (($key = array_search(strtolower($island), $this->islands, true)) !== false) {
            unset($this->islands[$key]);
        }
    }

    public function teleportToHome(Player $player, string $home) : void {
        $arr = $this->homes[strtolower($home)];
        $pos = new Position($arr['x'], $arr['y'], $arr['z'], Server::getInstance()->getWorldManager()->getWorldByName($arr['world']));
        $pos->getWorld()->loadChunk($pos->getFloorX() >> 4, $pos->getFloorZ() >> 4);
        $player->teleport($pos, $arr['yaw'], $arr['pitch']);
    }

    public function getHomesString() : string {
        $home = array_keys($this->homes);
        return implode(", ", $home);
    }

    public function hasHome(string $home) : bool {
        return isset($this->homes[strtolower($home)]);
    }

    public function hasHomes() : bool {
        if ($this->homes === null) {
            return false;
        } elseif (count($this->homes) <= 0) {
            return false;
        } else return true;
    }

    public function updateHome(string $home, Location $pos) : void {
        $home = strtolower($home);
        $this->homes[$home]['x'] = $pos->getX();
        $this->homes[$home]['y'] = $pos->getY();
        $this->homes[$home]['z'] = $pos->getZ();
        $this->homes[$home]['world'] = $pos->getWorld()->getDisplayName();
        $this->homes[$home]['yaw'] = $pos->getYaw();
        $this->homes[$home]['pitch'] = $pos->getPitch();
    }

    public function removeHome(string $home) : void {
        unset($this->homes[strtolower($home)]);
    }

    public function getHomesCount() : int {
        return count($this->homes);
    }

    public function getSelTag() : int {
        return $this->seltag;
    }

    public function update() {
        if ($this->task->getHandler() === null) {
            return;
        }
        $this->task->getHandler()->cancel();
        Main::getInstance()->getDb()->updateUser($this->getName(), $this->money, $this->mobcoin, $this->xp, $this->xpbank, $this->mana, $this->blocks, $this->kills, $this->deaths, $this->streak, $this->chips, $this->won, $this->bounty, $this->base, $this->seconds, Main::getInstance()->getGoalManager()->implodeGoal($this->goals), $this->kits, $this->getPetsString(), $this->pet, $this->petname, $this->seltag, $this->getTagsString(), $this->wm, json_encode($this->homes), json_encode($this->preferences), json_encode($this->playerExtraData), json_encode($this->quests));
    }

    public function getName() : string {
        return $this->username;
    }

    public function getTagsString() : string {
        return implode(",", $this->tags);
    }

    public function getIslandsString() : string {
        return implode(",", $this->islands);
    }

    public function getPetsString() : string {
        return implode(",", $this->pets);
    }

    public static function jsonSerializeItem(Item $item) : string {
        $serializer = new LittleEndianNbtSerializer();
        return base64_encode($serializer->write(new TreeRoot($item->nbtSerialize())));
    }

    public static function jsonDeserializeItem(string $data) : Item {
        $serializer = new LittleEndianNbtSerializer();
        return Item::nbtDeserialize($serializer->read(base64_decode($data))->mustGetCompoundTag());
    }
}