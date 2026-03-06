<?php


namespace SkyBlock\enchants\touch;


use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Pickaxe;
use pocketmine\player\Player;
use SkyBlock\util\Values;

class Demolisher extends BaseTouchEnchant {

    static int $id = 182;

    public function isApplicableTo(Player $holder, int $enchlevel = 0, Block $block = null) : bool {
        return $block->getTypeId() === BlockTypeIds::BEDROCK and ($holder->getInventory()->getItemInHand() instanceof Pickaxe || $holder->getInventory()->getItemInHand() instanceof \SkyBlock\item\Pickaxe);
    }

    public function onActivation(Player $player, PlayerInteractEvent $ev, int $enchantmentlevel) : void {
        if ($player->getWorld()->getDisplayName() === Values::MINES_WORLD)
            return;
        $item = $player->getInventory()->getItemInHand();
        $block = $ev->getBlock();
        $island = $this->pl->getIslandManager()->getOnlineIslandByWorld($player->getWorld()->getDisplayName());
        if ($island->isAnOwner($player->getName())) {
            $player->getWorld()->setBlock($block->getPosition(), VanillaBlocks::AIR());
            $player->getInventory()->addItem(VanillaBlocks::BEDROCK()->asItem());
        }
        //            $player->getWorld()->useBreakOn($block->getPosition()->asVector3(), $item, $player, true);
    }
}