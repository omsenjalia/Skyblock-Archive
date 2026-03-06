<?php

declare(strict_types=1);

namespace SkyBlock\tiles;

use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\tile\Nameable;
use pocketmine\block\tile\NameableTrait;
use pocketmine\block\tile\Spawnable;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\Position;
use SkyBlock\spawner\SpawnerFactory;

class MobSpawner extends Spawnable implements Nameable {

    use NameableTrait;

    public const
        TAG_ENTITY_ID = "EntityId",
        TAG_LEVEL = "Level",
        TAG_SPAWN_COUNT = "SpawnCount",
        TAG_SPAWN_RANGE = "SpawnRange",
        TAG_MIN_SPAWN_DELAY = "MinSpawnDelay",
        TAG_MAX_SPAWN_DELAY = "MaxSpawnDelay",
        TAG_DELAY = "Delay",
        MAX_LEVEL = 4;
    /** @var ?string */
    public ?string $entityId = null;
    /** @var int */
    public int $level1 = 0;
    /** @var int */
    protected int $spawnCount = 1;
    /** @var int */
    protected int $spawnRange = 4;
    /** @var int */
    protected int $minSpawnDelay = 200, $maxSpawnDelay = 800;
    /** @var int */
    protected int $delay = 200;
    /** @var Block|null */
    protected ?Block $target = null;

    /**
     * @return string
     */
    public function getName() : string {
        return "Monster Spawner";
    }

    public function getDefaultName() : string {
        return "Monster Spawner";
    }

    /**
     * @return bool
     */
    public function onUpdate() : bool {
        if ($this->entityId === null) $this->close();
        if ($this->closed === true) return false;
        if ($this->delay > 0) {
            --$this->delay;
            return true;
        }
        if ($this->target === null) {
            for ($i = 0; $i < $this->getLevelSpawnCount(); $i++) {
                $pos = $this->getPosition()->add(mt_rand() / mt_getrandmax() * $this->getSpawnRange(), mt_rand(-1, 1), mt_rand() / mt_getrandmax() * $this->getSpawnRange());
                $newtarget = $this->getPosition()->getWorld()->getBlock($pos);
                if ($newtarget->getTypeId() === BlockTypeIds::AIR) {
                    $this->target = $newtarget;
                    break;
                }
            }
        }
        if ($this->target === null or $this->getPosition()->getWorld()->getBlock($this->target->getPosition())->getTypeId() !== BlockTypeIds::AIR) {
            $this->target = null;
            $this->resetDelay();
            return true;
        }
        for ($i = 0; $i < $this->getLevelSpawnCount(); $i++) {
            $sdata = SpawnerFactory::SPAWNER_CLASSES[$this->getEntityId()] ?? null;
            if ($sdata === -1) break;
            $pos = $this->target->getPosition();
            $entity = new $sdata[0](Location::fromObject($pos, null), new CompoundTag());
            $entity->teleport(new Position($entity->getPosition()->getX() + 0.5, $entity->getPosition()->getY(), $entity->getPosition()->getZ() + 0.5, $entity->getWorld()));
            $entity->spawnToAll();
        }
        $this->resetDelay();
        return true;
    }

    private function resetDelay() : void {
        $this->delay = $this->getDelayByLevel($this->level1);
    }

    /**
     * @return int
     */
    public function getLevelSpawnCount() : int {
        return ($this->spawnCount = $this->level1);
    }

    /**
     * @return int
     */
    public function getDelayInSeconds() : int {
        return (int) ($this->getDelayByLevel($this->level1) / 20);
    }

    /**
     * @param int $level
     *
     * @return int
     */
    public function getDelayByLevel(int $level) : int {
        if ($level == 1) return 500;
        elseif ($level == 2) return 400;
        elseif ($level == 3) return 300;
        elseif ($level == 4) return 200;
        else return 500;
    }

    /**
     * @param CompoundTag $nbt
     */
    public function addAdditionalSpawnData(CompoundTag $nbt) : void // runs second
    {
        $this->applyBaseNBT($nbt);
    }

    /**
     * @param CompoundTag $nbt
     */
    private function applyBaseNBT(CompoundTag $nbt) : void {
        $nbt->setString(self::TAG_ENTITY_ID, $this->getEntityId());
        $nbt->setInt(self::TAG_LEVEL, $this->level1);
        $nbt->setInt(self::TAG_SPAWN_COUNT, $this->getSpawnCount());
        $nbt->setInt(self::TAG_SPAWN_RANGE, $this->getSpawnRange());
        $nbt->setInt(self::TAG_MIN_SPAWN_DELAY, $this->getMinSpawnDelay());
        $nbt->setInt(self::TAG_MAX_SPAWN_DELAY, $this->getMaxSpawnDelay());
    }

    /**
     * @return string
     */
    public function getEntityId() : string {
        return $this->entityId;
    }

    /**
     * @param string $entityId
     */
    public function setEntityId(string $entityId) : void {
        $this->entityId = $entityId;
        $this->setDirty();
        //$this->onChanged(); // this needs to be sent to the client so the entity animation updates too
        //$this->scheduleUpdate();
    }

    /**
     * @return int
     */
    public function getSpawnCount() : int {
        return $this->spawnCount;
    }

    /**
     * @param int $spawnCount
     */
    public function setSpawnCount(int $spawnCount) : void {
        $this->spawnCount = $spawnCount;
    }

    /**
     * @return int
     */
    public function getSpawnRange() : int {
        return $this->spawnRange;
    }

    /**
     * @param int $spawnRange
     */
    public function setSpawnRange(int $spawnRange) : void {
        $this->spawnRange = $spawnRange;
    }

    /**
     * @return int
     */
    public function getMinSpawnDelay() : int {
        return $this->minSpawnDelay;
    }

    /**
     * @param int $minSpawnDelay
     */
    public function setMinSpawnDelay(int $minSpawnDelay) : void {
        $this->minSpawnDelay = $minSpawnDelay;
    }

    /**
     * @return int
     */
    public function getMaxSpawnDelay() : int {
        return $this->maxSpawnDelay;
    }

    /**
     * @param int $maxSpawnDelay
     */
    public function setMaxSpawnDelay(int $maxSpawnDelay) : void {
        $this->maxSpawnDelay = $maxSpawnDelay;
    }

    /**
     * @return int
     */
    public function getDelay() : int {
        return $this->delay;
    }

    /**
     * @param int $delay
     */
    public function setDelay(int $delay) : void {
        $this->delay = $delay;
    }

    /**
     * @param CompoundTag $nbt
     */
    public function readSaveData(CompoundTag $nbt) : void // runs first
    {
        if ($nbt->getString(self::TAG_ENTITY_ID, null) === null) $this->close();

        $this->setEntityId($nbt->getString(self::TAG_ENTITY_ID, $this->entityId));
        $this->level1 = $nbt->getInt(self::TAG_LEVEL, $this->level1);
        $this->spawnCount = $this->level1;
        $this->spawnRange = $nbt->getInt(self::TAG_SPAWN_RANGE, $this->spawnRange);
        $this->minSpawnDelay = $nbt->getInt(self::TAG_MIN_SPAWN_DELAY, $this->minSpawnDelay);
        $this->maxSpawnDelay = $nbt->getInt(self::TAG_MAX_SPAWN_DELAY, $this->maxSpawnDelay);
        $this->delay = $nbt->getInt(self::TAG_DELAY, $this->delay);
        //$this->scheduleUpdate();
    }

    /**
     * @param CompoundTag $nbt
     */
    protected function writeSaveData(CompoundTag $nbt) : void // on unload
    {
        $this->applyBaseNBT($nbt);
    }

}