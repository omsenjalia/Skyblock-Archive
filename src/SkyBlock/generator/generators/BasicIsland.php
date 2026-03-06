<?php

namespace SkyBlock\generator\generators;

use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\object\OakTree;
use SkyBlock\generator\SkyBlockGenerator;

class BasicIsland extends SkyBlockGenerator {

    /** @var ChunkManager */
    protected ChunkManager $level;
    /** @var Random */
    protected Random $random;
    /** @var string */
    private string $name;

    /**
     * Initialize BasicIsland
     *
     * @param ChunkManager $level
     * @param Random       $random
     */
    public function init(ChunkManager $level, Random $random) : void {
        $this->level = $level;
        $this->random = $random;
        $this->name = "basic";
        $this->islandName = "Basic Island";
    }

    /**
     * Return generator name
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void {
        $chunk = $world->getChunk($chunkX, $chunkZ);
        if ($chunkX % 20 === 0 && $chunkZ % 20 === 0) {
            $grass = VanillaBlocks::GRASS()->getStateId();
            $dirt = VanillaBlocks::DIRT()->getStateId();
            $stone = VanillaBlocks::STONE()->getStateId();
            $bedrock = VanillaBlocks::BEDROCK()->getStateId();
            $air = VanillaBlocks::AIR()->getStateId();
            $water = VanillaBlocks::WATER()->getStateId();
            $cobb = VanillaBlocks::COBBLESTONE()->getStateId();
            $lava = VanillaBlocks::LAVA()->getStateId();
            $chest = VanillaBlocks::CHEST()->getStateId();
            $sign = VanillaBlocks::OAK_SIGN()->getStateId();
            for ($x = 0; $x < 7; $x++) {
                for ($z = 0; $z < 7; $z++) {
                    if ($x % 6 == 0 and $z % 6 == 0) continue;
                    $chunk->setBlockStateId($x, 5, $z, $grass);
                    if ($x == 5 and $z == 6 or $x == 6 and $z == 5 or $x == 6 and $z == 1 or $x == 1 and $z == 6 or $x == 1 and $z == 0 or $x == 0 and $z == 1 or $x == 5 and $z == 0 or $x == 0 and $z == 5)
                        continue;
                    else $chunk->setBlockStateId($x, 4, $z, $dirt);
                }
            }
            for ($x = 1; $x <= 5; $x++) {
                for ($z = 1; $z <= 5; $z++) {
                    for ($y = 1; $y <= 3; $y++) {
                        if (($x == 5 or $x == 1) and ($z == 1 or $z == 5)) continue;
                        if ($x == 5 and $z != $x) {
                            $chunk->setBlockStateId($x, $y, $z, $dirt);
                            continue;
                        }
                        if ($x == 1 and $z != $x) {
                            $chunk->setBlockStateId($x, $y, $z, $dirt);
                            continue;
                        }
                        if ($z == 1 and $x != $z) {
                            $chunk->setBlockStateId($x, $y, $z, $dirt);
                            continue;
                        }
                        if ($z == 5 and $x != $z) {
                            $chunk->setBlockStateId($x, $y, $z, $dirt);
                            continue;
                        }
                        $chunk->setBlockStateId($x, $y, $z, $stone);
                    }
                }
            }
            for ($x = 0; $x <= 6; $x++) {
                for ($z = 0; $z <= 6; $z++) {
                    for ($y = 1; $y <= 3; $y++) {
                        if ($x % 3 == 0 and $z % 3 == 0) {
                            if (($x == 6 or $x == 0) and ($z == 6 or $z == 0)) continue;
                            else $chunk->setBlockStateId($x, $y, $z, $dirt);
                        }
                    }
                }
            }
            for ($x = 2; $x < 5; $x++) {
                for ($z = 2; $z < 5; $z++) {
                    $chunk->setBlockStateId($x, 0, $z, $bedrock);
                }
            }
            $tree = new OakTree();
            $trans = $tree->getBlockTransaction($world, $chunkX * 16 + 3, 6, $chunkZ * 16 + 4, $this->random);
            $trans->apply();
            for ($y = 4; $y <= 5; $y++) {
                for ($x = 2; $x <= 5; $x++) {
                    $chunk->setBlockStateId($x, $y, 5, $air);
                }
            }
            $chunk->setBlockStateId(3, 3, 5, $air);
            $chunk->setBlockStateId(5, 4, 6, $dirt);
            $chunk->setBlockStateId(6, 4, 5, $dirt);
            $chunk->setBlockStateId(5, 3, 5, $dirt);
            $chunk->setBlockStateId(2, 5, 5, $water);
            $chunk->setBlockStateId(3, 5, 5, $cobb);
            $chunk->setBlockStateId(4, 5, 5, $cobb);
            $chunk->setBlockStateId(5, 5, 5, $lava);
            $chunk->setBlockStateId(4, 6, 4, $chest);
            $chunk->setBlockStateId(3, 6, 0, $sign);
            //$chunk->setX($chunkX);
            //$chunk->setZ($chunkZ);
            $world->setChunk($chunkX, $chunkZ, $chunk);
            $chunk->setPopulated(true);
            //$this->level->setChunk($chunkX, $chunkZ, $chunk);
            //$chunk->setPopulated();
        }
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ) : void {
        //TODO: Set Biome ID?
    }

}
