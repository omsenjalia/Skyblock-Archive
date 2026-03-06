<?php

declare(strict_types=1);

namespace SkyBlock;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\world\Position;
use SkyBlock\tasks\GameTimerTask;
use SkyBlock\tasks\PreGameTimerTask;
use SkyBlock\util\Values;

class Arena {

    public Main $plugin;
    public array $players = [];
    public int $highest = 0;
    private bool $running = false;
    private array $spawns = [];
    private Position $p1;
    private Position $p2;
    private ?Task $timer = null;
    private ?Player $winner = null;

    public function __construct(Main $main, $spawns, $capture) {
        $this->plugin = $main;
        foreach ($spawns as $spawn) {
            $l = explode(":", $spawn);
            $l = new Position((float) $l[0], (float) $l[1], (float) $l[2], $main->getServer()->getWorldManager()->getWorldByName($l[3]));
            $this->spawns[] = $l;
        }
        $l = explode(":", $capture["c1"]);
        $this->p1 = new Position((float) $l[0], (float) $l[1], (float) $l[2], $main->getServer()->getWorldManager()->getWorldByName($l[3]));
        $l = explode(":", $capture["c2"]);
        $this->p2 = new Position((float) $l[0], (float) $l[1], (float) $l[2], $main->getServer()->getWorldManager()->getWorldByName($l[3]));
    }

    public function preStart() : void {
        $task = new PreGameTimerTask($this);
        $handler = $this->plugin->getScheduler()->scheduleRepeatingTask($task, 20);
        $task->setHandler($handler);
        $this->timer = $task;
        $this->running = true;
    }

    public function startGame() : void {
        $task = new GameTimerTask($this);
        $handler = $this->plugin->getScheduler()->scheduleRepeatingTask($task, 20);
        $task->setHandler($handler);
        $this->timer = $task;
    }

    public function addPlayer(Player $player) : void {
        $this->players[$player->getName()] = 90;
        $this->sendRandomSpot($player);
    }

    public function sendRandomSpot(Player $player) : void {
        $player->teleport($this->spawns[array_rand($this->spawns)]);
    }

    public function isInArena(string $player) : bool {
        if (isset($this->players[$player])) return true;
        else return false;
    }

    public function resetCapture(Player $player) : void {
        if (isset($this->players[$player->getName()])) {
            $this->players[$player->getName()] = 90;
        }
    }

    public function isRunning() : bool {
        return $this->running;
    }

    public function checkPlayers() : void {
        $pcount = count($this->players);
        if ($pcount > $this->highest) $this->highest = $pcount;
        foreach ($this->players as $player => $time) {
            if (($oplayer = $this->plugin->getServer()->getPlayerExact($player)) instanceof Player) {
                if ($this->inCapture($oplayer)) {
                    $time = --$this->players[$player];
                    $this->sendProgress($oplayer, $time);
                    if ($time < 1) {
                        $this->won($oplayer);
                    }
                }
            } else {
                unset($this->players[$player]);
            }
        }
    }

    public function inCapture(Player $player) : bool {
        if ($player->getWorld()->getDisplayName() !== Values::PVP_WORLD) return false;
        $l = $player->getPosition();
        $x = $l->getX();
        $z = $l->getZ();
        $y = $l->getY();
        $p1 = $this->p1;
        $p2 = $this->p2;
        $minx = min($p1->getX(), $p2->getX());
        $maxx = max($p1->getX(), $p2->getX());
        $minz = min($p1->getZ(), $p2->getZ());
        $maxz = max($p1->getZ(), $p2->getZ());
        $miny = min($p1->getY(), $p2->getY());
        $maxy = max($p1->getY(), $p2->getY());

        return ($minx <= $x && $x <= $maxx && $minz <= $z && $z <= $maxz && $miny <= $y && $y <= $maxy);
    }

    public function sendProgress(Player $player, $time) : void {
        $max = 90;
        $time = 90 - $time;
        $per = (int) ((($time / $max) * 100));
        $percent = $per . '%';
        $highest = min($this->players);
        $player->sendTip("§k§b|| §r§l§aCapturing§r§f: §e" . $percent . " §k§b||§r\n§eCurrently Highest §f- §a" . array_search($highest, $this->players, true));
    }

    public function won(Player $player, bool $forcewin = false) : void {
        $this->winner = $player;
        $this->plugin->noDrop[$this->winner->getName()] = true;
        $count = $this->plugin->kothnumber;
        if (!$forcewin) $winmsg = "by capturing the Hill against " . $this->highest . " players!";
        else $winmsg = "by time up against " . $this->highest . " players!";
        $this->plugin->getServer()->broadcastMessage("§l§e> §a" . $player->getName() . " §r§ehas won the §bKOTH §eevent §7#§f{$count} §e$winmsg");
        $msg = "`" . $player->getName() . "` won the KOTH event #`" . $count . "` " . $winmsg . "\n";
        $this->plugin->sendDiscordMessage("KOTH Event Ended!", $msg, 1, "", "F81616");
        $this->plugin->giveRewards($player);
        $this->endGame();
    }

    public function timeOver() : void {
        if (count($this->players) > 0) {
            $highest = min($this->players);
            $max = array_search($highest, $this->players, true);
            $p = Server::getInstance()->getPlayerExact($max);
            if ($p instanceof Player) {
                $this->won($p, true);
                return;
            }
        }
        $this->endGame();
        if (isset(Main::getInstance()->noDrop[$this->winner->getName()])) {
            unset(Main::getInstance()->noDrop[$this->winner->getName()]);
        }
    }

    public function endGame() : void {
        $this->running = false;
        foreach ($this->players as $player => $time) {
            $p = Server::getInstance()->getPlayerExact($player);
            if ($p instanceof Player) {
                Main::getInstance()->teleportToSpawn($p);
                $max = 90;
                $time = 90 - $time;
                $per = (int) (($time / $max) * 100);
                $percent = $per . "%";
                $p->sendMessage("§a§l> §r§bKOTH §eEvent ended! You were at " . $percent . " percent!");
            }
            unset($this->players[$player]);
        }
        $this->resetGame();
    }

    public function resetGame() : void {
        $this->players = [];
        $timer = $this->timer;
        if ($timer instanceof Task && !$timer->getHandler()->isCancelled()) {
            $timer->getHandler()->cancel();
        }
        $this->timer = null;
    }

    public function removePlayer(Player $player) : void {
        unset($this->players[$player->getName()]);
    }

    public function sendTip($msg) : void {
        foreach ($this->players as $player => $time) {
            $p = Server::getInstance()->getPlayerExact($player);
            if ($p instanceof Player) {
                $p->sendTip($msg);
            }
        }
    }
}