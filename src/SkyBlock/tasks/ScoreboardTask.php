<?php

namespace SkyBlock\tasks;

use pocketmine\scheduler\Task;
use SkyBlock\Main;
use SkyBlock\ScoreboardAPI;
use SkyBlock\user\User;
use function count;
use function floor;
use function time;

class ScoreboardTask extends Task {

    /** @var Main */
    private Main $plugin;
    /** @var User */
    private User $user;
    /** @var ScoreboardAPI */
    private ScoreboardAPI $scoreboard;
    /** @var int */
    private int $i = 14;

    /**
     * @param Main $plugin
     * @param User $user
     */
    public function __construct(Main $plugin, User $user) {
        $this->plugin = $plugin;
        $this->user = $user;
        $this->setHandler($this->plugin->getScheduler()->scheduleRepeatingTask($this, 5 * 20));
    }

    public function onRun() : void {
        $user = $this->user;
        $user->addSeconds(5);
        $this->scoreboard = $user->getScoreboard();
        if (!$user->getPref()->scoreboard_enabled) {
            if ($this->scoreboard->hasScoreboard()) $this->scoreboard->remove();
        } else {
            if (!$this->scoreboard->hasScoreboard()) $this->scoreboard->new($this->plugin->sctitle);
            $this->updateLines();
            $this->checkLines();
            $this->i = 14;
        }
    }

    public function getPingColor(int $ping) : string {
        $map = ["b", "a", "e", "6", "c", "4", "8"];
        return $map[(int) (($ping / 100) - 1)] ?? $map[count($map) - 1];
    }

    private function updateLines() : void {
        $user = $this->user;
        if (($p = $user->getPlayer()) === null) return;

        $this->setLine("§3 ✪ §aIGN: §f" . $p->getName());
        $this->setLine("§3 ✪ §ePlayers: §f" . count($this->plugin->getServer()->getOnlinePlayers()) . "/" . $this->plugin->getServer()->getMaxPlayers());
        $this->setLine("§3 ✪ §6Money: §f" . Main::shortenNumber($user->getMoney()) . "$");
        $this->setLine("§3 ✪ §dMana: §f" . Main::shortenNumber($user->getMana()));
        $this->setLine("§3 ✪ §eMobCoin: §f" . Main::shortenNumber($user->getMobCoin()));
        $this->setLine("§3 ✪ §cXP: §f" . Main::shortenNumber($user->getXP()));
        $ping = $p->getNetworkSession()->getPing() ?? 100;
        $this->setLine("§3 ✪ §cPing: §" . $this->getPingColor($ping) . $ping . "ms");


        $item = $p->getInventory()->getItemInHand();
        $str = $item->getVanillaName();
        $world = $p->getWorld()->getDisplayName();
        if (($island = $this->plugin->getIslandManager()->getOnlineIslandByWorld($world)) === null) {
            $this->setLine("§d † Your Stats †");
            $this->setLine("§3 》 §aRank: §f" . $this->plugin->permsapi->getUserGroup($p->getName())->getName());
            $this->setLine("§3 》 §2Island: §f" . ($user->isIslandSet() ? $user->getIsland() : "---"));
            $this->setLine("§3 》 §eGang: §f" . ($user->hasGang() ? $user->getGang() : "---"));
            $this->setLine("§3 》 §bPlayed: §f" . $user->getTimePlayed(false));
            $this->setLine("§3 》 §9K-D: §f" . $user->getKills() . "-" . $user->getDeaths());
            $this->setLine("§3 》 §eStreak: §f" . $user->getStreak());
            $this->setLine("§b ➺ §6Do /pref");
        } else {
            $this->setLine("§d † Island Stats †");
            $this->setLine("§3 》 §bIsland: §f" . $island->getName());
            $this->setLine("§3 》 §6Owner: §f" . $island->getOwner());
            $this->setLine("§3 》 §5Bank: §f" . Main::shortenNumber($island->getMoney()) . "$");
            $this->setLine("§3 》 §eLevel: §f" . $island->getLevel());
            $this->setLine("§3 》 §2Points: §f" . $island->getPoints() . "/" . $island->getPointsNeeded());
            $this->setLine("§3 》 §aItem: §f" . $str);
            $this->setLine("§b ➺ §6Do /is help");
        }

    }

    /**
     * @param string $line
     */
    private function setLine(string $line = "") : void {
        $this->scoreboard->setLine($this->i--, $line);
    }

    /**
     * @return string
     */
    private function getTimeLeft() : string {
        $time = 300 - (time() - $this->plugin->warstart);
        $seconds = floor($time % 60);
        $minutes = null;
        if ($time >= 60) $minutes = floor(($time % 3600) / 60);
        return ($minutes !== null ? "$minutes mins " : "") . "$seconds s";
    }

    private function checkLines() : void {
        if ($this->i !== -1) {
            while ($this->i > -1) {
                $this->setLine();
                --$this->i;
            }
        }
    }

}
