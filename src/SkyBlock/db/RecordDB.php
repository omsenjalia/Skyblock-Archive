<?php

namespace SkyBlock\db;

use pocketmine\utils\TextFormat as TF;
use poggit\libasynql\SqlThread;
use SkyBlock\Main;

class RecordDB {

    /** @var string */
    public const
        TOP_ISLAND = "islandlevel",
        TOP_GANG = "ganglevel",
        TOP_MONEY = "money",
        TOP_MANA = "mana",
        TOP_KILLS = "kills",
        TOP_BLOCKS_BROKEN = "blocksbroken",
        TOP_MOB_COIN = "mobcoin",
        TOP_XP_BANK = "xpbank",
        TOP_TIME_PLAYED = "timeplayed",
        TOP_KILL_STREAK = "killstreak";

    /** @var int */
    public const TRACK_MAX = 10;

    public static array $cache
        = [
            self::TOP_ISLAND        => [],
            self::TOP_GANG          => [],
            self::TOP_MONEY         => [],
            self::TOP_MANA          => [],
            self::TOP_KILLS         => [],
            self::TOP_BLOCKS_BROKEN => [],
            self::TOP_MOB_COIN      => [],
            self::TOP_XP_BANK       => [],
            self::TOP_TIME_PLAYED   => [],
            self::TOP_KILL_STREAK   => [],
        ];

    public static function init() : void {
        list($_, $server) = self::getServerAndSeason();
        foreach (array_keys(self::$cache) as $type) {
            $func = function(array $rows) use ($type) : void {
                self::$cache[$type] = $rows;
            };
            self::fetch($type, $server, $func);
        }
        Connector::get()->waitAll();
    }

    /**
     * @param string $type
     * @param string $name
     * @param int    $value
     *
     * @return bool
     */
    public static function qualify(string $type, string $name, int $value) : bool {
        $rows = self::$cache[$type];
        $rcount = count($rows);
        if (empty($rows) or $rcount < self::TRACK_MAX) return true;
        if (in_array($name, array_column($rows, 'name'), true)) return true;
        return $rows[$rcount - 1]['value'] < $value;
    }

    /**
     * @return array
     */
    public static function getServerAndSeason() : array {
        $main = Main::getInstance();
        return [$main->season, $main->server . "-" . TF::clean($main->sbtype)];
    }

    /**
     * @param string        $type
     * @param string        $server
     * @param callable|null $func
     */
    public static function fetch(string $type, string $server = "", callable $func = null) : void {
        if ($server !== "")
            Connector::get()->executeSelect("records.select", ["type" => $type, "server" => $server], $func);
        else
            Connector::get()->executeSelect("records.select_no_server", ["type" => $type], $func);
    }

    /**
     * @param string $type
     * @param string $name
     * @param int    $value
     * @param string $unit
     * @param array  $extra
     */
    public static function record(string $type, string $name, int $value, string $unit = "", array $extra = []) : void {
        if (!self::qualify($type, $name, $value)) return;
        list($season, $server) = self::getServerAndSeason();
        $newdata = ["pos" => 1, "type" => $type, "name" => $name, "value" => $value, "unit" => $unit, "extra" => json_encode($extra), "season" => $season, "server" => $server];

        $newrows[0] = $newdata;
        if (!empty(self::$cache[$type])) {
            $newrows = self::curate(self::$cache[$type], $newdata);
            self::sort($newrows);
        }
        self::$cache[$type] = $newrows;
    }

    public static function update() : void {
        $sql = "REPLACE INTO records(pos, type, name, value, server, season, unit, extra) VALUES ";
        $send = false;
        foreach (self::$cache as $row) {
            foreach ($row as $k => $d) {
                $pos = $k + 1;
                $sql .= "($pos, '{$d['type']}', '{$d['name']}', {$d['value']}, '{$d['server']}', {$d['season']}, '{$d['unit']}', '{$d['extra']}'), ";
                $send = true;
            }
        }
        if ($send) {
            $sql = substr($sql, 0, -2);
            Connector::get()->executeImplRaw([$sql], [[]], [SqlThread::MODE_INSERT], function(array $results) {
            },                               null
            );
        }
    }

    /**
     * @param array $rows
     * @param array $newdata
     *
     * @return array
     */
    public static function curate(array $rows, array $newdata) : array {
        $newrows = $rows;
        foreach ($rows as $k => $row) {
            if ($row['name'] === $newdata['name'] && $row['season'] === $newdata['season']) {
                $newrows[$k]['value'] = $newdata['value'];
                $newrows[$k]['extra'] = $newdata['extra'];
                return $newrows;
            }
        }
        $rows[] = $newdata;
        return $rows;
    }

    /**
     * @param array $rows
     */
    public static function sort(array &$rows) : void {
        usort($rows, function($a, $b) {
            return $b['value'] - $a['value'];
        }
        );
        $rows = array_slice($rows, 0, self::TRACK_MAX);
    }

    /**
     * @param string $dtype
     * @param string $old
     * @param string $new
     */
    public static function rename(string $dtype, string $old, string $new) : void {
        list($season, $_) = self::getServerAndSeason();

        if ($dtype === "island") {
            $type = self::TOP_ISLAND;
        } elseif ($dtype === "gang") {
            $type = self::TOP_GANG;
        } else return;

        $rows = self::$cache[$type];
        foreach ($rows as $k => $row) {
            if ($row['name'] === $old && $row['season'] === $season) {
                self::$cache[$type][$k]['name'] = $new;
            }
        }

    }

}