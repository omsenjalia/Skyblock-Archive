<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use InvalidArgumentException;
use pocketmine\entity\Attribute;
use pocketmine\entity\AttributeFactory;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\entity\Attribute as NetworkAttribute;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\spawner\Creature;
use SkyBlock\spawner\Rideable;
use SkyBlock\util\Values;

abstract class BasePet extends Creature implements Rideable {

    public const ENTITY_ID = "";

    const STATE_STANDING = 0;
    const STATE_SITTING = 1;
    const LINK_RIDING = 0;
    const LINK_RIDER = 1;

    /** @var string */
    public string $name = "";
    /** @var float */
    protected float $scale = 1.0;
    /** @var float */
    public float $width = 0.0, $height = 0.0;
    public float $follow_range_sq = 0.0;
    /** @var string */
    protected string $petName = "Name";
    /** @var Player|null */
    protected ?Player $rider = null;
    /** @var Vector3 */
    protected Vector3 $rider_seatpos;
    /** @var bool */
    protected bool $riding = false, $saddled = false;
    /** @var Vector3 */
    protected Vector3 $seatpos;
    /** @var bool */
    protected bool $visibility = true;
    /** @var float */
    protected float $speed = 1.0;
    /** @var bool */
    protected bool $canBeRidden = true;
    /** @var float */
    protected float $xOffset = 0.0;
    /** @var float */
    protected float $yOffset = 0.0;
    /** @var float */
    protected float $zOffset = 0.0;
    protected string $type = "Normal";
    /** @var EntityLink[] */
    private array $links = [];
    private ?Player $petOwner;
    /** @var bool */
    private bool $dormant = false;
    /** @var bool */
    private bool $shouldIgnoreEvent = false;
    /** @var int */
    private int $positionSeekTick = 60;

    /**
     * @param Location         $location
     * @param CompoundTag|null $nbt
     */
    final public function __construct(Location $location, ?CompoundTag $nbt = null) {
        $this->petOwner = Server::getInstance()->getPlayerExact($nbt->getString("petOwner"));
        if ($this->petOwner === null) {
            $this->close();
            return;
        }
        parent::__construct($location, $nbt);
    }

    /**
     * Returns the BlockPets Loader. For internal usage.
     * @return Main|null
     */
    public function getLoader() : ?Main {
        return Main::getInstance();
    }

    public function getType() : string {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function getVisibility() : bool {
        return $this->visibility;
    }

    public function canFollow() : bool {
        return !isset($this->getLoader()->dontFollow[$this->getPetOwnerName()]);
    }

    /**
     * @param bool $value
     *
     * @internal
     */
    public function updateVisibility(bool $value) : void {
        $this->visibility = $value;
        $this->setImmobile(!$value);
        if ($value) {
            $this->spawnToAll();
        } else {
            $this->despawnFromAll();
        }
    }

    public function setImmobile(bool $value = true) : void {
        if (!$this->visibility && $value) {
            return;
        }
        parent::setNoClientPredictions($value);
    }

    public function spawnTo(Player $player) : void {
        if (!$this->visibility) {
            return;
        }
        parent::spawnTo($player);
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source) : void {
        if (!$this->visibility or !$source->getEntity() instanceof BasePet) {
            return;
        }
        if ($source instanceof EntityDamageByEntityEvent) {
            $player = $source->getDamager();
            if ($player instanceof Player) {
                if ($player->getName() === $this->getPetOwnerName()) {
                    $item = $player->getInventory()->getItemInHand();
                    if ($item->getTypeId() === 329) { // saddle
                        if (in_array($player->getWorld()->getDisplayName(), Values::SERVER_WORLDS, true)) {
                            $player->sendMessage(TextFormat::RED . "Pets are only rideable on islands!");
                            return;
                        }
                        if (!$player->isSneaking() && $this->canBeRidden) {
                            $this->setRider($player);
                            $player->sendTip(TextFormat::AQUA . "Crouch or jump to dismount...");
                        } else {
                            if (!$this->canBeRidden) {
                                $player->sendMessage(TextFormat::AQUA . "This pet is not rideable...");
                            }
                        }
                    }
                }
            }
        }
        $this->getLoader()->updateNameTag($this);
        $source->cancel();
    }

    /**
     * Returns the name of the owner of this pet.
     * @return string
     */
    final public function getPetOwnerName() : string {
        return $this->petOwner->getName();
    }

    /**
     * Internal.
     * @return string
     */
    public function getNameTag() : string {
        return $this->getPetName();
    }

    protected function syncNetworkData(EntityMetadataCollection $properties) : void {
        parent::syncNetworkData($properties);

        $properties->setGenericFlag(EntityMetadataFlags::TAMED, true);
        $properties->setGenericFlag(EntityMetadataFlags::RIDING, $this->riding);
        $properties->setGenericFlag(EntityMetadataFlags::SADDLED, $this->saddled);
    }

    /**
     * Returns the actual name of the pet. Not to be confused with getName(), which returns the pet type name.
     * @return string
     */
    public function getPetName() : string {
        return $this->petName;
    }

    /**
     * Returns the speed of this pet.
     * @return float
     */
    public function getSpeed() : float {
        return $this->speed;
    }

    protected function getInitialSizeInfo() : EntitySizeInfo {
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId() : string {
        return self::ENTITY_ID;
    }

    /**
     * @return float
     */
    public function getStartingScale() : float {
        return $this->scale;
    }

    /**
     * @param int $currentTick
     *
     * @return bool
     */
    final public function onUpdate(int $currentTick) : bool {
        if (!parent::onUpdate($currentTick) && $this->isClosed()) {
            return false;
        }
        $petOwner = $this->getPetOwner();
        if ($this->isRiding()) {
            $this->gravityEnabled = false;
            $ownerLoc = $petOwner->getLocation();
            $currLoc = $this->getLocation();
            $x = $ownerLoc->getX() - $currLoc->getX();
            $y = $ownerLoc->getY() - $currLoc->getY();
            $z = $ownerLoc->getZ() - $currLoc->getZ();
            if ($x !== 0.0 || $z !== 0.0 || $y !== -$petOwner->getSize()->getHeight()) {
                $this->move($x, $y + $petOwner->getSize()->getHeight(), $z);
            }
            return false;
        }
        $this->gravityEnabled = true;
        if (!$this->checkUpdateRequirements()) {
            return true;
        }
        if (!$this->isRidden()) {
            $petOwner = $this->getPetOwner();
            if (!$this->isDormant() && ($this->getWorld()->getEntity($petOwner->getId()) === null || $this->location->distance($petOwner->location) >= 50)) {
                $this->teleport($petOwner->location);
                return true;
            }
            ++$this->positionSeekTick;
        }
        $this->doPetUpdates($currentTick);
        return true;
    }

    /**
     * @return bool
     */
    public function isRiding() : bool {
        return $this->riding;
    }

    /**
     * Returns the player that owns this pet if they are online.
     * @return Player
     */
    final public function getPetOwner() : Player {
        return $this->petOwner;
    }

    /**
     * @return bool
     */
    protected function checkUpdateRequirements() : bool {
        if (!$this->visibility) {
            return false;
        }
        if ($this->isDormant()) {
            $this->despawnFromAll();
            return false;
        }
        if ($this->getPetOwner()->isClosed()) {
            $this->rider = null;
            $this->riding = false;
            $this->despawnFromAll();
            $this->setDormant();
            $this->close();
            return false;
        }
        if (!$this->getPetOwner()->isAlive()) {
            return false;
        }
        return true;
    }

    /**
     * Returns whether this pet is dormant or not. If this pet is dormant, it will not move.
     * @return bool
     */
    public function isDormant() : bool {
        return $this->dormant;
    }

    /**
     * Sets the dormant state to this pet with the given value.
     *
     * @param bool $value
     */
    public function setDormant(bool $value = true) : void {
        $this->dormant = $value;
    }

    /**
     * Returns whether this pet is being ridden or not.
     * @return bool
     */
    public function isRidden() : bool {
        return $this->rider !== null;
    }

    /**
     * @param int $currentTick
     *
     * @return bool
     */
    public function doPetUpdates(int $currentTick) : bool {
        return true;
    }

    /**
     * @param bool $ignore
     */
    public function kill(bool $ignore = false) : void {
        $this->shouldIgnoreEvent = $ignore;
        parent::kill();
    }

    /**
     * Detaches the rider from the pet.
     * @return bool
     */
    public function throwRiderOff() : bool {
        if (!$this->isRidden()) {
            return false;
        }
        $rider = $this->getRider();
        $this->rider = null;
        $this->riding = false;
        //		$rider->canCollide = true;
        $this->removeLink($rider, self::LINK_RIDER);
        $this->networkPropertiesDirty = true;

        if ($rider->isSurvival()) {
            $rider->setAllowFlight(false);
        }
        $rider->onGround = true;
        $this->size = $this->getInitialSizeInfo();
        $this->recalculateBoundingBox();
        return true;
    }

    /**
     * Returns the rider of the pet if it has a rider, and null if this is not the case.
     * @return Player|null
     */
    public function getRider() : ?Player {
        return $this->rider;
    }

    /**
     * Sets the given player as rider on the pet, connecting it to it and initializing some things.
     *
     * @param Player $player
     *
     * @return bool
     */
    public function setRider(Player $player) : bool {
        if ($this->isRidden()) {
            return false;
        }

        $this->rider = $player;
        //		$player->canCollide = false;
        $owner = $this->getPetOwner();

        $player->getNetworkProperties()->setVector3(EntityMetadataProperties::RIDER_SEAT_POSITION, $this->rider_seatpos);
        $player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::RIDING, true);

        $this->addLink($player, self::LINK_RIDER);

        $this->saddled = true;
        $this->networkPropertiesDirty = true;

        if ($owner->isSurvival()) {
            $owner->setAllowFlight(true); // Set allow flight to true to prevent any 'kicked for flying' issues.
        }
        $this->size = new EntitySizeInfo(
            max(($this->rider_seatpos->y / 2.5) + $player->size->getHeight(), $this->size->getHeight()),
            max($player->size->getWidth(), $this->size->getWidth())
        );

        $this->recalculateBoundingBox();
        return true;
    }

    public function isSaddled() : bool {
        return $this->saddled;
    }

    public function setSaddled(bool $saddled) : void {
        $this->saddled = $saddled;
        $this->networkPropertiesDirty = true;
    }

    /**
     * Removes a link from this pet.
     *
     * @param Entity $entity
     * @param int    $type
     */
    public function removeLink(Entity $entity, int $type) : void {
        if (!isset($this->links[$type])) {
            return;
        }
        $viewers = $this->getViewers();
        switch ($type) {
            case self::LINK_RIDER:
                $link = new EntityLink($this->getId(), $entity->getId(), self::STATE_STANDING, true, true);

                if ($entity instanceof Player) {
                    $pk = new SetActorLinkPacket();
                    $pk->link = $link;
                    $entity->getNetworkSession()->sendDataPacket($pk);

                    $link_2 = new EntityLink($this->getId(), 0, self::STATE_STANDING, true, true);

                    $pk = new SetActorLinkPacket();
                    $pk->link = $link_2;
                    $entity->getNetworkSession()->sendDataPacket($pk);
                    unset($viewers[$entity->getId()]);
                }
                break;
            case self::LINK_RIDING:
                $link = new EntityLink($entity->getId(), $this->getId(), self::STATE_STANDING, true, false);

                if ($entity instanceof Player) {
                    $pk = new SetActorLinkPacket();
                    $pk->link = $link;
                    $entity->getNetworkSession()->sendDataPacket($pk);

                    $link_2 = new EntityLink($entity->getId(), 0, self::STATE_STANDING, true, false);

                    $pk = new SetActorLinkPacket();
                    $pk->link = $link_2;
                    $entity->getNetworkSession()->sendDataPacket($pk);
                    unset($viewers[$entity->getId()]);
                }
                break;
            default:
                throw new InvalidArgumentException();
        }
        unset($this->links[$type]);
        if (!empty($viewers)) {
            $pk = new SetActorLinkPacket();
            $pk->link = $link;
            NetworkBroadcastUtils::broadcastPackets($viewers, [$pk]);
        }
    }

    /**
     * Heals the current pet back to full health.
     */
    public function fullHeal() : bool {
        $health = $this->getHealth();
        $maxHealth = $this->getMaxHealth();
        if ($health == $maxHealth) {
            return false;
        }
        $diff = $maxHealth - $health;
        $this->heal(new EntityRegainHealthEvent($this, $diff, EntityRegainHealthEvent::CAUSE_CUSTOM));
        return true;
    }

    /**
     * @param string $newName
     */
    public function changeName(string $newName) : void {
        $this->petName = $newName;
        unset($this->getLoader()->playerPets[strtolower($this->getPetOwnerName())][strtolower($this->getPetName())]);
        $this->getLoader()->playerPets[strtolower($this->getPetOwnerName())][strtolower($this->getPetName())] = $this;
        $this->getLoader()->updateNameTag($this);
    }

    /**
     * @return bool
     */
    public function shouldIgnoreEvent() : bool {
        return $this->shouldIgnoreEvent;
    }

    /**
     * @param float $motionX
     * @param float $motionZ
     */
    public abstract function doRidingMovement(float $motionX, float $motionZ) : void;

    /**
     * Adds a link to this pet.
     *
     * @param Entity $entity
     * @param int    $type
     */
    public function addLink(Entity $entity, int $type) : void {
        $this->removeLink($entity, $type);
        $viewers = $this->getViewers();
        switch ($type) {
            case self::LINK_RIDER:
                $link = new EntityLink($this->getId(), $entity->getId(), self::STATE_SITTING, true, true);

                if ($entity instanceof Player) {
                    $pk = new SetActorLinkPacket();
                    $pk->link = $link;
                    $entity->getNetworkSession()->sendDataPacket($pk);

                    $link_2 = new EntityLink($this->getId(), 0, self::STATE_SITTING, true, true);

                    $pk = new SetActorLinkPacket();
                    $pk->link = $link_2;
                    $entity->getNetworkSession()->sendDataPacket($pk);
                    unset($viewers[$entity->getId()]);
                }
                break;
            case self::LINK_RIDING:
                $link = new EntityLink($entity->getId(), $this->getId(), self::STATE_SITTING, true, false);

                if ($entity instanceof Player) {
                    $pk = new SetActorLinkPacket();
                    $pk->link = $link;
                    $entity->getNetworkSession()->sendDataPacket($pk);

                    $link_2 = new EntityLink($entity->getId(), 0, self::STATE_SITTING, true, false);

                    $pk = new SetActorLinkPacket();
                    $pk->link = $link_2;
                    $entity->getNetworkSession()->sendDataPacket($pk);
                    unset($viewers[$entity->getId()]);
                }
                break;
            default:
                throw new InvalidArgumentException();
        }
        if (!empty($viewers)) {
            $pk = new SetActorLinkPacket();
            $pk->link = $link;
            NetworkBroadcastUtils::broadcastPackets($viewers, [$pk]);
        }
        $this->links[$type] = $link;
    }

    /**
     * @param Player $player
     */
    protected function sendSpawnPacket(Player $player) : void {
        $player->getNetworkSession()->sendDataPacket(AddActorPacket::create(
            $this->getId(),
            $this->getId(),
            static::ENTITY_ID,
            $this->location->asVector3(),
            $this->getMotion(),
            $this->location->pitch,
            $this->location->yaw,
            $this->location->yaw,
            $this->location->yaw,
            array_map(static function(Attribute $attr) : NetworkAttribute {
                return new NetworkAttribute($attr->getId(), $attr->getMinValue(), $attr->getMaxValue(), $attr->getValue(), $attr->getDefaultValue(), []);
            }, $this->attributeMap->getAll()
            ),
            $this->getAllNetworkData(),
            new PropertySyncData([], []),
            array_values($this->links)
        )
        );
    }

    public function generateCustomPetData() {
    }

    protected function initEntity(CompoundTag $nbt) : void {
        parent::initEntity($nbt);
        $this->selectProperties();
        $this->petName = $nbt->getString("petName");
        $this->scale = $nbt->getFloat("scale", 1.0);
        $this->setScale($this->scale);
        $this->setNameTagVisible(true);
        $this->setNameTagAlwaysVisible(true);
        $this->spawnToAll();

        $this->getAttributeMap()->add(AttributeFactory::getInstance()->get(Attribute::HORSE_JUMP_STRENGTH));
        $this->setCanSaveWithChunk(false);

        $this->generateCustomPetData();
        $this->setImmobile();

        $scale = $this->getScale();
        if ($this instanceof EnderDragonPet) {
            $this->rider_seatpos = new Vector3(-0.5, 3.35 + $scale, -1.7);
        } elseif ($this instanceof SmallCreature) {
            $this->rider_seatpos = new Vector3(0, 0.78 + $scale * 0.9, -0.25);
        } else {
            $this->rider_seatpos = new Vector3(0, 1.8 + $scale * 0.9, -0.25);
        }
        $this->seatpos = new Vector3(0, $scale * 0.4 - 0.3, 0);
        $this->networkPropertiesDirty = true;
    }

    public function selectProperties() : void {
        $properties = $this->getLoader()->getPetProperties()->getPropertiesFor($this->getEntityType());
        $this->useProperties($properties);
    }

    /**
     * Internal.
     * @return string
     */
    public function getEntityType() : string {
        return strtr($this->getName(), [
            " "   => "",
            "Pet" => ""
        ]
        );
    }

    /**
     * Returns the name of the pet type.
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * @param array $properties
     */
    public function useProperties(array $properties) : void {
        $this->type = (string) $properties["Type"];
        $this->speed = (float) $properties["Speed"];
        $this->canBeRidden = (bool) $properties["Can-Be-Ridden"];
    }

    protected function broadcastMovement(bool $teleport = false) : void {
        if ($this->isRiding()) {
            return;
        }
        parent::broadcastMovement($teleport);
    }
}