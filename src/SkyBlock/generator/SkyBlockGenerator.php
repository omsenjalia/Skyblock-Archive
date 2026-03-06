<?php

namespace SkyBlock\generator;

use pocketmine\world\generator\Generator;

abstract class SkyBlockGenerator extends Generator {

    /** @var string */
    protected string $islandName;

    /**
     * Return island name
     * @return string
     */
    public function getIslandName() : string {
        return $this->islandName;
    }

    /**
     * Set island name
     *
     * @param string $name
     */
    public function setIslandName(string $name) : void {
        $this->islandName = $name;
    }

}