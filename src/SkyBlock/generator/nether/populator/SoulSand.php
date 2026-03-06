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

class SoulSand implements Populator {

    public function populate(ChunkManager $world, int $chunkX, int $chunkZ, Random $random) : void {
        if ($random->nextRange(0, 6) !== 0) return;

        $x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
        $z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);

        $sphereY = 0;

        for ($y = 45; $y > 0; $y--) {
            if ($world->getBlockAt($x, $y, $z)->getTypeId() === BlockTypeIds::AIR) {
                $sphereY = $y;
            }
        }

        if ($sphereY - 3 < 2) {
            return;
        }

        if ($world->getBlockAt($x, $sphereY - 3, $z)->getTypeId() !== BlockTypeIds::NETHERRACK) {
            return;
        }

        $this->placeSoulSand($world, $random, new Vector3($x, $sphereY - $random->nextRange(2, 4), $z));
    }

    public function placeSoulSand(ChunkManager $world, Random $random, Vector3 $position) : void {
        $radiusX = $random->nextRange(8, 15);
        $radiusZ = $random->nextRange(8, 15);
        $radiusY = $random->nextRange(5, 8);

        $minX = $position->getX() - $radiusX;
        $maxX = $position->getX() + $radiusX;
        $minY = $position->getY() - $radiusY;
        $maxY = $position->getY() + $radiusY;
        $minZ = $position->getZ() - $radiusZ;
        $maxZ = $position->getZ() + $radiusZ;

        for ($x = $minX; $x < $maxX; ++$x) {
            $xsqr = ($position->getX() - $x) * ($position->getX() - $x);
            for ($y = $minY; $y < $maxY; ++$y) {
                $ysqr = ($position->getY() - $y) * ($position->getY() - $y);
                for ($z = $minZ; $z < $maxZ; ++$z) {
                    $zsqr = ($position->getZ() - $z) * ($position->getZ() - $z);
                    if (($xsqr + $ysqr + $zsqr) < (pow(2, $random->nextRange(3, 6)))) {
                        if ($world->getBlockAt($x, $y, $z)->getTypeId() === BlockTypeIds::NETHERRACK) {
                            $world->setBlockAt($x, $y, $z, VanillaBlocks::SOUL_SAND());
                            if ($random->nextRange(0, 3) == 3 && $world->getBlockAt($x, $y + 1, $z)->getTypeId() === BlockTypeIds::AIR) {
                                $world->setBlockAt($x, $y + 1, $z, VanillaBlocks::NETHER_WART());
                            }
                        }
                    }
                }
            }
        }
    }

}