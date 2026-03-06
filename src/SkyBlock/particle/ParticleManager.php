<?php

namespace SkyBlock\particle;

use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat as TF;
use SkyBlock\Main;
use SkyBlock\util\Util;

class ParticleManager {

    /** @var Main */
    private Main $pl;

    /** @var Particle[] $particles */
    private array $particles = [];

    /**
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->pl = $plugin;
    }

    /**
     * @param array|null $players
     * @param bool       $force
     * @param string     $world
     */
    public function sendParticle(array $players = null, bool $force = false, string $world = "") {
        foreach ($this->particles as $particle) {
            $particle->setInvisible(false);
            if ($players === null) {
                $players = $this->pl->getServer()->getOnlinePlayers();
            }
            foreach ($players as $player) {
                $player->getWorld()->loadChunk($particle->getParticle()->getPos()->getFloorX() >> 4, $particle->getParticle()->getPos()->getFloorZ() >> 4);
                if (!$force && $player->getWorld()->getDisplayName() == $particle->getWorld()) {
                    if ($particle->getUpdate() == "stats")
                        $this->updateStats($particle, $player->getName());
                    $player->getWorld()->addParticle($particle->getParticle()->getPos(), $particle->getParticle(), [$player]);
                }
                if ($force && $world == $particle->getWorld()) {
                    if ($particle->getUpdate() == "stats")
                        $this->updateStats($particle, $player->getName());
                    $player->getWorld()->addParticle($particle->getParticle()->getPos(), $particle->getParticle(), [$player]);
                }
                if ($force && $world != $particle->getWorld()) {
                    $this->removeParticle($players);
                }
                if (!$force && $player->getPosition()->getWorld()->getDisplayName() != $particle->getWorld()) {
                    $this->removeParticle($players);
                }
            }
        }
    }

    /**
     * @param Particle $particle
     * @param string   $name
     */
    public function updateStats(Particle $particle, string $name) {
        if (($user = $this->pl->getUserManager()->getOnlineUser($name)) !== null) {
            $text = "§aName: §r§f" . $name . "\n" . "§l§6Money: §r§f" . number_format($user->getMoney()) . "$\n" . "§l§eBlocks Broken: §r§f" . number_format($user->getBlocksBroken()) . "\n" . "§l§bMana: §r§f" . number_format($user->getMana()) . "\n" . "§l§9Kills: §r§f" . $user->getKills() . "\n" . "§l§cDeaths: §r§f" . $user->getDeaths() . "\n" . "§l§eStreak: §r§f" . $user->getStreak() . "\n" . "§l§3KDR: §r§f" . $user->getKDR() . "\n" . "§l§4Chips: §r§f" . $user->getChips() . "\n" . "§l§6Played: §r§f" . $user->getTimePlayed();
            $particle->setText($text);
        }
    }

    /**
     * @param array|null $players
     */
    public function removeParticle(array $players = null) {
        foreach ($this->particles as $particle) {
            $particle->setInvisible();
            if ($players === null) {
                $players = $this->pl->getServer()->getOnlinePlayers();
            }
            foreach ($players as $player) {
                $player->getWorld()->addParticle($particle->getParticle()->getPos(), $particle->getParticle(), [$player]);
            }
        }
    }

    public function setParticlesOnline() {
        $particle = $this->pl->particles;
        $this->particles["combat"] = new Particle($this->pl, new FloatingTextParticle(new Vector3($particle['combat']['x'], $particle['combat']['y'], $particle['combat']['z']), '', TF::YELLOW . TF::BOLD . "======== MCMMO-Combat ========"), $this->addText("mcmmo", "combat"), "combat", "lobby", "mcmmo");
        $this->particles["farming"] = new Particle($this->pl, new FloatingTextParticle(new Vector3($particle['farming']['x'], $particle['farming']['y'], $particle['farming']['z']), '', TF::YELLOW . TF::BOLD . "======== MCMMO-Farming ========"), $this->addText("mcmmo", "farming"), "farming", "lobby", "mcmmo");
        $this->particles["gambling"] = new Particle($this->pl, new FloatingTextParticle(new Vector3($particle['gambling']['x'], $particle['gambling']['y'], $particle['gambling']['z']), '', TF::YELLOW . TF::BOLD . "======== MCMMO-Gambling ========"), $this->addText("mcmmo", "gambling"), "gambling", "lobby", "mcmmo");
        $this->particles["mining"] = new Particle($this->pl, new FloatingTextParticle(new Vector3($particle['mining']['x'], $particle['mining']['y'], $particle['mining']['z']), '', TF::YELLOW . TF::BOLD . "======== MCMMO-Mining ========"), $this->addText("mcmmo", "mining"), "mining", "lobby", "mcmmo");
        $this->particles["topplayed"] = new Particle($this->pl, new FloatingTextParticle(new Vector3($particle['topplayed']['x'], $particle['topplayed']['y'], $particle['topplayed']['z']), '', TF::YELLOW . TF::BOLD . "======== Top Time Played ========"), $this->addText("topplayed"), "topplayed", "lobby", "topplayed");
        $this->particles["topislands"] = new Particle($this->pl, new FloatingTextParticle(new Vector3($particle['topislands']['x'], $particle['topislands']['y'], $particle['topislands']['z']), '', TF::YELLOW . TF::BOLD . "======== Top Islands ========"), $this->addText("topislands"), "topislands", "lobby", "topislands");
        $this->particles["topgangs"] = new Particle($this->pl, new FloatingTextParticle(new Vector3($particle['topgangs']['x'], $particle['topgangs']['y'], $particle['topgangs']['z']), '', TF::YELLOW . TF::BOLD . "======== Top Gangs ========"), $this->addText("topgangs"), "topgangs", "lobby", "topgangs");
        $this->particles["stats"] = new Particle($this->pl, new FloatingTextParticle(new Vector3($particle['stats']['x'], $particle['stats']['y'], $particle['stats']['z']), '', TF::YELLOW . TF::BOLD . "======== Your Stats ========"), "", "stats", "lobby", "stats");
        $this->particles["welcome"] = new Particle($this->pl, new FloatingTextParticle(new Vector3($particle['welcome']['x'], $particle['welcome']['y'], $particle['welcome']['z']), '', ''), $this->addText("welcome"), "welcome", "lobby", "welcome");
        $this->particles["topblocks"] = new Particle($this->pl, new FloatingTextParticle(new Vector3($particle['topblocks']['x'], $particle['topblocks']['y'], $particle['topblocks']['z']), '', TF::YELLOW . TF::BOLD . '======== Top Block Broken ========'), $this->addText("topblocks"), "topblocks", "lobby", "topblocks");
    }

    /**
     * @param string $type
     * @param string $name
     *
     * @return string
     */
    public function addText(string $type, string $name = "none") : string {
        $text = '';
        if ($type == "mcmmo") {
            $array = $this->pl->db->prepare("SELECT player, level FROM {$name} ORDER BY level DESC LIMIT 7;")->execute();
            while ($result = $array->fetchArray(SQLITE3_ASSOC)) {
                $level = $result['level'];
                $player = $result['player'];
                $text .= TF::RESET . TF::WHITE . "Name: " . TF::GREEN . $player . " §fLevel: " . TF::AQUA . $level . "\n";
            }
        }
        if ($type == "topblocks") {
            $array = $this->pl->db->prepare("SELECT player, blocks FROM player ORDER BY blocks DESC LIMIT 7;")->execute();
            while ($result = $array->fetchArray(SQLITE3_ASSOC)) {
                $blocks = $result['blocks'];
                $name = $result['player'];
                $text .= TF::RESET . TF::WHITE . "Name: " . TF::AQUA . $name . " §a| §fBlocks: " . TF::YELLOW . number_format($blocks) . "\n";
            }
        }
        if ($type == "topplayed") {
            $array = $this->pl->db->prepare("SELECT player, seconds FROM timings ORDER BY seconds DESC LIMIT 7;")->execute();
            while ($result = $array->fetchArray(SQLITE3_ASSOC)) {
                $seconds = $result['seconds'];
                $name = $result['player'];
                $text .= TF::RESET . TF::WHITE . "Name: " . TF::AQUA . $name . " §a| §fTime: " . TF::YELLOW . Util::getTimePlayed($seconds) . "\n";
            }
        }
        if ($type == "topislands") {
            $array = $this->pl->db->prepare("SELECT name, level FROM level ORDER BY level DESC LIMIT 7;")->execute();
            while ($result = $array->fetchArray(SQLITE3_ASSOC)) {
                $level = $result['level'];
                $name = $result['name'];
                $text .= TF::RESET . TF::WHITE . "Island: " . TF::AQUA . $name . " §fLevel: " . TF::YELLOW . $level . "\n";
            }
        }
        if ($type == "topgangs") {
            $array = $this->pl->db->prepare("SELECT gang, level FROM creator ORDER BY level DESC LIMIT 7;")->execute();
            while ($result = $array->fetchArray(SQLITE3_ASSOC)) {
                $level = $result['level'];
                $name = $result['gang'];
                $text .= TF::RESET . TF::WHITE . "Gang: " . TF::AQUA . $name . " §fLevel: " . TF::YELLOW . $level . "\n";
            }
        }
        if ($type == "welcome") {
            $text = TF::GRAY . TF::BOLD . "►" . str_repeat("-", 45) . "◄\n§r§aWelcome to the §l§bFallentech §dSkyBlock §6" . $this->pl->sbtype . " §eSeason §r§e" . $this->pl->season . "\n§aDo /helpme <page> to check all the commands on server\n§aDo /ranks for all the ranks info!\n" . TF::GRAY . TF::BOLD . "►" . str_repeat("-", 45) . "◄";
        }
        return $text;
    }

}