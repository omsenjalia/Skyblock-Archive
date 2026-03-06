<?php


namespace SkyBlock\util;


use pocketmine\item\Item;

final class Lore {
    public const FIX_LORE = 1;
    public const PLAYERS_KILLED_LORE = 2;
    public const BLOCKS_BROKEN_LORE = 3;
    public const LAST_LORE_VALUE = self::BLOCKS_BROKEN_LORE + 1;


    public const BLOCKS_BROKEN_STR = "§r§7Blocks Broken: ";
    public const FIX_STR = "§r§7Fixed: ";
    public const KILL_STR = "§r§7Players Killed: ";

    /**
     * @param array  $lore
     * @param int    $info
     * @param string $offset
     *
     * @return string|null
     */
    public static function getLoreInfo(array $lore, int $info, string $offset) : ?string {
        if (isset($lore[$info])) {
            $str = substr($lore[$info], strlen($offset));
            if ($str === "" or !$str) return null;
            return str_replace(",", "", $str);
        }
        return null;
    }

    /**
     * @param Item   $item
     * @param int    $key
     * @param string $data
     *
     * @return Item
     */
    public static function setLoreInfo(Item $item, int $key, string $data) : Item {
        $lore = $item->getLore();
        for ($i = 0; $i <= $key; $i++) {
            if (!isset($lore[$i])) $lore[$i] = "";
        }
        $lore[$key] = $data;
        return $item->setLore(array_splice($lore, 0, Values::LAST_LORE_VALUE));
    }

}