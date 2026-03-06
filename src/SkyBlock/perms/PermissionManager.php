<?php


namespace SkyBlock\perms;


class PermissionManager {

    /** @var Permission[] */
    public static array $permissions = [];

    public static function init() : void {
        self::registerPermission(new Permission(Permission::MINE, "Ability to mine ores", true));
        self::registerPermission(new Permission(Permission::FARM, "Ability to farm", true));
        self::registerPermission(new Permission(Permission::BUILD, "Ability to place blocks", true));
        self::registerPermission(new Permission(Permission::QUARTZ, "Ability to break quartz blocks"));
        self::registerPermission(new Permission(Permission::BREAK, "Ability to break blocks", true));
        self::registerPermission(new Permission(Permission::FREEZE, "Get frozen in /freeze", true));
        self::registerPermission(new Permission(Permission::CHEST, "Open chests"));
        self::registerPermission(new Permission(Permission::DELETE_CHEST, "Ability to delete chests using /deletechest"));
        self::registerPermission(new Permission(Permission::HOME, "Use /is home"));
        self::registerPermission(new Permission(Permission::MANAGER, "Use Island managing commands"));
        self::registerPermission(new Permission(Permission::CUSTOM_BLOCKS, "Ability to mine custom blocks(oregens etc.)"));
        self::registerPermission(new Permission(Permission::EXCL_CMDS, "Ability to use excl cmds(/sc, etc.)", true));
    }

    /**
     * @param Permission $permission
     */
    public static function registerPermission(Permission $permission) : void {
        self::$permissions[$permission->getName()] = $permission;
    }

    /**
     * @return array
     */
    public static function getDefaultPermissions() : array {
        return array_map(function(Permission $permission) : array {
            return $permission->getHolders();
        }, self::getPermissions()
        );
    }

    /**
     * @return Permission[]
     */
    public static function getPermissions() : array {
        return self::$permissions;
    }

    /**
     * @param string $name
     *
     * @return Permission|null
     */
    public static function getPermission(string $name) : ?Permission {
        return self::$permissions[$name] ?? null;
    }

}