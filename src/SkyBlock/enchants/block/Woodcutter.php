<?php


namespace SkyBlock\enchants\block;


use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockTypeIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\Axe;
use pocketmine\player\Player;

class Woodcutter extends BaseBlockBreakEnchant {

    static int $id = 136;

    /**
     * @param Player $holder
     * @param int    $level
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder, int $level = 0) : bool {
        if ($holder->getInventory()->getItemInHand() instanceof Axe or $holder->getInventory()->getItemInHand() instanceof \SkyBlock\item\Axe) {
            return mt_rand(1, 5) === 1;
        } else   return false;
    }

    /**
     * @param Player          $player
     * @param BlockBreakEvent $ev
     * @param int             $enchantmentlevel
     */
    public function onActivation(Player $player, BlockBreakEvent $ev, int $enchantmentlevel) : void {
        if ($ev->getBlock()->getTypeId() == BlockTypeIds::OAK_LOG || $ev->getBlock()->getTypeId() == BlockTypeIds::BIRCH_LOG) {
            if (!isset($this->pl->using[strtolower($player->getName())]) || $this->pl->using[strtolower($player->getName())] < time()) {
                $this->pl->mined[strtolower($player->getName())] = 0;
                $this->sendActivation($player, "§bWoodcutter §aActivated!");
                $this->breakTree($ev->getBlock(), $player, null, $enchantmentlevel);
            }
        }
        $ev->setInstaBreak(true);
    }

    /**
     * @param Block      $block
     * @param Player     $player
     * @param Block|null $oldblock
     * @param            $level
     */
    public function breakTree(Block $block, Player $player, ?Block $oldblock, $level) : void {
        $item = $player->getInventory()->getItemInHand();
        for ($i = 0; $i <= 5; $i++) {
            if ($this->pl->mined[strtolower($player->getName())] > $this->getLevel($level)) break;
            $this->pl->using[$player->getName()] = time() + 1;
            $side = $block->getSide($i);
            if ($oldblock !== null) {
                if ($side->getPosition()->equals($oldblock->getPosition())) continue;
            }
            if ($side->getTypeId() !== BlockTypeIds::OAK_LOG && $side->getTypeId() !== BlockTypeIds::BIRCH_LOG) continue;
            $player->getWorld()->useBreakOn($side->getPosition(), $item, $player);
            $this->pl->mined[strtolower($player->getName())]++;
            $this->breakTree($side, $player, $block, $level);
        }
    }
}