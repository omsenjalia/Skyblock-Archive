<?php

namespace SkyBlock\tasks;

use Exception;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use SkyBlock\Main;

class DropItemsTask extends Task {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onRun() : void {
        if ($this->getPlugin()->status == "enabled") {
            $level = $this->getPlugin()->getServer()->getWorldManager()->getWorldByName("PvP");
            Server::getInstance()->broadcastPopup("§b§l[FT]§r> §eDropParty items are dropping!");
            ++$this->plugin->secs;
            $item = $this->getBook();
            $item2 = $this->getKey();
            $item3 = $this->plugin->getTagManager()->getRandomTag();
            $item4 = $this->getCheque();
            $item5 = $this->getPlugin()->getFunctions()->opSword(3);
            $w = mt_rand(1, 7);
            $z = mt_rand(1, 7);
            $s = mt_rand(1, 7);
            try {
                $level->dropItem(new Vector3($this->plugin->dropparty['middle']['x'], $this->plugin->dropparty['middle']['y'], $this->plugin->dropparty['middle']['z']), LegacyStringToItemParser::getInstance()->parse((string) $this->getPlugin()->getRandomItem()));
                if ($w == 3) {
                    $level->dropItem(new Vector3($this->plugin->dropparty['c3']['x'], $this->plugin->dropparty['c3']['y'], $this->plugin->dropparty['c3']['z']), $item);
                }
                if ($z == 1) {
                    $level->dropItem(new Vector3($this->plugin->dropparty['c4']['x'], $this->plugin->dropparty['c4']['y'], $this->plugin->dropparty['c4']['z']), $item2);
                }
                if ($z == 2) {
                    $level->dropItem(new Vector3($this->plugin->dropparty['c4']['x'], $this->plugin->dropparty['c4']['y'], $this->plugin->dropparty['c4']['z']), $item4);
                }
                if ($s == 2) {
                    $level->dropItem($this->getCoords(), $item3);
                }
                if ($s == 3) {
                    $level->dropItem($this->getCoords(), $item5);
                }
                if ($s == 4) {
                    $level->dropItem($this->getCoords(), $this->getPlugin()->getTrollItem());
                }
                if ($w == 4) {
                    $level->dropItem($this->getCoords(), $this->getPlugin()->getTrollItem('lmao'));
                }
                if ($z == 7) {
                    $level->dropItem($this->getCoords(), $this->getPlugin()->getTrollItem('oof'));
                }
                if ($w == 1) {
                    $level->dropItem($this->getCoords(), $this->getPlugin()->getTrollItem('nab'));
                }
            } catch (Exception) {

            }
        }
        if ($this->getPlugin()->secs == 5) {
            if ($this->getPlugin()->status == "enabled") {
                $this->getPlugin()->getServer()->broadcastMessage("§f§l[DropParty]§b> §r§bDropParty has ended!");
                $this->getPlugin()->status = "ended";
                $this->getPlugin()->secs = 0;
                $this->getPlugin()->droppartyTimer = -10;
            }
        }
    }

    public function getPlugin() : Main {
        return $this->plugin;
    }

    public function getBook() : ?Item {
        $l = mt_rand(1, 1000);
        if ($l <= 500)
            return $this->getPlugin()->getCEBook();
        else if ($l <= 950)
            return $this->getPlugin()->getCEBook('rare');
        else if ($l <= 1000)
            return $this->getPlugin()->getCEBook('legendary');
        else return null;
    }

    public function getKey() : ?Item {
        $l = mt_rand(1, 1000);
        if ($l <= 400)
            return $this->getPlugin()->getCrateKeys('common');
        else if ($l <= 650)
            return $this->getPlugin()->getCrateKeys('rare');
        //        else if ($l <= 950)
        //            return $this->getPlugin()->getFireworkItem();
        else if ($l <= 1000)
            return $this->getPlugin()->getCrateKeys('legendary');
        else return null;
    }

    public function getCheque() : ?Item {
        $l = mt_rand(1, 1000);
        if ($l <= 500)
            return $this->getPlugin()->getCheque(2500);
        else if ($l <= 950)
            return $this->getPlugin()->getCheque(5000);
        else if ($l <= 1000)
            return $this->getPlugin()->getCheque(10000);
        else return null;
    }

    public function getCoords() : ?Vector3 {
        $l = mt_rand(1, 2);
        if ($l == 1)
            return new Vector3($this->plugin->dropparty['c1']['x'], $this->plugin->dropparty['c1']['y'], $this->plugin->dropparty['c1']['z']);
        else if ($l == 2)
            return new Vector3($this->plugin->dropparty['c2']['x'], $this->plugin->dropparty['c2']['y'], $this->plugin->dropparty['c2']['z']);
        else return null;
    }
}