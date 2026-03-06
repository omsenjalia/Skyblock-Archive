<?php

namespace SkyBlock\generator\end;

use customiesdevs\customies\block\CustomiesBlockFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\biome\BiomeRegistry;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\world\generator\Generator;
use pocketmine\world\generator\noise\Simplex;
use pocketmine\world\generator\object\OreType;
use pocketmine\world\generator\populator\Ore;
use pocketmine\world\generator\populator\Populator;
use SkyBlock\generator\BiomeIds;

class EndGenerator extends Generator {

    /** @var Populator[] */
    private array $populators = [];

    private int $lavaHeight = 32;
    private int $emptyHeight = 64;

    private int $emptyAmplitude = 1;

    private float $density = 0.5;

    /** @var Populator[] */
    private array $generationPopulators = [];

    private Simplex $noiseBase;

    public function __construct(int $seed, string $preset) {
        parent::__construct($seed, $preset);

        $this->random->setSeed($seed);
        $this->noiseBase = new Simplex($this->random, 4, 1 / 4, 1 / 64);
        $this->random->setSeed($seed);

        $ores = new Ore();
        $ores->setOreTypes([
                               new OreType(CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_coal_ore"), VanillaBlocks::END_STONE(), 20, 20, 0, 128),
                               new OreType(CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_copper_ore"), VanillaBlocks::END_STONE(), 20, 20, 0, 128),
                               new OreType(CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_zinc_ore"), VanillaBlocks::END_STONE(), 20, 18, 0, 128),
                               new OreType(CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_iron_ore"), VanillaBlocks::END_STONE(), 20, 16, 25, 128),
                               new OreType(CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_gold_ore"), VanillaBlocks::END_STONE(), 16, 16, 25, 128),
                               new OreType(CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_redstone_ore"), VanillaBlocks::END_STONE(), 16, 16, 75, 128),
                               new OreType(CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_lapis_ore"), VanillaBlocks::END_STONE(), 16, 16, 75, 128),
                               new OreType(CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_diamond_ore"), VanillaBlocks::END_STONE(), 16, 16, 75, 128),
                               new OreType(CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_emerald_ore"), VanillaBlocks::END_STONE(), 12, 14, 110, 128),
                               new OreType(CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_topaz_ore"), VanillaBlocks::END_STONE(), 12, 14, 110, 128),
                               new OreType(CustomiesBlockFactory::getInstance()->get("fallentech:end_stone_quartz_ore"), VanillaBlocks::END_STONE(), 10, 14, 110, 128),

                           ]
        );

        $this->populators[] = $ores;
    }

    public function init(ChunkManager $world, Random $random) : void {

    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void {
        $this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->seed);

        $noise = $this->noiseBase->getFastNoise3D(16, 128, 16, 4, 8, 4, $chunkX * 16, 0, $chunkZ * 16);

        /** @var Chunk $chunk */
        $chunk = $world->getChunk($chunkX, $chunkZ);

        $bedrock = VanillaBlocks::BEDROCK()->getStateId();
        $end_stone = VanillaBlocks::END_STONE()->getStateId();

        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {
                $chunk->setBiomeId($x, 30, $z, BiomeIds::THE_END);

                for ($y = 0; $y < 128; ++$y) {
                    if ($y === 0 or $y === 127) {
                        $chunk->setBlockStateId($x, $y, $z, $bedrock);
                        continue;
                    }
                    $noiseValue = (abs($this->emptyHeight - $y) / $this->emptyHeight) * $this->emptyAmplitude - $noise[$x][$z][$y];
                    $noiseValue -= 1 - $this->density;

                    if ($noiseValue > 0) {
                        $chunk->setBlockStateId($x, $y, $z, $end_stone);
                    }
                }
            }
        }

        foreach ($this->generationPopulators as $populator) {
            $populator->populate($world, $chunkX, $chunkZ, $this->random);
        }
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void {
        $this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->seed);
        foreach ($this->populators as $populator) {
            $populator->populate($world, $chunkX, $chunkZ, $this->random);
        }

        $biome = BiomeRegistry::getInstance()->getBiome(BiomeIds::THE_END);
        $biome->populateChunk($world, $chunkX, $chunkZ, $this->random);
    }
}