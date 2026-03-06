<?php


namespace SkyBlock\tiles;


use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\tile\Chest;
use pocketmine\block\tile\Nameable;
use pocketmine\block\tile\NameableTrait;
use pocketmine\block\tile\Spawnable;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\Facing;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\Server;
use SkyBlock\block\AutoMiner;
use SkyBlock\command\Functions;
use SkyBlock\Main;
use function array_key_exists;

class AutoMinerTile extends Spawnable implements Nameable {
    use NameableTrait;

    public const TAG_LEVEL = "Level";
    public const TAG_FORTUNE = "Fortune";
    public const TAG_FORTUNE_LEVEL = "Fortune_Level";
    public const MAX_LEVEL = 4;
    public const MAX_FORTUNE_LEVEL = 15;

    /** @var int */
    public int $delay = 200, $level1 = 0, $fortune = 0, $flevel = 1;
    public array $blocks
        = [
            BlockTypeIds::DIAMOND_ORE                => true,
            BlockTypeIds::GOLD_ORE                   => true,
            BlockTypeIds::EMERALD_ORE                => true,
            BlockTypeIds::NETHERRACK                 => true,
            BlockTypeIds::LAPIS_LAZULI_ORE           => true,
            BlockTypeIds::IRON_ORE                   => true,
            BlockTypeIds::COPPER_ORE                 => true,
            BlockTypeIds::COAL_ORE                   => true,
            BlockTypeIds::COBBLESTONE                => true,
            BlockTypeIds::NETHER_QUARTZ_ORE          => true,
            BlockTypeIds::QUARTZ                     => true,
            BlockTypeIds::DEEPSLATE_COAL_ORE         => true,
            BlockTypeIds::DEEPSLATE_COPPER_ORE       => true,
            BlockTypeIds::DEEPSLATE_IRON_ORE         => true,
            BlockTypeIds::DEEPSLATE_GOLD_ORE         => true,
            BlockTypeIds::DEEPSLATE_LAPIS_LAZULI_ORE => true,
            BlockTypeIds::DEEPSLATE_DIAMOND_ORE      => true,
            BlockTypeIds::DEEPSLATE_EMERALD_ORE      => true,
            BlockTypeIds::ANCIENT_DEBRIS             => true,
            BlockTypeIds::NETHERITE                  => true,
        ];

    public function onUpdate() : bool {
        if ($this->level1 === 0) $this->close();
        if ($this->closed) return false;
        $block = $this->getBlock();
        if (!$block instanceof AutoMiner) return false;
        $inst = Main::getInstance();
        if (!isset($inst->autominer[$this->getPosition()->getWorld()->getDisplayName()])) {
            if ($this->delay <= 0) {
                $this->delay = $this->getDelayByLevel($this->level1);
                $down = $this->getPosition()->getWorld()->getBlock($this->getBlock()->getSide(Facing::DOWN)->getPosition()->add(0, -1, 0));
                $up = $this->getPosition()->getWorld()->getTile($this->getBlock()->getSide(Facing::UP)->getPosition());
                if (array_key_exists($down->getTypeId(), $this->blocks) and $up instanceof Chest) {
                    $item = VanillaItems::DIAMOND_PICKAXE();
                    if ($this->fortune == 1) {
                        if ($this->flevel > self::MAX_FORTUNE_LEVEL) $this->flevel = self::MAX_FORTUNE_LEVEL;
                        if ($this->flevel < 1) $this->flevel = 1;
                        $item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::FORTUNE), $this->flevel));
                    }
                    $affectedBlocks = $down->getAffectedBlocks();
                    $drops = array_merge(...array_map(function(Block $down) use ($item) : array {
                        return $down->getDrops($item);
                    }, $affectedBlocks
                                            )
                    );
                    $inv = $up->getInventory();
                    $smelt = [
                        BlockTypeIds::GOLD_ORE             => VanillaItems::GOLD_INGOT(),
                        BlockTypeIds::IRON_ORE             => VanillaItems::IRON_INGOT(),
                        BlockTypeIds::COPPER_ORE           => VanillaItems::COPPER_INGOT(),
                        BlockTypeIds::DEEPSLATE_GOLD_ORE   => VanillaItems::GOLD_INGOT(),
                        BlockTypeIds::DEEPSLATE_IRON_ORE   => VanillaItems::IRON_INGOT(),
                        BlockTypeIds::DEEPSLATE_COPPER_ORE => VanillaItems::COPPER_INGOT()
                    ];
                    foreach ($drops as $drop) {
                        /** @var Item $drop */
                        if ($this->fortune > 1 and $drop->getTypeId() != VanillaBlocks::COBBLESTONE()->asItem()->getTypeId() and $drop->getTypeId() != VanillaBlocks::ANCIENT_DEBRIS()->asItem()->getTypeId() and $drop->getTypeId() != VanillaBlocks::NETHERITE()->asItem()->getTypeId()) {
                            if (!str_contains(strtolower($block->getName()), "deepslate")) {
                                $count = mt_rand(1, $this->fortune);
                                $drop->setCount($count);
                            } else {
                                /** fortune item drops for normal ores and deepslate ores done here */
                                $count = mt_rand(1, $this->fortune);
                                if ($drop->getTypeId() === BlockTypeIds::QUARTZ) {
                                    $count = $count * 4;
                                    $drop = VanillaItems::NETHER_QUARTZ();
                                }
                                $drop->setCount((int) ceil($count * 1.8));
                            }
                        }
                        if (isset($smelt[$drop->getTypeId()])) $drop = $smelt[$drop->getTypeId()]->setCount($drop->getCount());
                        if ($inv->canAddItem($drop)) {
                            $inv->addItem($drop);
                        } else {
                            return true;
                        }
                    }
                    $this->getPosition()->getWorld()->setBlock($down->getPosition()->asVector3(), VanillaBlocks::AIR());
                    $blocks = [
                        BlockTypeIds::COBBLESTONE       => 0,
                        BlockTypeIds::COAL_ORE          => 1,
                        BlockTypeIds::COPPER_ORE        => 2,
                        BlockTypeIds::IRON_ORE          => 3,
                        BlockTypeIds::LAPIS_LAZULI_ORE  => 4,
                        BlockTypeIds::GOLD_ORE          => 5,
                        BlockTypeIds::DIAMOND_ORE       => 6,
                        BlockTypeIds::EMERALD_ORE       => 7,
                        BlockTypeIds::NETHER_QUARTZ_ORE => 8,
                        BlockTypeIds::ANCIENT_DEBRIS    => 9,

                        BlockTypeIds::DEEPSLATE_COAL_ORE         => 10,
                        BlockTypeIds::DEEPSLATE_COPPER_ORE       => 11,
                        BlockTypeIds::DEEPSLATE_IRON_ORE         => 12,
                        BlockTypeIds::DEEPSLATE_LAPIS_LAZULI_ORE => 13,
                        BlockTypeIds::DEEPSLATE_GOLD_ORE         => 14,
                        BlockTypeIds::DEEPSLATE_DIAMOND_ORE      => 15,
                        BlockTypeIds::DEEPSLATE_EMERALD_ORE      => 16,
                        BlockTypeIds::QUARTZ                     => 17,
                        BlockTypeIds::NETHERITE                  => 18,
                    ];
                    if (isset($blocks[$down->getTypeId()])) {
                        if (($island = $inst->getIslandManager()->getOnlineIslandByWorld($this->getPosition()->getWorld()->getDisplayName())) !== null) {
                            $receiver = $island->getReceiver();
                            $player = Server::getInstance()->getPlayerExact($receiver);
                            if (!$player instanceof Player) {
                                if (($player = $island->getRandomOnlineCoOwner()) === null) return true;
                                $receiver = $player->getName();
                            }
                            if (($user = $inst->getUserManager()->getOnlineUser($receiver)) !== null && $island->isAnOwner($receiver)) {
                                $user->addMana($blocks[$down->getTypeId()]);
                                $island->setPoints($blocks[$down->getTypeId()]);
                                Functions::safeXPAdd($user, $down->getXpDropForTool($item));
                            }
                        }
                    }
                }

            } else {
                --$this->delay;
            }
        }
        // main code goes here
        return true;
    }

    public function getName() : string {
        return "AutoMiner";
    }

    public function getDefaultName() : string {
        return "AutoMiner";
    }

    public function readSaveData(CompoundTag $nbt) : void {
        if ($nbt->getInt(self::TAG_LEVEL, null) === null) $this->close();

        $this->level1 = $nbt->getInt(self::TAG_LEVEL, $this->level1);
        $this->fortune = $nbt->getInt(self::TAG_FORTUNE, $this->fortune);
        $this->flevel = $nbt->getInt(self::TAG_FORTUNE_LEVEL, $this->flevel);

        $this->delay = $this->getDelayByLevel($this->level1);
        //$this->scheduleUpdate(); // schedules onUpdate
    }

    public function getDelayInSeconds() : int {
        return (int) ($this->getDelayByLevel($this->level1) / 20);
    }

    public function getDelayByLevel(int $level) : int {
        if ($level == 1) return 15 * 20; // 15 secs
        elseif ($level == 2) return 10 * 20;
        elseif ($level == 3) return 8 * 20;
        elseif ($level == 4) return 4 * 20;
        else return 200;
    }

    public function setDelay(int $delay) : void {
        $this->delay = $delay;
    }

    protected function writeSaveData(CompoundTag $nbt) : void {
        $this->applyBaseNBT($nbt);
    }

    private function applyBaseNBT(CompoundTag $nbt) : void {
        $nbt->setInt(self::TAG_LEVEL, $this->level1);
        $nbt->setInt(self::TAG_FORTUNE, $this->fortune);
        $nbt->setInt(self::TAG_FORTUNE_LEVEL, $this->flevel);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt) : void {
        $this->applyBaseNBT($nbt);
    }

}