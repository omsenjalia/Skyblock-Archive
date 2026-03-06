<?php

namespace SkyBlock;

use alvin0319\CustomItemLoader\CustomItems;
use customiesdevs\customies\block\CustomiesBlockFactory;
use PermsX\event\RankChangeEvent;
use pocketmine\block\{BaseSign,
    Block,
    BlockTypeIds,
    Crops,
    Lava,
    Liquid,
    utils\FortuneDropHelper,
    utils\SignText,
    VanillaBlocks,
    WallSign};
use pocketmine\block\tile\{Chest, Container, Sign};
use pocketmine\entity\{effect\VanillaEffects,
    Entity,
    Human,
    Living,
    object\ExperienceOrb,
    object\FallingBlock,
    object\ItemEntity,
    projectile\Arrow,
    projectile\Egg,
    projectile\EnderPearl,
    projectile\Snowball,
    projectile\SplashPotion,
    utils\ExperienceUtils};
use pocketmine\event\block\{BlockBreakEvent,
    BlockDeathEvent,
    BlockFormEvent,
    BlockPlaceEvent,
    BlockSpreadEvent,
    ChestPairEvent,
    LeavesDecayEvent,
    SignChangeEvent};
use pocketmine\event\entity\{EntityCombustByBlockEvent,
    EntityDamageByChildEntityEvent,
    EntityDamageByEntityEvent,
    EntityDamageEvent,
    EntityDeathEvent,
    EntityDespawnEvent,
    EntityEffectAddEvent,
    EntityExplodeEvent,
    EntityMotionEvent,
    EntityRegainHealthEvent,
    EntityShootBowEvent,
    EntitySpawnEvent,
    EntityTeleportEvent,
    EntityTrampleFarmlandEvent,
    ProjectileHitBlockEvent,
    ProjectileHitEntityEvent,
    ProjectileLaunchEvent};
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerChatEvent,
    PlayerCreationEvent,
    PlayerDeathEvent,
    PlayerDropItemEvent,
    PlayerExperienceChangeEvent,
    PlayerInteractEvent,
    PlayerItemConsumeEvent,
    PlayerItemHeldEvent,
    PlayerJoinEvent,
    PlayerKickEvent,
    PlayerLoginEvent,
    PlayerMoveEvent,
    PlayerPreLoginEvent,
    PlayerQuitEvent,
    PlayerToggleGlideEvent,
    PlayerToggleSneakEvent};
use pocketmine\event\server\CommandEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\world\ChunkLoadEvent;
use pocketmine\event\world\WorldLoadEvent;
use pocketmine\event\world\WorldUnloadEvent;
use pocketmine\item\{Armor,
    Bow,
    Durable,
    ItemTypeIds,
    LegacyStringToItemParser,
    LegacyStringToItemParserException,
    Pickaxe,
    StringToItemParser,
    ToolTier,
    VanillaItems};
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\PacketViolationWarningPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\types\PlayerAuthInputFlags;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Limits;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\World;
use SkyBlock\block\AutoMiner;
use SkyBlock\block\AutoSeller;
use SkyBlock\block\MonsterSpawner;
use SkyBlock\chat\Chat;
use SkyBlock\chat\GangChat;
use SkyBlock\command\Functions;
use SkyBlock\enchants\armor\Antidote;
use SkyBlock\enchants\armor\BaseArmorEnchant;
use SkyBlock\enchants\armor\Bloom;
use SkyBlock\enchants\armor\Deflate;
use SkyBlock\enchants\armor\DoubleJump;
use SkyBlock\enchants\armor\Inspirit;
use SkyBlock\enchants\armor\Sharingan;
use SkyBlock\enchants\armor\Tank;
use SkyBlock\enchants\BaseEnchantment;
use SkyBlock\enchants\block\Barter;
use SkyBlock\enchants\block\BaseBlockBreakEnchant;
use SkyBlock\enchants\block\Insurance;
use SkyBlock\enchants\block\LuckOfTheSky;
use SkyBlock\enchants\block\Prosperity;
use SkyBlock\enchants\block\Tinkerer;
use SkyBlock\enchants\bow\player\BaseBowHitPlayerEnchant;
use SkyBlock\enchants\bow\shoot\BaseBowShootEnchant;
use SkyBlock\enchants\sword\BaseMeleeEnchant;
use SkyBlock\enchants\sword\Chisel;
use SkyBlock\enchants\sword\MobSlayer;
use SkyBlock\enchants\sword\OverPower;
use SkyBlock\enchants\touch\BaseTouchEnchant;
use SkyBlock\item\ItemManager;
use SkyBlock\perms\Permission;
use SkyBlock\pets\BasePet;
use SkyBlock\spawner\Creature;
use SkyBlock\spawner\SpawnerEntity;
use SkyBlock\tiles\AutoMinerTile;
use SkyBlock\tiles\AutoSellerTile;
use SkyBlock\tiles\CatalystTile;
use SkyBlock\tiles\Hopper;
use SkyBlock\tiles\MobSpawner;
use SkyBlock\tiles\OreGenTile;
use SkyBlock\user\UserManager;
use SkyBlock\util\Constants;
use SkyBlock\util\Util;
use SkyBlock\util\Values;

class EventListener implements Listener {

    /** @var Main */
    private Main $plugin, $pl;
    /** @var EvFunctions */
    private EvFunctions $ev;
    /** @var UserManager */
    private UserManager $um;
    /** @var int */
    public const MAX_Y = World::Y_MAX - 6;

    /**
     * EventListener constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $this->pl = $plugin;
        $this->ev = $plugin->getEvFunctions();
        $this->um = $plugin->getUserManager();
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    /**
     * @param ChunkLoadEvent $event
     */
    public function onChunkLoad(ChunkLoadEvent $event) : void {
        if (!$event->isNewChunk()) {
            return;
        }
        $world = $event->getWorld();
        $chunk = $event->getChunk();
        if (isset(Values::SERVER_WORLDS[$world->getDisplayName()])) {
            return;
        }
        $position = new Vector3(4, 6, 4);
        if ($world->getChunk($position->getFloorX() >> 4, $position->getFloorZ() >> 4) === $event->getChunk()) {
            $tile = new Chest($world, $position);
            $inventory = $tile->getInventory();
            $inventory->addItem(VanillaBlocks::ICE()->asItem());
            $inventory->addItem(VanillaBlocks::LAVA()->asItem()->setCount(2));
            $inventory->addItem(VanillaItems::BONE());
            $inventory->addItem(VanillaBlocks::SUGARCANE()->asItem()->setCount(2));
            $inventory->addItem(VanillaItems::PUMPKIN_SEEDS());
            $inventory->addItem(VanillaItems::CARROT());
            $inventory->addItem(VanillaItems::WHEAT_SEEDS()->setCount(2));
            $inventory->addItem(VanillaItems::BEETROOT_SEEDS()->setCount(2));
            $inventory->addItem(VanillaItems::BREAD());
            $chunk->addTile($tile);
        }
        $position = new Vector3(3, 6, 0);
        if ($world->getChunk($position->getFloorX() >> 4, $position->getFloorZ() >> 4) === $event->getChunk()) {
            $chunk->addTile(new Sign($world, $position));
            $sign = $world->getBlock($position);
            if (!$sign instanceof BaseSign) {
                return;
            }
            $sign->setWaxed(true);
            $world->setBlock($position, $sign->setText(new SignText(["§eWelcome to ", "§eyour §bIsland", "§eUse §a/manashop", "§eto upgrade Oregens!"], glowing: true)));
        }
    }

    /**
     * Executes onJoin actions
     *
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event) : void {
        $player = $event->getPlayer();
        $player->getPermissionRecalculationCallbacks()->clear();
        $event->setJoinMessage("");

        $player->sendTitle("Please join the new discord server at discord.gg/ftech", "", -1, 100);
        $player->sendMessage("Please join the new discord server at discord.gg/ftech");

        $msg = "";
        foreach (Main::getInstance()->msgs as $message) {
            $msg .= $message . "\n";
        }
        $player->sendMessage($msg);
        $island = Main::getInstance()->getUserManager()->checkPlayer($player);
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
        if ($user->getPref()) {
            Main::getInstance()->teleportToSpawn($player);
        }
        Main::getInstance()->getIslandManager()->checkPlayerIsland($player, $island);
        Main::getInstance()->getGangManager()->checkPlayerGang($player);
        Main::getInstance()->getParticleManager()->sendParticle([$player], true, Values::LOBBY_WORLD);
        $player->setNameTag(Main::getInstance()->getEvFunctions()->getNametag($player));
        Main::getInstance()->getEvFunctions()->loadPlayerPet($player);
        if (!Main::getInstance()->hasOp($player)) {
            $player->setGamemode(GameMode::ADVENTURE());
        }
        $player->setInvisible(false);
        if (Main::getInstance()->updates["enabled"]) {
            Main::getInstance()->getFormFunctions()->sendUpdateWindow($player);
        }
        foreach ($player->getEffects() as $effect) {
            $player->getEffects()->remove($effect);
        }
        $this->sendMessage($player, Main::$joinMessage);
    }

    public function onPlayerExperienceChange(PlayerExperienceChangeEvent $event) : void {
        $player = $event->getEntity();
        if (!$player instanceof Player) {
            return;
        }
        if ($player->getXpManager()->getLifetimeTotalXp() !== 0 && $player->getXpManager()->getCurrentTotalXp() === 0) {
            $newLevel = ExperienceUtils::getLevelFromXp($player->getXpManager()->getLifetimeTotalXp());
            $xpLevel = (int) $newLevel;
            $xpProgress = $newLevel - (int) $newLevel;
            if ($xpLevel >= 21863.0) {
                $xpLevel = 21862;
                $player->getXpManager()->setLifetimeTotalXp(Limits::INT32_MAX - 1000);
            }
            $player->getXpManager()->setXpAndProgressNoEvent($xpLevel, $xpProgress);
            $event->setNewLevel($xpLevel);
            $event->setNewProgress($xpProgress);
        }
        $xpLevel = ExperienceUtils::getXpToReachLevel($event->getNewLevel());
        $xpProgress = intval(floor(ExperienceUtils::getXpToCompleteLevel($event->getNewLevel()) * $event->getNewProgress()));
        $player->getXpManager()->setXpAndProgressNoEvent($event->getNewLevel() ?? 0, $event->getNewProgress() ?? 0.00);
        $newXp = min(Limits::INT32_MAX, $xpLevel + $xpProgress);
        $player->getXpManager()->setLifetimeTotalXp($newXp);
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
        $user?->setXP($newXp);
        $event->cancel();
    }

    public function onDataPacketReceive(DataPacketReceiveEvent $event) : void {
        $packet = $event->getPacket();
        $player = $event->getOrigin()->getPlayer();

        if ($packet instanceof PlayerAuthInputPacket) {
            if (Main::getInstance()->isRidingAPet($player)) {
                if ((int) $packet->getMoveVecX() === 0 && (int) $packet->getMoveVecZ() === 0) {
                    return;
                }
                $pet = Main::getInstance()->getRiddenPet($player);
                if ($pet->isClosed() || $pet->isFlaggedForDespawn()) {
                    return;
                }
                $pet->doRidingMovement($packet->getMoveVecX(), $packet->getMoveVecZ());
            } elseif ($packet->hasFlag(PlayerAuthInputFlags::JUMP_DOWN)) {
                if (BaseEnchantment::hasEnchantment($player->getArmorInventory()->getLeggings(), DoubleJump::$id)) {
                    $enchant = BaseEnchantment::getEnchantment(DoubleJump::$id);
                    if ($enchant instanceof DoubleJump) {
                        $enchant->onJumpPressed($player);
                    }
                }
            }
        } elseif ($packet instanceof InteractPacket) {
            if ($packet->action === InteractPacket::ACTION_LEAVE_VEHICLE) {
                if (Main::getInstance()->isRidingAPet($player)) {
                    Main::getInstance()->getRiddenPet($player)->throwRiderOff();
                } elseif (Main::getInstance()->getChair()->isSitting($player)) {
                    Main::getInstance()->getChair()->unsetSitting($player);
                }
            }
        }
    }

    public function onEntityEffectAdd(EntityEffectAddEvent $event) : void {
        $effect = $event->getEffect();
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            if ($effect->getType() === VanillaEffects::POISON() && BaseEnchantment::hasEnchantment($entity->getArmorInventory()->getChestplate(), Antidote::$id)) {
                $event->cancel();
            } elseif ($effect->getType() === VanillaEffects::BLINDNESS() && BaseEnchantment::hasEnchantment($entity->getArmorInventory()->getHelmet(), Sharingan::$id)) {
                $event->cancel();
            } elseif ($effect->getType() === VanillaEffects::INVISIBILITY() && in_array($entity->getWorld()->getDisplayName(), Values::PVP_WORLDS, true)) {
                $this->sendMessage($entity, "Cannot be invisible here!");
                $event->cancel();
            } elseif ($effect->getType() === VanillaEffects::NAUSEA() && BaseEnchantment::hasEnchantment($entity->getArmorInventory()->getHelmet(), Inspirit::$id)) {
                $event->cancel();
            }
        }
    }

    public function onPlayerQuit(PlayerQuitEvent $event) : void {
        $player = $event->getPlayer();
        $event->setQuitMessage("");

        $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
        if ($user === null) {
            return;
        }

        if (!Main::getInstance()->staffapi->isSoftStaff($player->getName())) {
            $msg = "➼§7[§c-§7] §a{$player->getName()} §cleft the game!";
            if ($user->getPref()->welcome_msg) {
                Server::getInstance()->broadcastMessage($msg);
            } else {
                Main::getInstance()->getEvFunctions()->sendStaffMessage($msg);
            }
        }

        Main::getInstance()->removePlayer($player);
        $name = strtolower($player->getName());
        if ($user->isIslandSet()) {
            Main::getInstance()->userIslandCache[$name] = $user->getIsland();
        }
        if ($user->hasIslands()) {
            Main::getInstance()->userHelperCache[$name] = $user->getIslands();
        }
        if (!Main::getInstance()->gandalf->restarting && !Main::getInstance()->restarting) {
            if (Main::getInstance()->isInCombat($player)) {
                $player->kill();
                $player->save();
                unset(Main::getInstance()->combat[$player->getName()]);
            }
        }
        $pet = Main::getInstance()->getPetsFrom($player);
        foreach ($pet as $p) {
            Main::getInstance()->removePet($p);
        }
        if (Main::getInstance()->getChair()->isSitting($player)) {
            Main::getInstance()->getChair()->unsetSitting($player);
        }
        unset($this->pl->enchants[$name]);
        unset($this->pl->upd_touch[$player->getName()]);
        unset($this->pl->inv_full[$player->getName()]);
        unset($this->pl->buy[$name]);
        unset($this->pl->mined[$name]);
        unset($this->pl->nofall[$name]);
        unset($this->pl->notnt[$name]);
        unset($this->pl->grew[$name]);
        unset($this->pl->shrunk[$name]);
        unset($this->pl->icuc[$name]);
        unset($this->pl->icdc[$name]);
        unset($this->pl->sellchest[$name]);
        unset($this->pl->condensechest[$name]);
        unset($this->pl->schest[$name]);
        Main::getInstance()->getGangManager()->unloadByPlayer($player);
        $island = Main::getInstance()->getUserManager()->unloadByPlayer($player);
        Main::getInstance()->getIslandManager()->unloadByPlayer($player, $island);
    }


    /**
     * @priority LOWEST
     * */
    public function onBlockSpread(BlockSpreadEvent $event) : void {
        $block = $event->getSource();
        if ($block instanceof Liquid) {
            $island = Main::getInstance()->getIslandManager()->getOnlineIslandByWorld($block->getPosition()->getWorld()->getDisplayName());
            if ($island === null) {
                return;
            }
            $vector = new Vector3(3, $block->getPosition()->getY(), 2);
            if ($block->getPosition()->getFloorY() >= self::MAX_Y || $block->getPosition()->getFloorY() > $island->getRadius() || $block->getPosition()->distance($vector) > $island->getRadius()) {
                $event->cancel();
            }
        }
    }

    public function onChestPair(ChestPairEvent $event) : void {
        if ($event->isCancelled()) {
            return;
        }

        $island = Main::getInstance()->getIslandManager()->getOnlineIslandByWorld($event->getLeft()->getPosition()->getWorld()->getDisplayName());
        if ($island === null) {
            return;
        }

        if (Main::getInstance()->isPrivateChest($event->getLeft(), $island->getOwner()) || Main::getInstance()->isPrivateChest($event->getRight(), $island->getOwner())) {
            $event->cancel();
        }
    }

    /**
     * @priority LOW
     * */
    public function onBlockBreak(BlockBreakEvent $event) : void {
        if ($event->isCancelled()) {
            return;
        }

        $block = $event->getBlock();
        $player = $event->getPlayer();


        if ($player instanceof SBPlayer) {
            if (!$player->breakCheck($event)) {
                $event->cancel();
                return;
            }
        }
        $block = $event->getBlock();
        $canEdit = isset(Main::getInstance()->gandalf->edit[$player->getName()]);

        if ($player->getPosition()->getWorld()->getDisplayName() === Values::PVP_WORLD && !$canEdit) {
            $event->cancel();
            return;
        }
        if ($player->getPosition()->getWorld()->getDisplayName() === Values::MINES_WORLD) {
            if (!$canEdit) {
                $event->cancel();
                Main::getInstance()->getEvFunctions()->handleMinesBlockBreakEvent($event);
                return;
            }
        }

        $island = Main::getInstance()->getIslandManager()->getOnlineIslandByWorld($player->getPosition()->getWorld()->getDisplayName());
        if ($island === null) {
            return;
        }

        if (!$canEdit) {
            if (!$island->isMember($player->getName())) {
                if (!EvFunctions::roleCheck($island, $player, $block->getTypeId(), $block->getStateId(), "break")) {
                    $this->sendMessage($player, TextFormat::RED . "You must be a §6Helper §cof this island to break blocks!");
                    $event->cancel();
                    return;
                }
            } else {
                if ($island->getFreeze()) {
                    if ($island->isHelper($player->getName()) || $island->hasARole($player->getName())) {
                        if (!$island->hasPerm($player->getName(), Permission::FREEZE)) {
                            $this->sendMessage($player, TextFormat::RED . "Island is frozen by island owner. You cannot break blocks!");
                            $event->cancel();
                            return;
                        }
                    } else {
                        if ($island->getFreeze()) {
                            if ($island->isHelper($player->getName()) || $island->hasARole($player->getName())) {
                                if (!$island->hasPerm($player->getName(), Permission::FREEZE)) {
                                    $this->sendMessage($player, TextFormat::RED . "Island is frozen by island owner. Cannot break blocks!");
                                    $event->cancel();
                                    return;
                                }
                            }
                        }
                        if (!$island->hasPerm($player->getName(), Permission::BREAK)) {
                            $this->sendMessage($player, TextFormat::RED . "You dont have breaking perms on this island");
                            $event->cancel();
                            return;
                        }
                    }
                    if ($block->getTypeId() === BlockTypeIds::CHEST) {
                        if (!$island->hasPerm($player->getName(), Permission::CHEST)) {
                            $this->sendMessage($player, TextFormat::RED . "You dont have chest permissions on this island");
                            $event->cancel();
                        }
                    }
                    if (in_array($block->getTypeId(), Data::$customBlockBlocks)) {
                        if (!$island->hasPerm($player->getName(), Permission::CUSTOM_BLOCKS)) {
                            $this->sendMessage($player, TextFormat::RED . "You dont have custom block perms on this island");
                            $event->cancel();
                            return;
                        }
                    }
                    if ($block instanceof WallSign) {
                        if (Main::getInstance()->isPrivateChestSign($block, $island->getOwner())) {
                            if (!$island->isOwner($player->getName())) {
                                $this->sendMessage($player, "§cThat Sign is Private, only the Island Owner can break it!");
                                $event->cancel();
                            } else {
                                $this->sendMessage($player, "§cPrivate chest destroyed!");
                                Main::getInstance()->destroyPrivateChest($block, $player->getName());
                            }
                            return;
                        }
                    }
                }
                if ($block instanceof \pocketmine\block\Chest) {
                    if (Main::getInstance()->isPrivateChestSign($block, $island->getOwner())) {
                        $this->sendMessage($player, "§cThat Chest is Private, break the sign first to break the chest!");
                        $event->cancel();
                        return;
                    }
                }
                if ($block->getPosition()->getFloorY() >= self::MAX_Y) {
                    $this->sendMessage($player, TextFormat::RED . "You can't break here! You reached the limit!");
                    $event->cancel();
                    return;
                }
                $radius = $island->getRadius();
                if ($block->getPosition()->getFloorY() > $radius) {
                    $this->sendMessage($player, TextFormat::RED . "You need to expand your island by /is expand to place blocks above!");
                    $event->cancel();
                    return;
                }
                if ($island->getFarmingMode() === 1 && !EvFunctions::isFarmRipe($block)) {
                    $event->cancel();
                    return;
                }
                if (!Main::getInstance()->getEvFunctions()->checkPlayerShop($player, $block)) {
                    $event->cancel();
                    return;
                }
                if ($event->isCancelled()) {
                    return;
                }
                if ($block->getTypeId() === BlockTypeIds::HOPPER) {
                    $tile = $player->getPosition()->getWorld()->getTile($block->getPosition());
                    if (!$island->isOwner($player->getName()) && !$island->hasPerm($player->getName(), Permission::CUSTOM_BLOCKS)) {
                        $this->sendMessage($player, TextFormat::RED . "You dont have custom block perms on this island");
                        $event->cancel();
                        return;
                    } else {
                        if ($island->getMiningMode() === 1) {
                            $event->cancel();
                            return;
                        }
                    }
                    if ($tile instanceof Hopper) {
                        $island = Main::getInstance()->getIslandManager()->getOnlineIslandByWorld($block->getPosition()->getWorld()->getDisplayName());
                        if (!is_null($island)) {
                            $island->removeHopper();
                        }
                    }
                }
                if ($block->getTypeId() == BlockTypeIds::MONSTER_SPAWNER) {
                    $heldItem = $player->getInventory()->getItemInHand();
                    if (($heldItem instanceof Pickaxe || $heldItem instanceof item\Pickaxe) && $heldItem->getTier()->getHarvestLevel() >= ToolTier::IRON()->getHarvestLevel()) {
                        $tile = $player->getWorld()->getTile($block->getPosition());
                        if ($tile instanceof MobSpawner) {
                            if (!$island->isOwner($player->getName()) && !$island->hasPerm($player->getName(), Permission::CUSTOM_BLOCKS)) {
                                $this->sendMessage($player, TextFormat::RED . "You dont have custom block perms on this island");
                                $event->cancel();
                            } else {
                                if (!$player->getInventory()->canAddItem(VanillaBlocks::MONSTER_SPAWNER()->asItem())) {
                                    $this->sendMessage($player, "§cYour inventory is full!");
                                    $event->cancel();
                                    return;
                                }
                                $event->setDrops([]);
                            }
                        } else {
                            $this->sendMessage($player, TextFormat::RED . "Weird Error! Contact staff");
                            $event->cancel();
                        }
                    } else {
                        $this->sendMessage($player, TextFormat::RED . "Use Iron Pickaxe or Better!");
                        $event->cancel();
                    }
                    return;
                }
                if ($block->getTypeId() === CustomiesBlockFactory::getInstance()->get("fallentech:autominer")->getTypeId()) {
                    $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                    if ($tile instanceof AutoMinerTile) {
                        if (!$island->isOwner($player->getName()) && !$island->hasPerm($player->getName(), Permission::CUSTOM_BLOCKS)) {
                            $this->sendMessage($player, TextFormat::RED . "You dont have custom block perms on this island");
                            $event->cancel();
                        } else {
                            if ($island->getMiningMode() === 1) {
                                $this->sendMessage($player, "§cYou Cannot do this in mining mode!");
                                $event->cancel();
                                return;
                            }
                            if (!$player->getInventory()->canAddItem($block->asItem())) {
                                $this->sendMessage($player, "§cYour inventory is full!");
                                $event->cancel();
                                return;
                            }
                            $event->setDrops([]);
                        }
                        return;
                    }
                }
                if ($block->getTypeId() === CustomiesBlockFactory::getInstance()->get("fallentech:autoseller")->getTypeId()) {
                    $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                    if ($tile instanceof AutoSellerTile) {
                        if (!$island->isOwner($player->getName()) && !$island->hasPerm($player->getName(), Permission::CUSTOM_BLOCKS)) {
                            $this->sendMessage($player, TextFormat::RED . "You dont have custom block perms on this island");
                            $event->cancel();
                        } else {
                            if ($island->getMiningMode() === 1) {
                                $this->sendMessage($player, "§cYou Cannot do this in mining mode!");
                                $event->cancel();
                                return;
                            }
                            if (!$player->getInventory()->canAddItem($block->asItem())) {
                                $this->sendMessage($player, "§cYour inventory is full!");
                                $event->cancel();
                                return;
                            }
                            $event->setDrops([]);
                        }
                        return;
                    }
                }
                if ($block->getTypeId() === CustomiesBlockFactory::getInstance()->get("fallentech:catalyst")->getTypeId()) {
                    $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                    if ($tile instanceof CatalystTile) {
                        if (!$island->isOwner($player->getName()) && !$island->hasPerm($player->getName(), Permission::CUSTOM_BLOCKS)) {
                            $this->sendMessage($player, TextFormat::RED . "You dont have custom block perms on this island");
                            $event->cancel();
                            return;
                        } else {
                            if ($island->getMiningMode() === 1) {
                                $this->sendMessage($player, "§cYou Cannot do this in mining mode!");
                                $event->cancel();
                                return;
                            }
                            if (!$player->getInventory()->canAddItem($block->asItem())) {
                                $this->sendMessage($player, "§cYour inventory is full!");
                                $event->cancel();
                                return;
                            }
                            $event->setDrops([]);
                            return;
                        }
                    }
                }
                $usingPlayer = Main::getInstance()->getChair()->isUsingSeat($block->getPosition()->floor());
                if (Main::getInstance()->getChair()->isStairBlock($block) && $usingPlayer) {
                    Main::getInstance()->getChair()->unsetSitting($usingPlayer);
                }
                Main::getInstance()->getEvFunctions()->forceDestroyShop($block);
                $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
                Main::getInstance()->getEvFunctions()->checkBlockBreak($user, $block, $island, $event);
                $item = $event->getItem();
                if ($block instanceof Crops) {
                    switch ($block->getTypeId()) {
                        case BlockTypeIds::SUGARCANE:
                            $event->setDrops([VanillaBlocks::SUGARCANE()->asItem()->setCount(FortuneDropHelper::discrete($item, 1, 4))]);
                            $island->removeFarm();
                            break;
                        case BlockTypeIds::BEETROOTS:
                            $event->setDrops([VanillaItems::BEETROOT()->setCount(FortuneDropHelper::discrete($item, 1, 4))]);
                            $island->removeFarm();
                            break;
                        case BlockTypeIds::WHEAT:
                            $event->setDrops([VanillaBlocks::WHEAT()->asItem()->setCount(FortuneDropHelper::discrete($item, 1, 4))]);
                            $island->removeFarm();
                            break;
                        case BlockTypeIds::CACTUS:
                            $event->setDrops([VanillaBlocks::CACTUS()->asItem()->setCount(FortuneDropHelper::discrete($item, 1, 2))]);
                            $island->removeFarm();
                            break;
                        case BlockTypeIds::NETHER_WART:
                            $event->setDrops([VanillaBlocks::NETHER_WART()->asItem()->setCount(FortuneDropHelper::discrete($item, 1, 4))]);
                            $island->removeFarm();
                            break;
                        case BlockTypeIds::POTATOES:
                            $event->setDrops([VanillaBlocks::POTATOES()->asItem()->setCount(FortuneDropHelper::discrete($item, 1, 4))]);
                            $island->removeFarm();
                            break;
                        case BlockTypeIds::CARROTS:
                            $event->setDrops([VanillaBlocks::CARROTS()->asItem()->setCount(FortuneDropHelper::discrete($item, 1, 4))]);
                            $island->removeFarm();
                            break;
                        case BlockTypeIds::PUMPKIN:
                            $event->setDrops([VanillaBlocks::PUMPKIN()->asItem()->setCount(FortuneDropHelper::discrete($item, 1, 3))]);
                            $island->removeFarm();
                            break;
                        case BlockTypeIds::MELON:
                            $event->setDrops([VanillaBlocks::MELON()->asItem()->setCount(FortuneDropHelper::discrete($item, 1, 3))]);
                            $island->removeFarm();
                            break;
                    }
                }
                $player->getInventory()->setItemInHand(ItemManager::getInstance()->doItemTasks($player->getInventory()->getItemInHand(), $event));

                $tinkerer = $barter = $lots = $prosperity = false;
                if ($player->getInventory()->getItemInHand()->hasEnchantments()) {
                    foreach ($player->getInventory()->getItemInHand()->getEnchantments() as $enchantment) {
                        $type = $enchantment->getType();
                        if ($type instanceof BaseBlockBreakEnchant && $type->isApplicableTo($player, $enchantment->getLevel())) {
                            if ($type instanceof Barter) {
                                $barter = true;
                                $user->addMana(Functions::calcTinkBarterMoneyXp($enchantment->getLevel()));
                            }
                            if ($type instanceof Tinkerer) {
                                $tinkerer = true;
                                Functions::safeXPAdd($user, Functions::calcTinkBarterMoneyXp($enchantment->getLevel()));
                            }
                            if ($type instanceof LuckOfTheSky) {
                                $lots = $enchantment->getLevel();
                            }
                            if ($type instanceof Prosperity) {
                                $prosperity = true;
                            }
                            if ($type instanceof Insurance) {
                                $item = $event->getItem();
                                if ($item instanceof Durable) {
                                    if ($item->getDamage() <= 5) {
                                        $type->onActivation($player, $event, $enchantment->getLevel());
                                        $event->cancel();
                                        return;
                                    }
                                }
                            }
                            $type->onActivation($player, $event, $enchantment->getLevel());
                        }
                    }
                }
                $user->addBlocksBroken();
                Main::getInstance()->serverblocks++;

                if (in_array($block->getTypeId(), Constants::FARM_BLOCKS) && EvFunctions::isFarmRipe($block)) {
                    $maxRandom = 1500;
                    if (is_int((int) $lots)) {
                        $maxRandom -= (int) $lots * ($lots <= 10 ? 40 : 55);
                    }
                    if (mt_rand(1, $maxRandom) < 5) {
                        $item = Main::getInstance()->getEvFunctions()->getRandomRelic($prosperity);
                        $player->sendMessage("§b§l>> §eYou got a §aRelic! §b<<§r");
                        $drops = $event->getDrops();
                        $drops[] = $item;
                        $event->setDrops($drops);
                    }
                }

                if ($block->getTypeId() === BlockTypeIds::CHEST || $block->getTypeId() === BlockTypeIds::TRAPPED_CHEST) {
                    $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                    if ($tile instanceof Container) {
                        $event->setDrops([]);
                        if (!Main::getInstance()->getEvFunctions()->addChestItems($player, $tile)) {
                            $this->sendMessage($player, "Cant break chest, Inventory not empty enough, added the chest items which could be added!");
                            $event->cancel();
                        }
                        return;
                    }
                }
                foreach ($event->getDrops() as $drop) {
                    if (is_array($drop)) {
                        foreach ($drop as $dr) {
                            if (!Main::getInstance()->getEvFunctions()->addItemInInventory($player, $dr, $barter, $tinkerer)) {
                                break;
                            }
                        }
                    } else {
                        Main::getInstance()->getEvFunctions()->addItemInInventory($player, $drop, $barter, $tinkerer);
                    }
                }
                $event->setDrops([]);
            }
        }
    }

    /**
     * @param EntityDeathEvent $event
     */
    public function entityDeath(EntityDeathEvent $event) : void {
        $entityDamaged = $event->getEntity();
        $cause = $entityDamaged->getLastDamageCause();
        if ($cause instanceof EntityDamageByEntityEvent) { // player v entity
            $killer = $cause->getDamager();
            if ($killer instanceof Player) {
                foreach ($event->getDrops() as $drop) {
                    if ($killer->getInventory()->canAddItem($drop)) {
                        $killer->getInventory()->addItem($drop);
                    } else {
                        //						todo add to stash
                        $this->sendMessage($killer, TextFormat::RED . "Your inventory is full!");
                        break;
                    }
                }
                $event->setDrops([]);
                if ($entityDamaged instanceof Living && !$entityDamaged instanceof Human && !$entityDamaged instanceof BasePet) { // player v mob
                    $exp = $entityDamaged->getXpDropAmount();
                    $chisel = BaseEnchantment::getEnchantmentLevel($killer->getInventory()->getItemInHand(), Chisel::$id);
                    $user = Main::getInstance()->getUserManager()->getOnlineUser($killer->getName());
                    if ($exp > 0 && $chisel > 0 && $user !== null) {
                        $exp += $chisel;
                        Functions::safeXPAdd($user, $exp);
                        if ($entityDamaged instanceof SpawnerEntity) {
                            $user->addMobCoin($entityDamaged->getMobcoins());
                        }
                    }
                } elseif ($entityDamaged instanceof Player) { // player v player
                    $entityDamaged->getCraftingGrid()->clearAll();
                    $entityDamaged->getCursorInventory()->clearAll();
                    $exp = min(91, $entityDamaged->getXpManager()->getCurrentTotalXp());
                    $user = Main::getInstance()->getUserManager()->getOnlineUser($killer->getName());
                    if ($user !== null) {
                        Functions::safeXPAdd($user, $exp);
                    }
                }
            }
        } else { // mob v lava
            if ($entityDamaged instanceof Living && !$entityDamaged instanceof Human && !$entityDamaged instanceof BasePet) {
                $exp = $entityDamaged->getXpDropAmount();
                $island = Main::getInstance()->getIslandManager()->getOnlineIslandByWorld($entityDamaged->getWorld()->getDisplayName());
                if ($exp > 0 && $island !== null) {
                    $receiver = $island->getReceiver();
                    $player = Server::getInstance()->getPlayerExact($receiver);
                    if (!$player instanceof Player) {
                        $player = $island->getRandomOnlineCoOwner();
                        if ($player === null) {
                            return;
                        }
                        $receiver = $player->getName();
                    }
                    $user = Main::getInstance()->getUserManager()->getOnlineUser($receiver);
                    if ($user !== null && $island->isAnOwner($receiver)) {
                        Functions::safeXPAdd($user, $exp);
                        if ($entityDamaged instanceof SpawnerEntity) {
                            $user->addMobCoin($entityDamaged->getMobcoins());
                        }
                    }
                }
            }
        }
    }

    public function onCraftItem(CraftItemEvent $event) : void {
        $player = $event->getPlayer();
        $inputs = $event->getInputs();
        $outputs = $event->getOutputs();
        foreach ($inputs as $input) {
            if ($input->hasCustomName()) {
                $this->sendMessage($player, "§cYou cannot use that item to craft!");
                $event->cancel();
                return;
            }
        }
        foreach ($outputs as $output) {
            if ($output->getTypeId() === ItemTypeIds::SLIMEBALL || $output->getTypeId() === CustomItems::CARROT_ON_A_STICK()->getTypeId()) {
                $this->sendMessage($player, "§cYou cannot craft that item!");
                $event->cancel();
                return;
            }
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event) : void {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $canEdit = isset(Main::getInstance()->gandalf->edit[$player->getName()]);

        /**
         * @var int   $x     The x-coordinate
         * @var int   $y     The y-coordinate
         * @var int   $z     The z-coordinate
         * @var Block $block The block object
         */
        foreach ($event->getTransaction()->getBlocks() as [$x, $y, $z, $block]) {
            if (isset(Data::$illegalBlocks[$block->getTypeId()]) && !$canEdit) {
                $event->cancel();
                $player->getInventory()->remove($item);
                return;
            }
            if (isset(Main::getInstance()->placeQueue[$player->getName()])) {
                $event->cancel();
                unset(Main::getInstance()->placeQueue[$player->getName()]);
                return;
            }
            if (in_array($player->getWorld()->getDisplayName(), [Values::PVP_WORLD, Values::MINES_WORLD, Values::LOBBY_WORLD]) && !$canEdit) {
                $event->cancel();
                return;
            }

            $island = Main::getInstance()->getIslandManager()->getOnlineIslandByWorld($player->getWorld()->getDisplayName());
            if ($island === null) {
                return;
            }

            if (!$canEdit) {
                if (Main::getInstance()->getEvFunctions()->checkIfRelic($player, $item)) {
                    $event->cancel();
                    return;
                }
                if (!$island->isMember($player->getName())) {
                    if (!EvFunctions::roleCheck($island, $player, $item->getTypeId(), $item->getStateId(), "place")) {
                        $this->sendMessage($player, TextFormat::RED . "You must be a §6Helper §cof this island to place here!");
                        $event->cancel();
                        return;
                    }
                }
                if (!$island->hasPerm($player->getName(), Permission::BUILD)) {
                    $this->sendMessage($player, TextFormat::RED . "You don't have build permissions on this island!");
                    $event->cancel();
                    return;
                }
                if ($island->getFreeze()) {
                    if ($island->isHelper($player->getName()) || $island->hasARole($player->getName())) {
                        if (!$island->hasPerm($player->getName(), Permission::FREEZE)) {
                            $this->sendMessage($player, TextFormat::RED . "Island is frozen by island owner. Cannot place blocks!");
                            $event->cancel();
                            return;
                        }
                    }
                }
                if ($block->getTypeId() === BlockTypeIds::CHEST && !$island->isAdmin($player->getName()) && !$island->isCoowner($player->getName())) {
                    $this->sendMessage($player, TextFormat::RED . "You must be an §9Admin §cor §bCoOwner §cof this island to place chests!");
                    $event->cancel();
                    return;
                }
                if (isset(Data::$customBlockBlocks[$block->getTypeId()])) {
                    if (!$island->hasPerm($player->getName(), Permission::CUSTOM_BLOCKS)) {
                        $this->sendMessage($player, TextFormat::RED . "You don't have custom block permissions on this island!");
                        $event->cancel();
                        return;
                    }
                }
            }
            if ($block->getPosition()->getFloorY() >= self::MAX_Y) {
                $this->sendMessage($player, TextFormat::RED . "You can't place here! You reached the limit!");
                $event->cancel();
                return;
            }
            $radius = $island->getRadius();
            $vector = new Vector3(3, $block->getPosition()->getFloorY(), 2);
            if (!$canEdit) {
                if ($block->getPosition()->distance($vector) > $radius || $block->getPosition()->getFloorY() > $radius) {
                    $this->sendMessage($player, TextFormat::RED . "You can't place here. You need to expand your island by using /is expand!");
                    $event->cancel();
                    return;
                }
            }
            $location = $block->getPosition()->getX() . ":" . $block->getPosition()->getY() . ":" . $block->getPosition()->getZ() . ":" . $block->getPosition()->getWorld()->getDisplayName();
            if (isset(Main::getInstance()->shops[$location])) {
                Main::getInstance()->getEvFunctions()->forceDestroyShop($block);
            }
            if ($block->getTypeId() === BlockTypeIds::MONSTER_SPAWNER) {
                if ($item->hasCustomName()) {
                    if ($island->getSpawner() + 1 > $island->getSpawnerLimit()) {
                        $this->sendMessage($player, "§cYou have reached the max number of Spawners allowed on your island. To increase the amount use /is expand!");
                        $event->cancel();
                    } elseif (!$event->isCancelled()) {
                        $custom = preg_split('/\r\n|\r|\n/', $item->getCustomName());
                        $clean = TextFormat::clean($custom[1]);
                        $level = (int) substr($clean, -1);
                        $island->addSpawner();
                        $this->sendMessage($player, "§ePlaced $custom[0] §eLevel §6$level!\n§6Shift click with an empty hand to upgrade Spawner level!\n§aYou can pick up Spawner blocks from Iron or Diamond pickaxe, you wont lose Spawner level.");
                    }
                    return;
                }
            }
            if ($block->getTypeId() === BlockTypeIds::HOPPER) {
                if ($island->getHopper() + 1 > $island->getHopperLimit()) {
                    $this->sendMessage($player, "§cYou have reached the max place limit of that block on your Island, to increase use §a/is expand.");
                    $event->cancel();
                } elseif (!$event->isCancelled()) {
                    $this->sendMessage($player, "§ePlaced §aHopper");
                    $island->addHopper();
                }
                return;
            }
            if ($block->getTypeId() === CustomiesBlockFactory::getInstance()->get("fallentech:autominer")->getTypeId()) {
                if (!$island->isOwner($player->getName()) && !$island->hasPerm($player->getName(), Permission::CUSTOM_BLOCKS)) {
                    $event->cancel();
                    $this->sendMessage($player, TextFormat::RED . "You dont have custom block perms on this island");
                }
                if ($item->hasCustomName()) {
                    if (($island->getAutoMiner() + 1) > $island->getAutoMinerLimit()) {
                        $this->sendMessage($player, "§cYou have reached the max place limit of that block on your Island, to increase use §a/is expand.");
                        $event->cancel();
                    } elseif (!$event->isCancelled()) {
                        $island->addAutoMiner();
                        $this->sendMessage($player, "§ePlaced §aAuto Miner\n§6Shift click with an empty hand to upgrade AutoMiner level! §bBuy /sellchest & /condensechest exclusively from §astore.fallentech.io, §bno rank has it.");
                    }
                    return;
                }
            }
            if ($block->getTypeId() === CustomiesBlockFactory::getInstance()->get("fallentech:autoseller")->getTypeId()) {
                if (!$island->isOwner($player->getName()) && !$island->hasPerm($player->getName(), Permission::CUSTOM_BLOCKS)) {
                    $event->cancel();
                    $this->sendMessage($player, TextFormat::RED . "You dont have custom block perms on this island");
                }
                if ($item->hasCustomName()) {
                    if (($island->getAutoSeller() + 1) > $island->getAutoSellerLimit()) {
                        $this->sendMessage($player, "§cYou have reached the max place limit of that block on your Island, to increase use §a/is expand.");
                        $event->cancel();
                    } elseif (!$event->isCancelled()) {
                        $island->addAutoSeller();
                        $this->sendMessage($player, "§ePlaced §aAuto Seller\n§6Shift click with an empty hand to upgrade AutoSeller level!");
                    }
                    return;
                }
            }
            if ($block->getTypeId() == CustomiesBlockFactory::getInstance()->get("fallentech:catalyst")->getTypeId()) {
                if ((!$island->isOwner($player->getName()) && !$island->hasPerm($player->getName(), Permission::CUSTOM_BLOCKS)) and !$this->pl->isTrusted($player->getName())) {
                    $event->cancel();
                    $this->sendMessage($player, TextFormat::RED . "You dont have custom block perms on this island");
                }
                if (($island->getCatalyst() + 1) > $island->getCatalystLimit() and !$this->pl->isTrusted($player->getName())) {
                    $this->sendMessage($player, "§cYou have reached the max place limit of that block on your Island, to increase use §a/is expand. You can also remove old ones!");
                    $event->cancel();
                } elseif (!$event->isCancelled()) {
                    $island->addOreGen();
                    $player->sendPopup("§ePlaced §l§mCatalyst");
                }
                return;
            }
            if (!Main::getInstance()->getEvFunctions()->canPlaceSeed($player, $block->getTypeId(), $island->getLevel())) {
                $event->cancel();
                return;
            } else {
                if (isset(Main::getInstance()->crops[$block->getTypeId()])) {
                    if (!$island->hasARole($player->getName()) && !$island->hasPerm($player->getName(), Permission::FARM)) {
                        $this->sendMessage($player, TextFormat::RED . "You dont have farming perms on this island");
                        $event->cancel();
                        return;
                    }
                    if ($island->getFreeze() + 1 > $island->getFarmLimit()) {
                        $player->sendMessage("§cYou have reached the max limit of farming on your Island, to increase use §a/is expand.");
                        $event->cancel();
                        return;
                    } elseif (!$event->isCancelled()) {
                        $island->addFarm();
                    }
                }
            }
            if ($block->canBePlaced()) {
                $island->setPoints(1);
            }
            if (Main::getInstance()->getEvFunctions()->checkIfRelic($player, $item)) {
                $event->cancel();
            }
        }
    }

    public function onPlayerMove(PlayerMoveEvent $event) : void {
        if ($event->isCancelled()) {
            return;
        }
        $player = $event->getPlayer();
        if (isset(Main::getInstance()->autosprint[$player->getName()])) {
            if ($event->getTo()->getFloorX() !== $event->getFrom()->getFloorX() || $event->getTo()->getFloorZ() !== $event->getFrom()->getFloorZ()) {
                $player->setSprinting();
            }
        }
        if (Main::getInstance()->hasOp($player)) {
            return;
        }
        $island = Main::getInstance()->getIslandManager()->getOnlineIslandByWorld($player->getWorld()->getDisplayName());
        if ($island === null) {
            return;
        }
        $vector = new Vector3(3, $player->getPosition()->getFloorY(), 2);
        if ($player->getPosition()->distance($vector) - 1 > $island->getRadius()) {
            $player->sendPopup(TextFormat::RED . "You cannot go there. Expand your island by using /is expand!");
            $event->cancel();
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $event) : void {
        if ($event->isCancelled()) {
            return;
        }
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $item = $event->getItem();

        $canEdit = isset(Main::getInstance()->gandalf->edit[$event->getEventName()]);
        if ($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK && $block->getPosition()->getWorld()->getDisplayName() === Values::MINES_WORLD) {
            if (in_array($block->getTypeId(), Data::$mineableBlocks)) {
                if (($item instanceof Pickaxe || $item instanceof item\Pickaxe) && $item->getBlockToolHarvestLevel() < ToolTier::IRON()) {
                    $this->sendMessage($player, "Use an iron pickaxe or better!");
                    $event->cancel();
                    return;
                }
            }
        }
        $bannedBlocks = [
            BlockTypeIds::BREWING_STAND, BlockTypeIds::ENCHANTING_TABLE, BlockTypeIds::HOPPER,
            BlockTypeIds::STONECUTTER, BlockTypeIds::BEACON, BlockTypeIds::BLAST_FURNACE, BlockTypeIds::CHISELED_BOOKSHELF,
            BlockTypeIds::REDSTONE_COMPARATOR, BlockTypeIds::DAYLIGHT_SENSOR,
            BlockTypeIds::JUKEBOX, BlockTypeIds::NOTE_BLOCK, BlockTypeIds::SHULKER_BOX,
            BlockTypeIds::DYED_SHULKER_BOX, BlockTypeIds::SMOKER
        ]; // todo furnace
        if (in_array($block->getTypeId(), $bannedBlocks)) {
            $event->cancel();
            return;
        }
        if ($block->getTypeId() === BlockTypeIds::ANVIL) {
            $this->sendMessage($player, "§cGet §aFixer scroll §cby /vote or /ms to fix items or get §b/fix and /fixall from §cstore!");
            $event->cancel();
            return;
        }
        $allowed_blocks = [BlockTypeIds::AIR, BlockTypeIds::CHEST, BlockTypeIds::TRAPPED_CHEST];
        if (!$canEdit && !in_array($block->getTypeId(), $allowed_blocks, true) && ($player->getWorld()->getDisplayName() === Values::LOBBY_WORLD || $player->getWorld()->getDisplayName() === Values::PVP_WORLD)) {
            $event->cancel();
        }
        if ($block->getTypeId() === BlockTypeIds::CHEST) {
            if ($block->getPosition()->getWorld()->getDisplayName() === Values::PVP_WORLD) {
                if (!isset(Main::getInstance()->using[strtolower($player->getName())]) || Main::getInstance()->using[strtolower($player->getName())] <= time()) {
                    $event->cancel();
                    if (!empty(Main::getInstance()->envoys)) {
                        foreach (Main::getInstance()->envoys as $id => $data) {
                            if ($data["x"] === $block->getPosition()->getX() && $data["y"] === $block->getPosition()->getY() && $data["z"] === $block->getPosition()->getZ()) {
                                Main::getInstance()->using[strtolower($player->getName())] = time() + 2;
                                $block->getPosition()->getWorld()->useBreakOn($block->getPosition()->asVector3());
                                Server::getInstance()->broadcastMessage("§l§b(!) §a{$player->getName()} §bfound an §eEnvoy §bChest at /warp warzone");
                                $pos = new Position($data["x"], $data["y"], $data["z"], $block->getPosition()->getWorld());
                                Main::getInstance()->despawnEnvoy($id, $pos);
                            }
                        }
                    } else {
                        $block->getPosition()->getWorld()->setBlock($event->getBlock()->getModelPositionOffset(), VanillaBlocks::AIR());
                        $this->sendMessage($player, "§cEnvoy event not running!");
                    }
                }
            }
            if ($block->getPosition()->getWorld()->getDisplayName() === Values::LOBBY_WORLD) {
                $event->cancel();
            }
        }
        if (Main::getInstance()->getEvFunctions()->checkCustomItem($player, $item, $block)) {
            $event->cancel();
            return;
        }
        if ($event->isCancelled()) {
            return;
        }
        $island = Main::getInstance()->getIslandManager()->getOnlineIslandByWorld($player->getWorld()->getDisplayName());
        if ($island === null) {
            return;
        }
        if ($player->isSneaking() && $player->getInventory()->getItemInHand()->equals(VanillaItems::AIR()) && $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            if (!isset(Main::getInstance()->interact[strtolower($player->getName())])) {
                Main::getInstance()->interact[strtolower($player->getName())] = time();
            } elseif (time() - Main::getInstance()->interact[strtolower($player->getName())] >= 1) {
                if ($island->hasPerm($player->getName(), Permission::CUSTOM_BLOCKS)) {
                    if ($block instanceof AutoMiner) {
                        Main::getInstance()->getFormFunctions()->sendAutoMinerUpgrade($player, $block);
                    } elseif ($block instanceof AutoSeller) {
                        Main::getInstance()->getFormFunctions()->sendAutoSellerUpgrade($player, $block);
                    } elseif ($block instanceof MonsterSpawner) {
                        Main::getInstance()->getFormFunctions()->sendSpawnerUpgrade($player, $block);
                    }
                    Main::getInstance()->interact[strtolower($player->getName())] = time();
                } else {
                    $this->sendMessage($player, "§cYou don't have custom block permissions on this island!");
                }
            }
        }
        if ($block->getTypeId() === BlockTypeIds::CHEST) {
            if (isset(Main::getInstance()->icuc[strtolower($player->getName())]) && !$player->isSneaking()) {
                Main::getInstance()->removePlayerPet($player);
                if ($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                    return;
                }
                $event->cancel();
                if (isset(Main::getInstance()->icuctime[strtolower($player->getName())])) {
                    $time = 2;
                    $left = time() - Main::getInstance()->icuctime[strtolower($player->getName())];
                    if ($left < $time) {
                        $this->sendMessage($player, "§cYou need to wait §a" . ($time - $left) . " §cseconds to upload chest again");
                        return;
                    } else {
                        unset(Main::getInstance()->icuctime[strtolower($player->getName())]);
                    }
                }
                if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName()) === null) {
                    return;
                }
                if (!$island->hasPerm($player->getName(), Permission::EXCL_CMDS) && !$island->isOwner($player->getName())) {
                    $this->sendMessage($player, "§cYou don't have exclusive command permissions on this island!");
                    return;
                }
                if (isset(Main::getInstance()->icucc[strtolower($player->getName())])) {
                    $then = Main::getInstance()->icucc[strtolower($player->getName())];
                    if (array_sum(explode(" ", microtime())) - $then < 1) {
                        $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                        if ($tile instanceof Chest) {
                            $inventory = $tile->getInventory();
                            Main::getInstance()->icuctime[strtolower($player->getName())] = time();
                            Main::getInstance()->getEvFunctions()->uploadChest($player, $inventory, Main::getInstance()->icuc[strtolower($player->getName())]);
                        } else {
                            $this->sendMessage($player, "Unknown error!");
                        }
                    } else {
                        $this->sendMessage($player, "Task cancelled, please double tap faster.");
                    }
                    unset(Main::getInstance()->icucc[strtolower($player->getName())]);
                } else {
                    $this->pl->icucc[strtolower($player->getName())] = array_sum(explode(' ', microtime()));
                    if (($user = $this->um->getOnlineUser($player->getName())) === null) return;
                    if ($user->getPref()->exclcmdmessages) {
                        $this->sendMessage($player, "§eTap the chest again to upload...");
                    }
                }
            } elseif (isset($this->pl->icdc[strtolower($player->getName())]) && !$player->isSneaking()) {
                $this->pl->removePlayerPet($player);
                if ($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) return;
                $event->cancel();
                if (isset($this->pl->icdctime[strtolower($player->getName())])) {
                    $time = 2;
                    if (($left = time() - $this->pl->icdctime[strtolower($player->getName())]) < $time) {
                        $this->sendMessage($player, "§cYou need to wait §a" . ($time - $left) . " §cseconds to download chest again");
                        return;
                    } else unset($this->pl->icdctime[strtolower($player->getName())]);
                }
                if (($this->um->getOnlineUser($player->getName())) === null) return;
                if (!$island->hasPerm($player->getName(), Permission::EXCL_CMDS) && !$island->isOwner($player->getName())) {
                    $this->sendMessage($player, "§cYou dont have exclusive cmds perms on this island");
                    return;
                }
                if (isset($this->pl->icdcc[strtolower($player->getName())])) {
                    $then = $this->pl->icdcc[strtolower($player->getName())];
                    if ((array_sum(explode(' ', microtime())) - $then) < 1) {
                        $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                        if ($tile instanceof Chest) {
                            $inv = $tile->getInventory();
                            $this->pl->icdctime[strtolower($player->getName())] = time();
                            $this->ev->downloadChest($player, $inv, $this->pl->icdc[strtolower($player->getName())]);
                        } else {
                            $this->sendMessage($player, "Unknown Error.");
                        }
                    } else {
                        $this->sendMessage($player, "Task Cancelled, please double tap faster.");
                    }
                    unset($this->pl->icdcc[strtolower($player->getName())]);
                } else {
                    $this->pl->icdcc[strtolower($player->getName())] = array_sum(explode(' ', microtime()));
                    if (($user = $this->um->getOnlineUser($player->getName())) === null) return;
                    if ($user->getPref()->exclcmdmessages) {
                        $this->sendMessage($player, "§eTap the chest again to download...");
                    }
                }
            } elseif (isset($this->pl->sellchest[strtolower($player->getName())]) && !$player->isSneaking()) {
                $this->pl->removePlayerPet($player);
                if ($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) return;
                $event->cancel();
                if (($user = $this->um->getOnlineUser($player->getName())) === null) return;
                if (!$island->hasPerm($player->getName(), Permission::EXCL_CMDS) && !$island->isOwner($player->getName())) {
                    $this->sendMessage($player, "§cYou dont have exclusive cmds perms on this island");
                    return;
                }
                $type = $this->pl->sellchest[strtolower($player->getName())];
                if (isset($this->pl->schest[strtolower($player->getName())])) {
                    $then = $this->pl->schest[strtolower($player->getName())];
                    if ((array_sum(explode(' ', microtime())) - $then) < 1) {
                        $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                        if ($tile instanceof Chest) {
                            $inv = $tile->getInventory();
                            $this->ev->sellChest($inv, $user, strtolower($type)); // yo
                        } else {
                            $this->sendMessage($player, "Unknown Error.");
                        }
                    } else {
                        $this->sendMessage($player, "Task Cancelled, please double tap faster.");
                    }
                    unset($this->pl->schest[strtolower($player->getName())]);
                } else {
                    $this->pl->schest[strtolower($player->getName())] = array_sum(explode(' ', microtime()));
                    if ($user->getPref()->exclcmdmessages) {
                        $this->sendMessage($player, "§eTap the chest again to sell its contents for §b$type §e...");
                    }
                }
            } elseif (isset($this->pl->condensechest[strtolower($player->getName())]) && !$player->isSneaking()) {
                $this->pl->removePlayerPet($player);
                if ($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) return;
                $event->cancel();
                if (($user = $this->um->getOnlineUser($player->getName())) === null) return;
                if (!$island->hasPerm($player->getName(), Permission::EXCL_CMDS) && !$island->isOwner($player->getName())) {
                    $this->sendMessage($player, "§cYou dont have exclusive cmds perms on this island");
                    return;
                }
                if (isset($this->pl->cchest[strtolower($player->getName())])) {
                    $then = $this->pl->cchest[strtolower($player->getName())];
                    if ((array_sum(explode(' ', microtime())) - $then) < 1) {
                        $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                        if ($tile instanceof Chest) {
                            $inv = $tile->getInventory();
                            $flag = $this->ev->condenseChest($inv, $user);
                            if ($flag && $user->getPref()->exclcmdmessages) {
                                $this->sendMessage($player, "§bAll Resources/Ingots were condensed to their respective Blocks!");
                            } else {
                                $this->sendMessage($player, "§cNo Resource/Ingot item found in chest to condense!");
                            }
                        } else {
                            $this->sendMessage($player, "Unknown Error.");
                        }
                    } else {
                        $this->sendMessage($player, "Task Cancelled, please double tap faster.");
                    }
                    unset($this->pl->cchest[strtolower($player->getName())]);
                } else {
                    $this->pl->cchest[strtolower($player->getName())] = array_sum(explode(' ', microtime()));
                    if ($user->getPref()->exclcmdmessages) {
                        $this->sendMessage($player, "§eTap the chest again to condense its contents §e...");
                    }
                }
            }
            if (!Main::getInstance()->hasOp($player)) {
                if (!$island->isOwner($player->getName())) {
                    if (Main::getInstance()->isPrivateChest($block, $island->getOwner())) {
                        $this->sendMessage($player, "§cThat chest is private, only the island owner can access it!");
                        $event->cancel();
                        return;
                    }
                }
            }
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
        if ($user->getPref()->chair_feature && !Main::getInstance()->getChair()->isSitting($player) && Main::getInstance()->getChair()->canSit($block)) {
            if ($player->isSneaking()) {
                return;
            }
            if ($player->getInventory()->getItemInHand()->getTypeId() !== VanillaItems::AIR()->getTypeId()) {
                $player->sendMessage("§c> Don't hold anything to sit!");
                $event->cancel();
                return;
            }
            $usePlayer = Main::getInstance()->getChair()->isUsingSeat($block->getPosition()->floor());
            if ($usePlayer) {
                $player->sendMessage("§a{$usePlayer->getName()} §cis already using that chair!");
            } else {
                Main::getInstance()->removePlayerPet($player);
                if (Main::getInstance()->getChair()->setSitting($player, $block->getPosition()->asVector3(), Entity::nextRuntimeId())) {
                    $event->cancel();
                }
            }
        }
        if ($block->getTypeId() !== BlockTypeIds::OAK_SIGN && $block->getTypeId() !== BlockTypeIds::OAK_WALL_SIGN) {
            if (!$canEdit) {
                if (!$island->isMember($player->getName())) {
                    if ($block->getTypeId() === BlockTypeIds::ITEM_FRAME) {
                        $event->cancel();
                        return;
                    }
                    if (!EvFunctions::roleCheck($island, $player, $block->getTypeId(), $block->getTypeId(), "touch")) {
                        $this->sendMessage($player, TextFormat::RED . "You must be a §6Helper §cof this island ot interact here!");
                        $event->cancel();
                    } else {
                        if ($item->getTypeId() === ItemTypeIds::BUCKET) {
                            $event->cancel();
                            return;
                        }
                    }
                } else {
                    if ($island->getFreeze()) {
                        if ($island->isHelper($player->getName()) || $island->hasARole($player->getName())) {
                            if (!$island->hasPerm($player->getName(), Permission::FREEZE)) {
                                $this->sendMessage($player, TextFormat::RED . "Island is frozen by island owner! Cannot touch blocks!");
                                $event->cancel();
                                return;
                            }
                        }
                    }
                }
                if ($block->getTypeId() === BlockTypeIds::CHEST) {
                    if (!$island->isAdmin($player->getName()) && !$island->isCoowner($player->getName())) {
                        $this->sendMessage($player, TextFormat::RED . "You must be an §9Admin §cor §bCoowner §cof this island to open chests!");
                        $event->cancel();
                    }
                    if (!$island->hasPerm($player->getName(), Permission::CHEST)) {
                        $this->sendMessage($player, TextFormat::RED . "You don't have chest permissions on this island");
                        $event->cancel();
                        return;
                    }
                }
            }
        } else {
            $loc = $block->getPosition()->getX() . ":" . $block->getPosition()->getY() . ":" . $block->getPosition()->getZ() . ":" . $block->getPosition()->getWorld()->getDisplayName();
            if (isset(Main::getInstance()->shops[$loc])) {
                $event->cancel();
                $shop = Main::getInstance()->shop[$loc];
                Main::getInstance()->getEvFunctions()->checkSignShop($player, $shop, $loc);
                if ($event->getItem()->canBePlaced()) {
                    Main::getInstance()->placeQueue[$player->getName()] = true;
                }
            } else {
                if (!$canEdit) {
                    if (!$island->isMember($player->getName())) {
                        $this->sendMessage($player, TextFormat::RED . "You must be a §6Helper §cof this island to interact here!");
                        $event->cancel();
                    }
                }
            }
        }
        if ($event->isCancelled()) {
            return;
        }
        if (isset(Main::getInstance()->upd_touch[$player->getName()])) {
            $event->cancel();
            $type = Main::getInstance()->upd_touch[$player->getName()];
            unset(Main::getInstance()->upd_touch[$player->getName()]);
            if (!$island->isAnOwner($player->getName())) {
                $this->sendMessage($player, TextFormat::RED . "You need to be the island owner/coowner of this island to upgrade custom blocks!");
                return;
            }
            $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
            switch ($type) {
                case "oregen":
                    if (!$tile instanceof OreGenTile) {
                        $this->sendMessage($player, TextFormat::RED . "That is not a valid Ore Gen block!");
                        return;
                    }
                    Main::getInstance()->getFormFunctions()->sendOreGenUpgrade($player, $block);
                    break;
                case "spawner":
                    if (!$tile instanceof MobSpawner) {
                        $this->sendMessage($player, TextFormat::RED . "That is not a valid Spawner block!");
                        return;
                    }
                    Main::getInstance()->getFormFunctions()->sendSpawnerUpgrade($player, $block);
                    break;
                case "autominer":
                    if (!$tile instanceof AutoMinerTile) {
                        $this->sendMessage($player, TextFormat::RED . "That is not a valid AutoMiner block!");
                        return;
                    }
                    if ($tile->fortune === 0) {
                        Main::getInstance()->getFormFunctions()->sendAutoMinerUpgrade($player, $block);
                    } else {
                        Main::getInstance()->getFormFunctions()->sendAutoMinerFortuneUpgrade($player, $block);
                    }
                    break;
                case "autoseller":
                    if (!$tile instanceof AutoSellerTile) {
                        $this->sendMessage($player, TextFormat::RED . "That is not a valid AutoSeller block!");
                        return;
                    }
                    Main::getInstance()->getFormFunctions()->sendAutoSellerUpgrade($player, $block);
                    break;
            }
            return;
        }
        if ($item->hasEnchantments()) {
            foreach ($item->getEnchantments() as $enchantment) {
                $type = $enchantment->getType();
                if ($type instanceof BaseTouchEnchant && $type->isApplicableTo($player, $enchantment->getLevel(), $block)) {
                    $type->onActivation($player, $event, $enchantment->getLevel());
                }
            }
        }
    }

    public function onPlayerLogin(PlayerLoginEvent $event) : void {
        $player = $event->getPlayer();
        $name = $player->getName();
        $extraData = $player->getNetworkSession()->getPlayerInfo()->getExtraData();

        Main::getInstance()->os[$name] = (int) $extraData["DeviceOS"];
    }

    public function onPlayerPreLogin(PlayerPreLoginEvent $event) : void {
        $player = $event->getPlayerInfo();
        $count = count(Server::getInstance()->getOnlinePlayers());
        if ($count >= Main::MAX_PLAYERS && !Main::getInstance()->getEvFunctions()->hasPremiumRank($player->getUsername())) {
            $event->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_SERVER_FULL, "§6Server is full, maximum players of " . Main::MAX_PLAYERS . " has been reached!\n§eOnly premium ranks can join the full server!");
        } else {
            if ($count >= Main::MAX && !Main::getInstance()->staffapi->isSoftStaff($player->getUsername())) {
                $event->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_SERVER_FULL, "§6Server is at its limit of " . Main::MAX . " players!\n§eOnly staff can join the full server!");
            }
        }
    }

    public function onCommand(CommandEvent $event) : void {
        $player = $event->getSender();
        if (!$player instanceof Player) {
            return;
        }
        $command = explode(" ", $event->getCommand());
        if (Main::getInstance()->isInCombat($player) && !Main::getInstance()->staffapi->isSoftStaff($player->getName())) {
            if (isset($command[0])) {
                $canUse = false;
                foreach (Main::getInstance()->allowed_cmds as $allowedCmd) {
                    $allowedCmd .= " ";
                    if ($allowedCmd === $command[0]) {
                        $canUse = true;
                        break;
                    }
                }
                if (!$canUse) {
                    $player->sendMessage("§cYou cannot use this command during combat.");
                    $event->cancel();
                    return;
                }
            }
        }
    }

    public function onPlayerChat(PlayerChatEvent $event) : void {
        if ($event->isCancelled()) {
            return;
        }

        $player = $event->getPlayer();
        $name = $player->getName();

        if (isset(Main::getInstance()->players[$name]) && (Main::getInstance()->players[$name]["time"] >= time()) && !Main::getInstance()->staffapi->isSoftStaff($name) && !in_array(Main::getInstance()->staffapi->getStaffRank($name), ["Builder", "Head-Builder"], true)) {
            Main::getInstance()->players[$name]["time"] = time() + 2;
            Main::getInstance()->players[$name]["warnings"]++;
            $event->cancel();
            if (Main::getInstance()->players[$name]["warnings"] === 3) {
                $player->sendMessage(TextFormat::RED . "Careful... Last Warning!");
            } elseif (Main::getInstance()->players[$name]["warnings"] > 3) {
                $player->kick(TextFormat::RED . "Please do not spam chat!");
            } else {
                $player->sendMessage(TextFormat::RED . "Please be police and do not spam chat!");
            }
            return;
        } else {
            Main::getInstance()->players[$name] = ["time" => time() + 2, "warnings" => 0];
        }

        $message = TextFormat::clean($event->getMessage());
        if (str_replace(" ", "", $message) === "") {
            $event->cancel();
            return;
        }

        $message = ucfirst($message);
        $chat = Main::getInstance()->getChatHandler()->getPlayerChat($player);
        $gangChat = Main::getInstance()->getGangChatHandler()->getPlayerChat($player);
        if ($chat instanceof Chat) {
            $island = Main::getInstance()->getIslandManager()->getOnlineIslandByWorld($chat->getLevel());
            $island?->sendTeamChatMessage($player, $message);
            $event->cancel();
        } elseif ($gangChat instanceof GangChat) {
            $gang = Main::getInstance()->getGangManager()->getOnlineGang($gangChat->getGang());
            $gang?->sendGangChatMessage($player, $message);
            $event->cancel();
        } else {
            $event->cancel();
            Main::getInstance()->getLogger()->info($event->getPlayer()->getName() . " " . $event->getMessage());
            $common = Server::getInstance()->getPluginManager()->getPlugin("Common");
            $peace = $common->peace;
            foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $p) {
                if (!isset($peace[$p->getName()]) && !isset($common->blocks[strtolower($p->getName())][strtolower($player->getName())])) {
                    $start = Main::getInstance()->getEvFunctions()->getNormalChatFormat($player, $p);
                    if ($start !== null) {
                        $p->sendMessage($start . $message);
                    }
                }
            }
        }
    }

    public function onPlayerItemHeld(PlayerItemHeldEvent $event) : void {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if (isset(Main::getInstance()->gandalf->edit[$player->getName()])) {
            return;
        }

        switch ($item->getTypeId()) {
            case ItemTypeIds::WRITABLE_BOOK:
            case ItemTypeIds::WRITTEN_BOOK:
            case ItemTypeIds::NETHER_STAR:
            case ItemTypeIds::ENDER_PEARL:
            case ItemTypeIds::EXPERIENCE_BOTTLE:
                $player->getInventory()->remove($item);
                $player->sendMessage("§6§lThat item is banned.");
                break;
        }

    }

    public function onEntityShootBow(EntityShootBowEvent $event) : void {
        if ($event->isCancelled()) {
            return;
        }
        $shooter = $event->getEntity();
        $arrow = $event->getProjectile();
        if ($arrow instanceof Arrow) {
            $arrow->setPunchKnockback(ceil($arrow->getPunchKnockback() / 5));
        }

        if (in_array($shooter->getWorld()->getDisplayName(), [Values::LOBBY_WORLD, Values::MINES_WORLD])) {
            $event->cancel();
            return;
        }

        if (in_array($shooter->getWorld()->getDisplayName(), Values::PVP_WORLDS, true)) {
            if ($shooter instanceof Player && $event->getBow() instanceof Bow && $event->getBow()->hasEnchantments()) {
                foreach ($event->getBow()->getEnchantments() as $enchantment) {
                    $type = $enchantment->getType();
                    if ($type instanceof BaseBowShootEnchant && $type->isApplicableTo($shooter)) {
                        $type->onActivation($shooter, $enchantment->getLevel(), $event);
                    }
                }
            }
        }
    }

    /**
     * @param ProjectileHitEntityEvent $event
     */
    public function onArrowHitEntity(ProjectileHitEntityEvent $event) : void {
        $entity = $event->getEntityHit();
        $arrow = $event->getEntity();
        if ($entity instanceof Player) {
            if (isset(Main::getInstance()->god[$entity->getName()])) {
                if ($arrow instanceof Arrow) {
                    $arrow->setPunchKnockback(0);
                    $arrow->setCritical(false);
                    $arrow->setBaseDamage(-1);
                }
            }
        }
    }

    public function onEntityDamage(EntityDamageEvent $event) : void {
        $player = $event->getEntity();
        if ($player instanceof BasePet) {
            $event->cancel();
            return;
        }

        if ($player instanceof Player) {
            if ($player->noDamageTicks <= 0) {
                Main::getInstance()->getEvFunctions()->renderNameTag($player);
            }

            if (!in_array($player->getWorld()->getDisplayName(), Values::PVP_WORLDS, true) && $event->getCause() !== EntityDamageEvent::CAUSE_STARVATION) {
                $event->cancel();
            }
            if ($event->getCause() === EntityDamageEvent::CAUSE_STARVATION && $player->getWorld()->getDisplayName() === Values::LOBBY_WORLD) {
                $event->cancel();
            }
            if ($event->getCause() === EntityDamageEvent::CAUSE_VOID) {
                if (!in_array($player->getWorld()->getDisplayName(), Values::PVP_WORLDS, true)) {
                    $event->cancel();
                    Main::getInstance()->teleportToSpawn($player);
                    return;
                }
            }
            if (isset(Main::getInstance()->god[$player->getName()])) {
                $event->cancel();
                return;
            }
            if ($event->getCause() === EntityDamageEvent::CAUSE_SUFFOCATION) {
                if (!Main::getInstance()->staffapi->isSoftStaff($player->getName())) {
                    if (!in_array($player->getWorld()->getDisplayName(), Values::PVP_WORLDS, true)) {
                        if ($player->getPosition()->distance($player->getWorld()->getSpawnLocation()) >= 5) {
                            Main::getInstance()->teleportToSpawn($player);
                            $this->sendMessage($player, "§eTeleported to spawn due to suffocation!");
                        }
                        $event->cancel();
                    } else {
                        $event->setBaseDamage(4);
                    }
                }
                return;
            }
            if ($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
                $event->cancel();
            }
            if ($event->getCause() === EntityDamageEvent::CAUSE_ENTITY_EXPLOSION) {
                if (isset(Main::getInstance()->notnt[strtolower($player->getName())])) {
                    unset(Main::getInstance()->notnt[strtolower($player->getName())]);
                    $event->cancel();
                }
            }
            if ($event->isCancelled()) {
                return;
            }
        }
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if ($damager instanceof Player && $damager->getEffects()->has(VanillaEffects::STRENGTH()) && $player instanceof Creature) {
                $event->setModifier($event->getBaseDamage() * 0.035 * $damager->getEffects()->get(VanillaEffects::STRENGTH())->getEffectLevel(), EntityDamageEvent::MODIFIER_STRENGTH);
            }
            if ($player instanceof Player && $damager instanceof Player) {
                if ($damager->getPosition()->getFloorY() >= World::Y_MAX || $player->getPosition()->getFloorY() >= World::Y_MAX) {
                    $this->sendMessage($damager, TextFormat::RED . "You cannot PvP above " . World::Y_MAX . " blocks!");
                    $event->cancel();
                    return;
                }
                $player1 = $player->getName();
                $player2 = $damager->getName();
                $user1 = Main::getInstance()->getUserManager()->getOnlineUser($player1);
                $user2 = Main::getInstance()->getUserManager()->getOnlineUser($player2);
                if ($user1 === null || $user2 === null) {
                    return;
                }
                $user1->setLastAttacker($damager->getName());
                $user2->setLastAttacker($player->getName());
                if ($user1->hasGang() && $user2->hasGang()) {
                    if ($user1->getGangLowerCase() === $user2->getGangLowerCase()) {
                        if (!isset(Main::getInstance()->match[$player1]) && !isset(Main::getInstance()->match[$player2])) {
                            $event->cancel();
                            return;
                        }
                    }
                }
                if ($damager->isInvisible() && !Main::getInstance()->hasOp($damager)) {
                    $damager->setInvisible(false);
                    $damager->setNameTagVisible(false);
                    $this->sendMessage($damager, TextFormat::YELLOW . "You are not visible!");
                }
                if ($player->getScale() !== 1) {
                    $player->setScale(1);
                }
                if ($damager->getScale() !== 1) {
                    $damager->setScale(1);
                }
                if ($damager->getAllowFlight() && !isset(Main::getInstance()->god[$player->getName()])) {
                    $damager->setAllowFlight(false);
                    $damager->setFlying(false);
                    $event->cancel();
                    $damager->sendMessage(TextFormat::YELLOW . "Flying mode is disabled!");
                }
                if ($player->getAllowFlight()) {
                    $player->setAllowFlight(false);
                    $player->setFlying(false);
                    $event->cancel();
                    $player->sendMessage(TextFormat::YELLOW . "Flying mode is disabled!");
                }
                if ($event->isCancelled()) {
                    return;
                }
                if (!isset(Main::getInstance()->match[$player1]) && !isset(Main::getInstance()->match[$player2])) {
                    foreach ([$damager, $player] as $players) {
                        Main::getInstance()->getEvFunctions()->setTime($players);
                    }
                }

                $heldItem = $damager->getInventory()->getItemInHand();
                if ($heldItem->hasEnchantments()) {
                    foreach ($heldItem->getEnchantments() as $enchantment) {
                        $type = $enchantment->getType();
                        if ($type instanceof BaseMeleeEnchant && $type->isApplicableTo($player)) {
                            $type->onActivation($damager, $player, $event, $enchantment->getLevel());
                        }
                    }
                }
                $tank = 0;
                $originalKB = $event->getKnockBack();
                foreach ($player->getArmorInventory()->getContents() as $item) {
                    if ($item->hasEnchantments()) {
                        foreach ($item->getEnchantments() as $enchantment) {
                            $type = $enchantment->getType();
                            if ($type instanceof BaseArmorEnchant && $type->isApplicableTo($player)) {
                                $type->onActivation($damager, $player, $enchantment->getLevel());
                            }
                            if ($type instanceof Tank && $enchantment->getLevel() > $tank) {
                                $tank = $enchantment->getLevel();
                                $event->setKnockBack($originalKB - ($tank * 0.002));
                            }
                        }
                    }
                }
            }
            if ($damager instanceof Player && !$player instanceof Player) {
                $island = Main::getInstance()->getIslandManager()->getOnlineIslandByWorld($player->getPosition()->getWorld()->getDisplayName());
                if ($island !== null) {
                    if (!Main::getInstance()->hasOp($damager)) {
                        if (!$island->isMember($damager->getName()) && !in_array($island->getRole($damager->getName()), ["butchers", "labourers"], true)) {
                            $event->cancel();
                            return;
                        }
                    }
                    $event->setKnockBack(0.0);
                    $level = BaseEnchantment::getEnchantmentLevel($damager->getInventory()->getItemInHand(), MobSlayer::$id);
                    if ($level > 0 && mt_rand(1, 6) === 1) {
                        if ($player instanceof Living && !$player instanceof BasePet) {
                            $remove = min(StackFactory::getStackSize($player), ceil($level / 2));
                            if ($remove <= 1) {
                                return;
                            }
                            $player->setLastDamageCause($event);
                            $event->cancel();
                            for ($i = 0; $i <= $remove; $i++) {
                                StackFactory::removeFromStack($player);
                            }
                            $player->setHealth($player->getMaxHealth());
                            StackFactory::recalculateStackName($player);
                            $heldItem = $damager->getInventory()->getItemInHand();
                            if ($heldItem instanceof Durable) {
                                $heldItem->applyDamage(1);
                                $damager->getInventory()->setItemInHand($heldItem);
                            }
                            return;
                        }
                    }
                    $level = BaseEnchantment::getEnchantmentLevel($damager->getInventory()->getItemInHand(), OverPower::$id);
                    if ($level > 0) {
                        $event->setModifier($event->getBaseDamage() * ($level / 10), 159);
                    }
                }
            }
        }
        if ($event instanceof EntityDamageByChildEntityEvent) {
            if ($event->isCancelled()) {
                return;
            }
            $child = $event->getChild();
            $damager = $event->getDamager();
            $victim = $event->getEntity();
            if ($child instanceof Arrow && $victim instanceof Player && $damager instanceof Player) {
                if ($damager->getInventory()->getItemInHand()->hasEnchantments() && $damager->getInventory()->getItemInHand() instanceof Bow) {
                    foreach ($damager->getInventory()->getItemInHand()->getEnchantments() as $enchantment) {
                        $type = $enchantment->getType();
                        if ($type instanceof BaseBowHitPlayerEnchant && $type->isApplicableTo($victim)) {
                            $type->onActivation($damager, $victim, $enchantment->getLevel(), $event);
                        }
                    }
                }
            }
        }
        if (!$player instanceof Player && !$player instanceof BasePet) {
            $cause = $event->getCause();
            if ($cause === EntityDamageEvent::CAUSE_VOID) {
                $player->flagForDespawn();
                $event->cancel();
                return;
            }
            if ($event->getFinalDamage() >= $player->getHealth()) {
                if ($player instanceof Living && StackFactory::isStack($player)) {
                    $player->setLastDamageCause($event);
                    if (StackFactory::removeFromStack($player)) {
                        $event->cancel();
                        if ($event instanceof EntityDamageByEntityEvent) {
                            $damager = $event->getDamager();
                            if ($damager instanceof Player) {
                                $heldItem = $damager->getInventory()->getItemInHand();
                                if ($heldItem instanceof Durable) {
                                    $heldItem->applyDamage(1);
                                    $damager->getInventory()->setItemInHand($heldItem);
                                }
                            }
                        }
                        $player->setHealth($player->getMaxHealth());
                    }
                    StackFactory::recalculateStackName($player);
                }
            }
        }
    }


    public function onPlayerDropItem(PlayerDropItemEvent $event) : void {
        $player = $event->getPlayer();
        if (Main::getInstance()->hasOp($player)) {
            return;
        }
        if (isset(Main::getInstance()->noDrop[$player->getName()])) {
            $event->cancel();
            return;
        }
        $world = $player->getWorld();
        if (in_array($world->getDisplayName(), Values::SERVER_WORLDS, true)) {
            return;
        }
        $itemEntityCount = Main::getInstance()->getFunctions()->getIECount($world->getEntities());
        $max = 60;
        if ($itemEntityCount >= $max) {
            $this->sendMessage($player, "§cYou cannot drop more than §7$max §cItems on your island!");
            $event->cancel();
        }
    }

    public function onPlayerDeath(PlayerDeathEvent $event) : void {
        $player = $event->getPlayer();
        $event->setDeathMessage("");
        $player->setFlying(false);
        $player->setAllowFlight(false);
        Main::getInstance()->removePlayer($player);

        $cause = $player->getLastDamageCause();
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($damager instanceof Player) {
                Main::getInstance()->getEvFunctions()->killReward($damager, $player);
            }
        } else {
            if ($cause === null) {
                return;
            }
            $causes = [EntityDamageEvent::CAUSE_DROWNING, EntityDamageEvent::CAUSE_FALL, EntityDamageEvent::CAUSE_FIRE, EntityDamageEvent::CAUSE_FIRE_TICK, EntityDamageEvent::CAUSE_LAVA, EntityDamageEvent::CAUSE_STARVATION, EntityDamageEvent::CAUSE_BLOCK_EXPLOSION, EntityDamageEvent::CAUSE_ENTITY_EXPLOSION];
            if (in_array($cause->getCause(), $causes, true)) {
                $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
                if ($user === null) {
                    return;
                }
                $lastAttacker = $user->getLastAttacker();
                if ($lastAttacker instanceof Player && Main::getInstance()->isInCombat($player)) {
                    $user->setLastAttacker(null);
                    if ($lastAttacker->isOnline() && $lastAttacker->getName() !== $player->getName()) {
                        Main::getInstance()->getEvFunctions()->killReward($lastAttacker, $player);
                    }
                }
            }
            unset(Main::getInstance()->combat[$player->getName()]);
        }
    }

    public function onEntityTeleport(EntityTeleportEvent $event) : void {
        if (!$event->isCancelled()) {
            $entity = $event->getEntity();
            if ($entity instanceof Player) {
                if ($event->getTo()->getWorld()) {
                    $worldName = $event->getTo()->getWorld()->getDisplayName();
                    if ($worldName !== Values::PVP_WORLD) {
                        Main::getInstance()->removePlayer($entity);
                    }
                    if ($entity->getScale() !== 1) {
                        $entity->setScale(1);
                    }
                    Main::getInstance()->getParticleManager()->sendParticle([$entity], true, $event->getTo()->getWorld()->getDisplayName());
                    if ($entity->getAllowFlight() && !Main::getInstance()->hasOp($entity)) {
                        if (in_array($worldName, Values::SERVER_WORLDS, true)) {
                            $entity->setAllowFlight(false);
                            $entity->setFlying(false);
                            $this->sendMessage($entity, TextFormat::YELLOW . "Flying mode is disabled!");
                        }
                    }
                    if ($entity->isInvisible() && !Main::getInstance()->hasOp($entity)) {
                        $entity->setInvisible(false);
                        $entity->setNameTagVisible(false);
                        $this->sendMessage($entity, TextFormat::YELLOW . "You are not visible!");
                    }
                    if (Main::getInstance()->isRidingAPet($entity)) {
                        Main::getInstance()->getRiddenPet($entity)->throwRiderOff();
                    }
                    if ($worldName === Values::PVP_WORLD || $worldName === Values::LOBBY_WORLD) {
                        if (!Main::getInstance()->hasOp($entity)) {
                            $entity->setGamemode(GameMode::ADVENTURE);
                        }
                    } else {
                        if (!Main::getInstance()->hasOp($entity)) {
                            $entity->setGamemode(GameMode::SURVIVAL);
                        }
                    }
                    $pet = Main::getInstance()->getPetsFrom($entity);
                    if (!empty($pet)) {
                        foreach ($pet as $p) {
                            $p->updateVisibility($worldName !== Values::PVP_WORLD);
                        }
                    }
                    $island = Main::getInstance()->getIslandManager()->getOnlineIslandByWorld($worldName);
                    if ($island !== null) {
                        if ($island->isHelper($entity->getName()) || $island->hasARole($entity->getName())) {
                            if (!$island->hasPerm($entity->getName(), Permission::FREEZE)) {
                                if ($island->getFreeze()) {
                                    Main::getInstance()->removePlayerPet($entity);
                                    $entity->setNoClientPredictions();
                                    return;
                                }
                            }
                        }
                    }
                    if ($entity->hasNoClientPredictions()) {
                        $entity->setNoClientPredictions(false);
                    }
                }
            }
        }
    }

    public function onSignChange(SignChangeEvent $event) : void // late after common
    {
        $line = $event->getNewText()->getLines();
        $player = $event->getPlayer();

        if (($island = $this->plugin->getIslandManager()->getOnlineIslandByWorld($player->getWorld()->getDisplayName())) === null) return;

        if (!$island->isMember($player->getName()) && !$this->pl->hasOp($player)) {
            $event->cancel();
            return;
        }

        $str = implode("", $line);
        if ($str !== "" && !$this->pl->isStringValid($str)) {
            $event->setNewText(new SignText(["", "", "", ""]));
            $this->sendMessage($player, "Invalid character entered!");
            return;
        }

        $block = $event->getBlock();

        if (($block instanceof WallSign and $this->pl->isPrivateChestSign($block, $island->getOwner()))) {
            $event->cancel();
            return;
        }

        if ($this->ev->isPlayerShopSign($block->getPosition())) {
            $event->cancel();
            return;
        }

        if (strtolower($line[0]) === "pshop" and $line[1] !== "" and $line[2] != "" and $line[3] != "") {

            if (!($this->ev->getCloudForPlayer(strtolower($player->getName()))) instanceof ItemCloud) {
                $this->sendMessage($player, TF::RED . "You need to upload items on ItemCloud first by /ic upload to make player shops!");
                return;
            }

            $cost = $line[1];
            $amount = $line[3];

            if (!is_int((int) $cost) or !is_int((int) $amount)) {
                $this->sendMessage($player, TF::RED . "Please write the pshop Sign in right format i.e. \n" . TF::YELLOW . "pshop \n" . TF::YELLOW . "Cost \n" . TF::YELLOW . "ItemID \n" . TF::YELLOW . "Amount");
                return;
            }
            $cost = (int) $cost;
            $amount = (int) $amount;

            if ($cost < 1 or $amount < 1 or (int) $amount != $amount) {
                $this->sendMessage($player, "Invalid amount or cost value on sign!");
                return;
            }
            if ($cost > 1000000) {
                $this->sendMessage($player, "Invalid cost, price has to be less than 1 mil!");
                return;
            }
            $iname = trim($line[2]);
            $iname = str_replace(" ", "_", $iname);
            try {
                $item = StringToItemParser::getInstance()->parse($iname) ?? LegacyStringToItemParser::getInstance()->parse($iname);
            } catch (LegacyStringToItemParserException) {
                $this->sendMessage($player, "§cInvalid item specified on sign, Item name not found");
                return;
            }

            $user = $this->um->getOnlineUser($player->getName());
            if (!$user->removeMoney(5000)) {
                $this->sendMessage($player, "You don't have the required §65,000$ §cto make a player shop!");
                return;
            }

            $this->pl->shops[$block->getPosition()->getX() . ":" . $block->getPosition()->getY() . ":" . $block->getPosition()->getZ() . ":" . $block->getPosition()->getWorld()->getDisplayName()] = [
                "x"        => $block->getPosition()->getX(),
                "y"        => $block->getPosition()->getY(),
                "z"        => $block->getPosition()->getZ(),
                "level"    => $block->getPosition()->getWorld()->getDisplayName(),
                "owner"    => $player->getName(),
                "price"    => (int) $line[1],
                "item"     => $item->nbtSerialize(),
                "itemName" => $iname,
                "amount"   => (int) $line[3]
            ];

            $event->setNewText(new SignText(["§b{$item->getName()}", "§ex§c{$line[3]}", "§eCost: §6{$line[1]}$", TextFormat::GREEN . $player->getName()]));


            $this->sendMessage($player, "§eShop successfully created for §7(§ex§c{$line[3]}§7) §a{$item->getName()} §eat §6{$line[3]}$");
        } else if (strtolower($line[0]) == "[private]") {
            if ($block instanceof WallSign) {

                if (!$island->isOwner($player->getName())) {
                    $this->sendMessage($player, "You need to be the Island Owner to make private chests!");
                    return;
                }
                if (isset($this->pl->pchests[strtolower($player->getName())])) {
                    if (!isset($this->pl->pchests[strtolower($player->getName())][$block->getPosition()->getX() . ":" . $block->getPosition()->getY() . ":" . $block->getPosition()->getZ() . ":" . $block->getPosition()->getWorld()->getDisplayName()])) {
                        $no = count($this->pl->pchests[strtolower($player->getName())]);
                        if ($no >= 20) {
                            $this->sendMessage($player, "You cannot create more than 20 private chests!");
                            return;
                        }
                        if ($no >= 15) {
                            if (!$player->hasPermission("core.pchests.20")) {
                                $this->sendMessage($player, "You dont have permission to create more than 15 private chests! §eBuy private chests from §bStore.fallentech.io!");
                                return;
                            }
                        }
                        if ($no >= 10) {
                            if (!$player->hasPermission("core.pchests.15") && !$player->hasPermission("core.pchests.20")) {
                                $this->sendMessage($player, "You dont have permission to create more than 10 private chests! §eBuy private chests from §bStore.fallentech.io!");
                                return;
                            }
                        }
                        if ($no >= 5) {
                            if (!$player->hasPermission("core.pchests.10") && !$player->hasPermission("core.pchests.15") && !$player->hasPermission("core.pchests.20")) {
                                $this->sendMessage($player, "You dont have permission to create more than 5 private chests! §eBuy private chests from §bStore.fallentech.io!");
                                return;
                            }
                        }
                        if ($no >= 2) {
                            if (!$player->hasPermission("core.pchests.5") && !$player->hasPermission("core.pchests.10") && !$player->hasPermission("core.pchests.15") && !$player->hasPermission("core.pchests.20")) {
                                $this->sendMessage($player, "You dont have permission to create more than 2 private chests! §eBuy private chests from §bStore.fallentech.io!");
                                return;
                            }
                        }
                    }
                }
                $rearblock = Util::getRearBlock($block);
                if ($rearblock instanceof \pocketmine\block\Chest) {
                    $tile = $rearblock->getPosition()->getWorld()->getTile($rearblock->getPosition());
                    if ($tile instanceof Chest && $tile->isPaired()) {
                        $this->sendMessage($player, "Cannot make Double Chests private!");
                        return;
                    }
                    if (Util::getFrontBlock($rearblock) === $block && !$this->pl->isPrivateChest($rearblock, $player->getName())) { // we need this to confirm sign is placed on chest's front side, dont remove this
                        $this->pl->pchests[strtolower($player->getName())][$block->getPosition()->getX() . ":" . $block->getPosition()->getY() . ":" . $block->getPosition()->getZ() . ":" . $block->getPosition()->getWorld()->getDisplayName()] = ["x" => $block->getPosition()->getX(), "y" => $block->getPosition()->getY(), "z" => $block->getPosition()->getZ(), "level" => $block->getPosition()->getWorld()->getDisplayName()];
                        $event->setNewText(new SignText([TextFormat::GREEN . "[§bPrivate§a]", "", "", ""], glowing: true));
                        $this->sendMessage($player, "§ePrivate Chest successfully created!");
                    }
                }
            }
        }
    }

    /**
     * @param PlayerToggleSneakEvent $event
     */
    public function onSneak(PlayerToggleSneakEvent $event) : void {
        if ($event->isSneaking()) {
            $player = $event->getPlayer();
            if (in_array($player->getWorld()->getDisplayName(), Values::SERVER_WORLDS, true)) return;
            $growlevel = 0;
            $shrinklevel = 0;
            $type = null;
            $i = 0;
            foreach ($player->getArmorInventory()->getContents() as $armor) {
                if (!$armor instanceof Armor) break;
                if (!BaseEnchantment::hasEnchantment($armor, Bloom::$id)) break;
                $ench = BaseEnchantment::getEnchantment(Bloom::$id);
                if (!$ench instanceof Bloom) break;
                ++$i;
                $growlevel += $ench->getLevel();
            }
            if ($type instanceof Bloom && $i === 4) {
                if (isset($this->pl->bloom[$player->getName()])) {
                    $time = $this->pl->bloom[$player->getName()];
                    $max = 3;
                    if ((time() - $time) < $max) {
                        $this->sendMessage($player, "§cPlease dont spam it.");
                        return;
                    }
                }
                $player->setScale(1);
                $this->pl->bloom[$player->getName()] = time();
                $type->onSneak($player, $growlevel);
                return;
            }
            $i = 0;
            foreach ($player->getArmorInventory()->getContents() as $armor) {
                if (!$armor instanceof Armor) break;
                if (!BaseEnchantment::hasEnchantment($armor, Deflate::$id)) break;
                $ench = BaseEnchantment::getEnchantment(Deflate::$id);
                if (!$ench instanceof Deflate) break;
                ++$i;
                $shrinklevel += $ench->getLevel();
            }
            if ($type instanceof Deflate && $i === 4) {
                if (isset($this->pl->deflate[$player->getName()])) {
                    $time = $this->pl->deflate[$player->getName()];
                    $max = 3;
                    if ((time() - $time) < $max) {
                        $this->sendMessage($player, "§cPlease dont spam it.");
                        return;
                    }
                }
                $player->setScale(1);
                $this->pl->deflate[$player->getName()] = time();
                $type->onSneak($player, $shrinklevel);
            }
        }
    }

    //	SMALL CHECKS


    /**
     * @param EntitySpawnEvent $event
     */
    public function onEntitySpawn(EntitySpawnEvent $event) : void {
        $entity = $event->getEntity();
        if ($entity instanceof BasePet) {
            Main::getInstance()->updateNameTag($entity);
        } elseif ($entity instanceof Living && $entity instanceof SpawnerEntity) {
            StackFactory::addToClosestStack($entity);
        } elseif ($entity instanceof FallingBlock || $entity instanceof ExperienceOrb) {
            $entity->flagForDespawn();
        } elseif ($entity instanceof ItemEntity) {
            $entity->setDespawnDelay(30 * 20);
        }
    }


    public function onPlayerToggleGlide(PlayerToggleGlideEvent $event) : void {
        $player = $event->getPlayer();
        if (isset(Values::PVP_WORLDS[$player->getWorld()->getDisplayName()])) {
            $event->cancel();
        }
    }

    /**
     * @priority LOWEST
     */
    public function cancelRestartBlockBreaking(BlockBreakEvent $event) : void {
        if (Main::getInstance()->gandalf->rtime < 3) {
            $event->cancel();
        }
    }

    /**
     * @param RankChangeEvent $event
     */
    public function onRankChange(RankChangeEvent $event) : void {
        $player = $event->getPlayer();
        if ($player instanceof Player) {
            $player->setNameTag(Main::getInstance()->getEvFunctions()->getNametag($player, false));
        }
    }

    public function onLeavesDecay(LeavesDecayEvent $ev) : void {
        $ev->cancel();
    }

    public function onBlockDeath(BlockDeathEvent $event) : void {
        $event->cancel();
    }

    public function onEntityTrampleFarmland(EntityTrampleFarmlandEvent $event) : void {
        $event->cancel();
    }

    public function onBlockForm(BlockFormEvent $event) : void {
        $block = $event->getBlock();
        if ($block->getTypeId() == BlockTypeIds::LAVA) {
            $event->cancel();
            $block->getPosition()->getWorld()->setBlock($block->getPosition(), VanillaBlocks::AIR());
        }
    }

    /**
     * @param EntityCombustByBlockEvent $event
     */
    public function onEntityCombustByBlock(EntityCombustByBlockEvent $event) : void {
        if ($event->getEntity() instanceof Living && $event->getCombuster() instanceof Lava) { // no fire ticks if on island
            if (!in_array($event->getEntity()->getPosition()->getWorld()->getDisplayName(), Values::PVP_WORLDS, true)) {
                $event->cancel();
            }
        }
    }

    public function onDataPacketViolation(DataPacketReceiveEvent $event) : void {
        $packet = $event->getPacket();
        $name = $event->getOrigin()->getPlayer() === null ? $event->getOrigin()->getDisplayName() : $event->getOrigin()->getPlayer()->getName();
        if ($packet instanceof PacketViolationWarningPacket) {
            Main::getInstance()->getLogger()->info("Received PacketViolationWarningsPacket from $name: Packet Id: {$packet->getPacketId()}, Message: {$packet->getMessage()}");
        }
    }

    /**
     * @param PlayerKickEvent $event
     */
    public function onPlayerKick(PlayerKickEvent $event) : void {
        if ($event->getDisconnectReason() === Server::getInstance()->getLanguage()->translateString("kick.reason.cheat", ["%ability.flight"])) {
            $player = $event->getPlayer();
            if (Main::getInstance()->isRidingAPet($player) || Main::getInstance()->getEvFunctions()->hasPremiumRank($player->getName())) {
                $event->cancel();
            }
        }
    }

    /**
     * @param EntityRegainHealthEvent $event
     */
    public function onEntityRegainHealth(EntityRegainHealthEvent $event) : void {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            Main::getInstance()->getEvFunctions()->renderNameTag($entity);
        }
    }

    /**
     * @param PlayerItemConsumeEvent $event
     */
    public function onConsume(PlayerItemConsumeEvent $event) : void {
        $item = $event->getItem();
        if ($item->hasCustomName()) {
            $this->sendMessage($event->getPlayer(), TextFormat::RED . "Item not edible!");
            $event->cancel();
        }
    }


    /**
     * @param EntityDespawnEvent $event
     */
    public function onEntityDespawn(EntityDespawnEvent $event) : void {
        $entity = $event->getEntity();
        if ($entity instanceof BasePet) {
            Main::getInstance()->removePet($entity, false);
        }
    }

    /**
     * @param WorldLoadEvent $event
     */
    public function onWorldLoad(WorldLoadEvent $event) : void {
        $event->getWorld()->setAutoSave(true);
        $event->getWorld()->setDifficulty(World::DIFFICULTY_HARD);
    }

    /**
     * @param WorldUnloadEvent $event
     */
    public function onWorldUnload(WorldUnloadEvent $event) : void {
        $event->getWorld()->setAutoSave(true);
    }

    /**
     * @param PlayerCreationEvent $event
     */
    public function onPlayerCreation(PlayerCreationEvent $event) : void {
        $event->setPlayerClass(SBPlayer::class);
    }


    /**
     * @param EntityExplodeEvent $event
     */
    public function onTNTExplode(EntityExplodeEvent $event) : void {
        $event->cancel();
    }

    public function onEntityMotion(EntityMotionEvent $event) : void {
        $entity = $event->getEntity();
        if ($entity instanceof Living && !$entity instanceof Human && !$entity instanceof BasePet) {
            $event->cancel();
        }
    }

    /**
     * @param ProjectileLaunchEvent $event
     */
    public function onLaunch(ProjectileLaunchEvent $event) : void {
        $pro = $event->getEntity();
        if ($pro instanceof Snowball or $pro instanceof Egg or $pro instanceof EnderPearl or $pro instanceof SplashPotion) {
            $event->cancel();
        }
    }

    /**
     * @param ProjectileHitBlockEvent $event
     */
    public function onHit(ProjectileHitBlockEvent $event) : void {
        if ($event->getEntity() instanceof Arrow) {
            $event->getEntity()->flagForDespawn();
        }
    }


    /**
     * @param SBPlayer $sender
     * @param string   $message
     */
    public function sendMessage(Player $sender, string $message) : void {
        $sender->sendMessage(Values::FT_PREFIX . TextFormat::RED . $message);
    }
}
