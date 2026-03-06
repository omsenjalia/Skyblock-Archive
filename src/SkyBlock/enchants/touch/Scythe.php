<?php


namespace SkyBlock\enchants\touch;


use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockTypeIds;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Hoe;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Scythe extends BaseTouchEnchant {

    static int $id = 157;

    public function isApplicableTo(Player $holder, int $enchlevel = 0, Block $block = null) : bool {
        if ($block->getTypeId() == BlockTypeIds::GRASS || $block->getTypeId() == BlockTypeIds::DIRT) {
            if ($holder->getInventory()->getItemInHand() instanceof Hoe) {
                return mt_rand(1, 3) === 1;
            }
            return false;
        }
        return false;
    }

    public function onActivation(Player $player, PlayerInteractEvent $ev, int $enchantmentlevel) : void {
        if (!isset($this->pl->using[strtolower($player->getName())]) || $this->pl->using[strtolower($player->getName())] < time()) {
            $this->pl->mined[strtolower($player->getName())] = 0;
            $this->trimGrass($ev->getBlock(), $player, null, $enchantmentlevel);
        }
    }

    /**
     * @param Block      $block
     * @param Player     $player
     * @param Block|null $oldblock
     * @param            $level
     */
    public function trimGrass(Block $block, Player $player, ?Block $oldblock, $level) {
        $item = $player->getInventory()->getItemInHand();
        for ($i = 0; $i <= 5; $i++) {
            if ($this->pl->mined[strtolower($player->getName())] > $level) break;
            $this->pl->using[strtolower($player->getName())] = time() + 1;
            $side = $block->getSide($i);
            if ($oldblock !== null) {
                if ($side->getPosition()->equals($oldblock->getPosition()->asVector3())) continue;
            }
            if ($side->getTypeId() !== BlockTypeIds::GRASS and $side->getTypeId() !== BlockTypeIds::DIRT) continue;
            $player->getWorld()->useItemOn($block->getPosition()->asVector3(), $item, Facing::UP);
            $this->pl->mined[strtolower($player->getName())]++;
            $this->trimGrass($side, $player, $block, $level);
        }
    }

}