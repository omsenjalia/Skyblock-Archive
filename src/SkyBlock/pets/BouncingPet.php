<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\nbt\tag\CompoundTag;

abstract class BouncingPet extends IrasciblePet {
    /** @var int */
    protected int $jumpTicks = 0;
    /** @var float */
    protected float $jumpHeight = 0.08;

    public function doPetUpdates(int $currentTick) : bool {
        if (!parent::doPetUpdates($currentTick)) {
            return false;
        }

        if ($this->jumpTicks > 0) {
            --$this->jumpTicks;
        }

        if (!$this->isOnGround()) {
            if ($this->motion->y > -$this->gravity * 2) {
                $this->motion->y = -$this->gravity * 2;
            } else {
                $this->motion->y -= $this->gravity;
            }
        } else {
            $this->motion->y -= $this->gravity;
        }

        if ($this->isRidden()) {
            return false;
        }

        $this->follow($this->getPetOwner(), $this->xOffset, 0.0, $this->zOffset);
        $this->updateMovement();
        return true;
    }

    public function doRidingMovement(float $motionX, float $motionZ) : void {
        $rider = $this->getPetOwner();

        $this->location->pitch = $rider->location->pitch;
        $this->location->yaw = $rider->location->yaw;

        $speed_factor = 2 * $this->getSpeed();
        $direction_plane = $this->getDirectionPlane();
        $x = $direction_plane->x / $speed_factor;
        $z = $direction_plane->y / $speed_factor;

        if ($this->jumpTicks > 0) {
            $this->jumpTicks--;
        }

        if (!$this->isOnGround()) {
            if ($this->motion->y > -$this->gravity * 2) {
                $this->motion->y = -$this->gravity * 2;
            } else {
                $this->motion->y -= $this->gravity;
            }
        } else {
            $this->motion->y -= $this->gravity;
        }

        $finalMotionX = 0;
        $finalMotionZ = 0;

        switch ($motionZ) {
            case 1:
                $finalMotionX = $x;
                $finalMotionZ = $z;
                if ($this->isOnGround()) {
                    $this->jump();
                }
                break;
            case 0:
                if ($this->isOnGround()) {
                    $this->jump();
                }
                break;
            case -1:
                $finalMotionX = -$x;
                $finalMotionZ = -$z;
                if ($this->isOnGround()) {
                    $this->jump();
                }
                break;
            default:
                $average = $x + $z / 2;
                $finalMotionX = $average / 1.414 * $motionZ;
                $finalMotionZ = $average / 1.414 * $motionX;
                if ($this->isOnGround()) {
                    $this->jump();
                }
                break;
        }

        switch ($motionX) {
            case 1:
                $finalMotionX = $z;
                $finalMotionZ = -$x;
                if ($this->isOnGround()) {
                    $this->jump();
                }
                break;
            case 0:
                if ($this->isOnGround()) {
                    $this->jump();
                }
                break;
            case -1:
                $finalMotionX = -$z;
                $finalMotionZ = $x;
                if ($this->isOnGround()) {
                    $this->jump();
                }
                break;
        }

        $this->move($finalMotionX, $this->motion->y, $finalMotionZ);
        $this->updateMovement();
    }

    public function jump() : void {
        parent::jump();
        $this->jumpTicks = 10;
    }

    /**
     * @param array $properties
     */
    public function useProperties(array $properties) : void {
        parent::useProperties($properties);
        $this->jumpHeight = (float) $properties["Jumping-Height"];
    }

    protected function initEntity(CompoundTag $nbt) : void {
        parent::initEntity($nbt);
        $this->followRangeSq = 9 + $this->getScale();
        $this->jumpVelocity = $this->jumpHeight * 12 * $this->getScale();
    }

}