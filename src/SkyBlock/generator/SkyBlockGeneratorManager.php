<?php

namespace SkyBlock\generator;

use InvalidArgumentException;
use pocketmine\world\generator\GeneratorManager;
use SkyBlock\generator\generators\BasicIsland;

class SkyBlockGeneratorManager {

    /** @var string[] */
    private array $generators = [];

    public function __construct() {
        $this->registerGenerator(BasicIsland::class, "basic", "Basic Island");
    }

    /**
     * Register a generator
     *
     * @param        $generator
     * @param string $name
     * @param string $islandName
     */
    public function registerGenerator($generator, string $name, string $islandName) {
        try {
            GeneratorManager::getInstance()->addGenerator($generator, $name, fn() => null, true);
            $this->generators[$name] = $islandName;
        } catch (InvalidArgumentException) {
        }
    }

    /**
     * @return string[]
     */
    public function getGenerators() : array {
        return $this->generators;
    }

    public function getGeneratorIslandName($name) : string {
        return $this->isGenerator($name) ? $this->generators[$name] : "";
    }

    /**
     * Return if a generator exists
     *
     * @param $name
     *
     * @return bool
     */
    public function isGenerator($name) : bool {
        return isset($this->generators[$name]);
    }

}