<?php

declare(strict_types=1);

namespace SkyBlock;

class PetProperties {

    private Main $loader;
    private array $properties;

    public function __construct(Main $loader) {
        $this->loader = $loader;
        $loader->saveResource("pet_properties.yml", true);
        $this->collectProperties();
    }

    public function collectProperties() : void {
        $data = yaml_parse_file($this->getLoader()->getDataFolder() . "pet_properties.yml");
        $this->properties = $data;
    }

    /**
     * @return Main
     */
    public function getLoader() : Main {
        return $this->loader;
    }

    /**
     * @param string $entityType
     *
     * @return array
     */
    public function getPropertiesFor(string $entityType) : array {
        if (!$this->propertiesExistFor($entityType)) {
            return [];
        }
        return $this->properties[$entityType];
    }

    /**
     * @param string $entityType
     *
     * @return bool
     */
    public function propertiesExistFor(string $entityType) : bool {
        if (isset($this->properties[$entityType])) {
            return true;
        }
        return false;
    }
}