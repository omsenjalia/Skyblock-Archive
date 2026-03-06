<?php
declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use function atan2;
use function rad2deg;
use function sqrt;

abstract class IrasciblePet extends BasePet {

    protected float $followRangeSq = 1.2;
    protected int $waitingTime = 0;
    private ?Living $target = null;

    public function follow(Entity $target, float $xOffset = 0.0, float $yOffset = 0.0, float $zOffset = 0.0) : void {
        if (!parent::canFollow()) return;

        $targetLoc = $target->getLocation();
        $currLoc = $this->getLocation();

        $x = $targetLoc->getX() + $xOffset - $currLoc->getX();
        $y = $targetLoc->getY() + $yOffset - $currLoc->getY();
        $z = $targetLoc->getZ() + $zOffset - $currLoc->getZ();

        $xz_sq = $x * $x + $z * $z;
        $xz_modulus = sqrt($xz_sq);

        if ($xz_sq < $this->followRangeSq) {
            $this->motion->x = 0;
            $this->motion->z = 0;
        } else {
            $speed_factor = $this->getSpeed() * 0.15;
            $this->motion->x = $speed_factor * ($x / $xz_modulus);
            $this->motion->z = $speed_factor * ($z / $xz_modulus);
        }
        $this->location->yaw = rad2deg(atan2(-$x, $z));
        $this->location->pitch = rad2deg(-atan2($y, $xz_modulus));

        $this->move($this->motion->x, $this->motion->y, $this->motion->z);
    }

    /**
     * Calms down the pet, making it stop chasing its target.
     */
    public function calmDown() : void {
        $this->target = null;
    }

    /**
     * Returns the current target of this pet.
     */
    public function getTarget() : ?Living {
        return $this->target;
    }
}