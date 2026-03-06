<?php


namespace SkyBlock\util;


use pocketmine\utils\TextFormat as TF;

interface Values {

    public const FT_PREFIX = TF::GREEN . TF::BOLD . "[" . TF::AQUA . "FT" . TF::GREEN . "]> " . TF::RESET . TF::GOLD;

    public const NETHER_RESET = 2; // days
    public const NETHER_SPAWN_RADIUS = 50;
    public const NETHER_SPAWN_PROTECT = 75;
    public const NETHER_BORDER = 2000;
    public const NETHER_INVINCIBILITY = 20; // secs

    public const MAX_USER_HOME_LIMIT = 5;

    /** @deprecated */
    public const FIX_LORE = 1;
    /** @deprecated */
    public const PLAYERS_KILLED_LORE = 2;
    /** @deprecated */
    public const BLOCKS_BROKEN_LORE = 3;
    /** @deprecated */
    public const LAST_LORE_VALUE = self::BLOCKS_BROKEN_LORE + 1;

    /** @deprecated */
    public const MAX_DEFAULT_FIX = 3;
    /** @deprecated */
    public const MAX_FIX = 10;
    /** @deprecated */
    public const PER_ITEM_FIX_COST = 15000;

    public const NETHER_WORLD = "nether";
    public const MINES_WORLD = "mine";
    public const LOBBY_WORLD = "lobby";
    public const PVP_WORLD = "PvP";
    public const SERVER_WORLDS = [self::PVP_WORLD, self::LOBBY_WORLD, self::NETHER_WORLD, self::MINES_WORLD];
    public const PVP_WORLDS = [self::PVP_WORLD];

    public const ALLOWED = " QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm1234567890.:,;-_|!/?§~@`<>#$%^&*()+={}[]'" . '"';

    public const FLY_TIME = 2 * 60 * 60;
    public const COMBAT_TIME = 10; // secs

}