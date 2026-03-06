<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;

abstract class HoveringPet extends IrasciblePet {

    /** @var float */
    public float $gravity = 0;
    /** @var float */
    protected float $flyHeight = 0.0;

    public function doPetUpdates(int $currentTick) : bool {
        if (!parent::doPetUpdates($currentTick)) {
            return false;
        }
        if ($this->isRidden()) {
            return false;
        }
        $this->follow($this->getPetOwner(), $this->xOffset, abs($this->yOffset) + 1.5, $this->zOffset);
        $this->updateMovement();
        return true;
    }

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

        if ((float) $y !== 0.0) {
            $this->motion->y = $this->getSpeed() * 0.25 * $y;
        }

        $this->location->yaw = rad2deg(atan2(-$x, $z));
        $this->location->pitch = rad2deg(-atan2($y, $xz_modulus));

        if ($this instanceof EnderDragonPet)
            $this->location->yaw += 180;

        $this->move($this->motion->x, $this->motion->y, $this->motion->z);
    }

    public function doRidingMovement(float $motionX, float $motionZ) : void {
        $rider = $this->getPetOwner();

        $this->location->pitch = $rider->location->pitch;
        $this->location->yaw = $this instanceof EnderDragonPet ? $rider->location->yaw + 360 : $rider->location->yaw;

        $speed_factor = 2 * $this->getSpeed();
        $rider_directionvec = $rider->getDirectionVector();
        $x = $rider_directionvec->x / $speed_factor;
        $z = $rider_directionvec->z / $speed_factor;
        $y = $rider_directionvec->y / $speed_factor;

        $finalMotionX = 0;
        $finalMotionZ = 0;

        switch ($motionZ) {
            case 1:
                $finalMotionX = $x;
                $finalMotionZ = $z;
                break;
            case 0:
                break;
            case -1:
                $finalMotionX = -$x;
                $finalMotionZ = -$z;
                break;
            default:
                $average = $x + $z / 2;
                $finalMotionX = $average / 1.414 * $motionZ;
                $finalMotionZ = $average / 1.414 * $motionX;
                break;
        }

        switch ($motionX) {
            case 1:
                $finalMotionX = $z;
                $finalMotionZ = -$x;
                break;
            case 0:
                break;
            case -1:
                $finalMotionX = -$z;
                $finalMotionZ = $x;
                break;
        }

        if (((float) $y) !== 0.0) {
            if ($y < 0) {
                $this->motion->y = $this->getSpeed() * 0.3 * $y;
            } elseif ($this->location->y - $this->getWorld()->getHighestBlockAt((int) $this->location->x, (int) $this->location->z) < $this->flyHeight) {
                $this->motion->y = $this->getSpeed() * 0.3 * $y;
            }
        }
        if (abs($y) < 0.2) {
            $this->motion->y = 0;
        }

        $this->move($finalMotionX, $this->motion->y, $finalMotionZ);
        $this->updateMovement();
    }


    /**
     * @param array $properties
     */
    public function useProperties(array $properties) : void {
        parent::useProperties($properties);
        $this->flyHeight = (float) $properties["Flying-Height"];
    }

    protected function initEntity(CompoundTag $nbt) : void {
        parent::initEntity($nbt);
        $this->followRangeSq = 8 + $this->getScale();
    }

}