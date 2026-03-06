<?php

declare(strict_types=1);

namespace SkyBlock\generator\nether\populator;

use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\populator\Populator;
use function pow;

class GlowstoneSphere implements Populator {

    public const SPHERE_RADIUS = 3;

    public function populate(ChunkManager $world, int $chunkX, int $chunkZ, Random $random) : void {
        $world->getChunk($chunkX, $chunkZ);
        if ($random->nextRange(0, 10) !== 0) return;

        $x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
        $z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);

        $sphereY = 0;

        for ($y = 0; $y < 127; $y++) {
            if ($world->getBlockAt($x, $y, $z)->getTypeId() === BlockTypeIds::AIR) {
                $sphereY = $y;
            }
        }

        if ($sphereY < 80) {
            return;
        }

        $this->placeGlowstoneSphere($world, $random, new Vector3($x, $sphereY - $random->nextRange(2, 4), $z));
    }

    public function placeGlowStoneSphere(ChunkManager $world, Random $random, Vector3 $position) : void {
        $minX = $position->getX() - $this->getRandomRadius($random);
        $maxX = $position->getX() + $this->getRandomRadius($random);
        $minY = $position->getY() - $this->getRandomRadius($random);
        $maxY = $position->getY() + $this->getRandomRadius($random);
        $minZ = $position->getZ() - $this->getRandomRadius($random);
        $maxZ = $position->getZ() + $this->getRandomRadius($random);
        for ($x = $minX; $x < $maxX; ++$x) {
            $xsqr = ($position->getX() - $x) * ($position->getX() - $x);
            for ($y = $minY; $y < $maxY; ++$y) {
                $ysqr = ($position->getY() - $y) * ($position->getY() - $y);
                for ($z = $minZ; $z < $maxZ; ++$z) {
                    $zsqr = ($position->getZ() - $z) * ($position->getZ() - $z);
                    if (($xsqr + $ysqr + $zsqr) < (pow(2, $this->getRandomRadius($random)))) {
                        if ($random->nextRange(0, 4) !== 0) {
                            $world->setBlockAt($x, $y, $z, VanillaBlocks::GLOWSTONE());
                        }
                    }
                }
            }
        }
    }

    public function getRandomRadius(Random $random) : int {
        return $random->nextRange(self::SPHERE_RADIUS, self::SPHERE_RADIUS + 2);
    }

}