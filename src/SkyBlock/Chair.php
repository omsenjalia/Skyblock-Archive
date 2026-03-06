<?php


namespace SkyBlock;


use pocketmine\block\Block;
use pocketmine\block\Stair;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\LongMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\Player;

class Chair {

    /** @var array */
    public array $coolDown = [];
    /** @var Main */
    private Main $plugin;
    /** @var array */
    private array $sitting = [];
    /** @var array */
    private array $doubleTap = [];
    /** @var int */
    private const COOLDOWN = 3;

    /**
     * Chair constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param Block $block
     *
     * @return bool
     */
    public function canSit(Block $block) : bool {
        return $this->isStairBlock($block) && ($this->plugin->getIslandManager()->getOnlineIslandByWorld($block->getPosition()->getWorld()->getDisplayName())) !== null;
    }

    /**
     * @param Block $block
     *
     * @return bool
     */
    public function isStairBlock(Block $block) : bool {
        return $block instanceof Stair && $block->getStateId() <= 3;
    }

    /**
     * @param SBPlayer $player
     *
     * @return bool
     */
    public function isSitting(Player $player) : bool {
        return array_key_exists($player->getName(), $this->sitting);
    }

    public function isUsingSeat(Vector3 $pos) : ?Player {
        foreach ($this->sitting as $name => $data) {
            if ($pos->equals($data[1])) {
                return $this->plugin->getServer()->getPlayerByPrefix($name);
            }
        }
        return null;
    }

    /**
     * @param SBPlayer $player
     */
    public function unsetSitting(Player $player) : void {
        $id = $this->getSitData($player);
        $pk = new SetActorLinkPacket();
        $entLink = new EntityLink($id, $player->getId(), EntityLink::TYPE_REMOVE, true, false);
        $pk->link = $entLink;
        //        $this->plugin->getServer()->broadcastPackets($this->plugin->getServer()->getOnlinePlayers(), [$pk]);
        NetworkBroadcastUtils::broadcastPackets($this->plugin->getServer()->getOnlinePlayers(), [$pk]);
        $pk = new RemoveActorPacket();
        $pk->actorUniqueId = $id;
        //        $this->plugin->getServer()->broadcastPackets($this->plugin->getServer()->getOnlinePlayers(), [$pk]);
        NetworkBroadcastUtils::broadcastPackets($this->plugin->getServer()->getOnlinePlayers(), [$pk]);
        $player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::RIDING, false);
        unset($this->sitting[$player->getName()]);
    }

    /**
     * @param SBPlayer $player
     * @param int      $type
     *
     * @return mixed
     */
    public function getSitData(Player $player, int $type = 0) : mixed {
        return $this->sitting[$player->getName()][$type];
    }

    /**
     * @param SBPlayer      $player
     * @param Vector3       $pos
     * @param int           $id
     * @param SBPlayer|null $specific
     *
     * @return bool
     */
    public function setSitting(Player $player, Vector3 $pos, int $id, ?Player $specific = null) : bool {
        if ($specific === null) {
            $user = $this->plugin->getUserManager()->getOnlineUser($player->getName());
            if (!$user->getPref()->chair_feature) {
                $player->sendTip("§cChairs are disabled for you, use /pref to enable");
                return false;
            }
            if (isset($this->doubleTap[$player->getName()])) {
                if ((array_sum(explode(' ', microtime())) - $this->doubleTap[$player->getName()]) > 1) {
                    unset($this->doubleTap[$player->getName()]);
                    return true;
                }
            } else {
                $player->sendTip("§eTap again to sit");
                $this->doubleTap[$player->getName()] = array_sum(explode(' ', microtime()));
                return true;
            }
            unset($this->doubleTap[$player->getName()]);
        }
        if (isset($this->coolDown[$player->getName()]) && $this->coolDown[$player->getName()] > time()) {
            $left = $this->coolDown[$player->getName()] - time();
            $player->sendTip("§cYou need to wait §4" . $left . " §cmore seconds!");
            return true;
        }
        $addEntity = new AddActorPacket();
        $addEntity->actorRuntimeId = $id;
        $addEntity->actorUniqueId = $id;
        $addEntity->type = EntityIds::ARROW;
        $addEntity->position = $pos->add(0.5, 1.5, 0.5);
        $addEntity->syncedProperties = new PropertySyncData([], []);
        $flags = new LongMetadataProperty(1 << EntityMetadataFlags::IMMOBILE | 1 << EntityMetadataFlags::SILENT | 1 << EntityMetadataFlags::INVISIBLE);

        //$addEntity->metadata = [EntityMetadataProperties::FLAGS => [EntityMetadataTypes::LONG, $flags]];
        $addEntity->metadata = [EntityMetadataProperties::FLAGS => $flags];
        $setEntity = new SetActorLinkPacket();
        $entLink = new EntityLink($id, $player->getId(), EntityLink::TYPE_RIDER, true, false);
        $setEntity->link = $entLink;
        if ($specific !== null) {
            $specific->getNetworkSession()->sendDataPacket($addEntity);
            $specific->getNetworkSession()->sendDataPacket($setEntity);
        } else {
            $player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::RIDING, true);
            $this->setSitPlayerId($player, $id, $pos->floor());
            //            $this->plugin->getServer()->broadcastPackets($this->plugin->getServer()->getOnlinePlayers(), [$addEntity]);
            NetworkBroadcastUtils::broadcastPackets($this->plugin->getServer()->getOnlinePlayers(), [$addEntity]);
            //            $this->plugin->getServer()->broadcastPackets($this->plugin->getServer()->getOnlinePlayers(), [$setEntity]);
            NetworkBroadcastUtils::broadcastPackets($this->plugin->getServer()->getOnlinePlayers(), [$setEntity]);
            $player->sendTip("§eYou are now sitting!");
            $this->coolDown[$player->getName()] = time() + self::COOLDOWN;
        }
        return true;
    }

    /**
     * @param SBPlayer $player
     * @param int      $id
     * @param Vector3  $pos
     */
    public function setSitPlayerId(Player $player, int $id, Vector3 $pos) : void {
        $this->sitting[$player->getName()] = [$id, $pos];
    }

}