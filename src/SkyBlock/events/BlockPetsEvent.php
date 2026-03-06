<?php

declare(strict_types=1);

namespace SkyBlock\events;

use pocketmine\event\plugin\PluginEvent;
use SkyBlock\Main;

abstract class BlockPetsEvent extends PluginEvent {
    /** @var Main */
    private Main $loader;

    public function __construct(Main $loader) {
        parent::__construct($loader);
        $this->loader = $loader;
    }

    /**
     * @return Main
     */
    public function getLoader() : Main {
        return $this->loader;
    }
}