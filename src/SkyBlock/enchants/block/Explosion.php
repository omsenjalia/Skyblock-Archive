<?php


namespace SkyBlock\enchants\block;


use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\Pickaxe;
use pocketmine\player\Player;

class Explosion extends BaseBlockBreakEnchant {

    static int $id = 107;

    /** @var int[] */
    public const ORE = [BlockTypeIds::COBBLESTONE => 1, BlockTypeIds::COAL_ORE => 2, BlockTypeIds::COPPER_ORE => 3, BlockTypeIds::IRON_ORE => 4, BlockTypeIds::GOLD_ORE => 5, BlockTypeIds::DIAMOND_ORE => 6, BlockTypeIds::EMERALD_ORE => 7, BlockTypeIds::LAPIS_LAZULI_ORE => 8, BlockTypeIds::NETHER_QUARTZ_ORE => 9, BlockTypeIds::ANCIENT_DEBRIS => 10, BlockTypeIds::DEEPSLATE_COAL_ORE => 11, BlockTypeIds::DEEPSLATE_COPPER_ORE => 12, BlockTypeIds::DEEPSLATE_IRON_ORE => 13, BlockTypeIds::DEEPSLATE_LAPIS_LAZULI_ORE => 14, BlockTypeIds::DEEPSLATE_GOLD_ORE => 15, BlockTypeIds::DEEPSLATE_DIAMOND_ORE => 16, BlockTypeIds::DEEPSLATE_EMERALD_ORE => 17, BlockTypeIds::QUARTZ => 18, BlockTypeIds::NETHERITE => 19,];

    /**
     * @param Player $holder
     * @param int    $level
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder, int $level = 0) : bool {
        if ($holder->getInventory()->getItemInHand() instanceof Pickaxe or $holder->getInventory()->getItemInHand() instanceof \SkyBlock\item\Pickaxe) {
            return mt_rand(1, 15) === 1;
        }
        return false;
    }

    /**
     * @param Player          $player
     * @param BlockBreakEvent $ev
     * @param int             $enchantmentlevel
     */
    public function onActivation(Player $player, BlockBreakEvent $ev, int $enchantmentlevel) : void {
        if (isset(self::ORE[$ev->getBlock()->getTypeId()])) {
            if (!isset($this->pl->using[strtolower($player->getName())]) || $this->pl->using[strtolower($player->getName())] < time()) {
                $this->pl->mined[strtolower($player->getName())] = 0;
                $this->breakOres($ev->getBlock(), $player, null, $enchantmentlevel);
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
    public function breakOres(Block $block, Player $player, ?Block $oldblock, $level) {
        $item = $player->getInventory()->getItemInHand();
        for ($i = 0; $i <= 5; $i++) {
            if ($this->pl->mined[strtolower($player->getName())] > $level)
                break;
            $this->pl->using[strtolower($player->getName())] = time() + 1;
            $side = $block->getSide($i);
            if ($oldblock !== null) {
                if ($side->getPosition()->equals($oldblock->getPosition()))
                    continue;
            }
            if (!isset(self::ORE[$side->getTypeId()]))
                continue;
            $player->getWorld()->useBreakOn($side->getPosition()->asVector3(), $item, $player);
            $this->pl->mined[strtolower($player->getName())]++;
            $this->breakOres($side, $player, $block, $level);
        }
    }

}
