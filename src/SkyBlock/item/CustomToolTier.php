<?php


namespace SkyBlock\item;


final class CustomToolTier {
    private int $harvestLevel;
    private int $maxDurability;
    private int $baseAttackPoints;
    private int $baseEfficiency;

    public function __construct($harvestLevel, $maxDurability, $baseAttackPoints, $baseEfficiency) {
        $this->harvestLevel = $harvestLevel;
        $this->maxDurability = $maxDurability;
        $this->baseEfficiency = $baseEfficiency;
        $this->baseAttackPoints = $baseAttackPoints;
    }

    public function getHarvestLevel() : int {
        return $this->harvestLevel;
    }

    public function getMaxDurability() : int {
        return $this->maxDurability;
    }

    public function getBaseAttackPoints() : int {
        return $this->baseAttackPoints;
    }

    public function getBaseEfficiency() : int {
        return $this->baseEfficiency;
    }
}
