<?php


namespace SkyBlock\perms;

use JsonSerializable;

class Permission implements JsonSerializable {

    public const MINE = "mine";
    public const FARM = "farm";
    public const BUILD = "build";
    public const BREAK = "break";
    public const QUARTZ = "quartz";
    public const FREEZE = "freeze";
    public const CHEST = "chest";
    public const HOME = "home";
    public const MANAGER = "manager";
    public const CUSTOM_BLOCKS = "custom blocks";
    public const EXCL_CMDS = "exclusive cmds";
    public const DELETE_CHEST = "deletechest";

    /** @var string */
    private string $name;
    /** @var string */
    private string $desc;
    /** @var bool */
    private bool $default;
    /** @var array */
    private array $holders;

    public function __construct(string $name, string $desc, bool $default = false, array $holders = []) {
        $this->name = $name;
        $this->desc = $desc;
        $this->default = $default;
        $this->holders = $holders;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getHolders() : array {
        return $this->holders;
    }

    public function setHolders(array $holders) : void {
        $this->holders = $holders;
    }

    /**
     * @return bool
     */
    public function isDefault() : bool {
        return $this->default;
    }

    /**
     * @return string
     */
    public function getDesc() : string {
        return $this->desc;
    }

    public function isHolder(string $holder) : bool {
        return in_array($holder, $this->holders, true);
    }

    public function addHolder(string $holder) : void {
        if (!in_array($holder, $this->holders, true)) $this->holders[] = $holder;
    }

    public function removeHolder(string $holder) : void {
        unset($this->holders[array_search($holder, $this->holders, true)]);
    }

    public function jsonSerialize() : array {
        return $this->holders;
    }

}