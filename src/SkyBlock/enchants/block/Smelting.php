<?php


namespace SkyBlock\enchants\block;


use pocketmine\block\BlockTypeIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\Pickaxe;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class Smelting extends BaseBlockBreakEnchant {

    static int $id = 115;

    /**
     * @param Player $holder
     * @param int    $level
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder, int $level = 0) : bool {
        return ($holder->getInventory()->getItemInHand() instanceof Pickaxe or $holder->getInventory()->getItemInHand() instanceof \SkyBlock\item\Pickaxe);
    }

    public static function convertItem(Item $item) : Item {
        return match ($item->getTypeId()) {
            ItemTypeIds::RAW_COPPER => VanillaItems::COPPER_INGOT()->setCount($item->getCount()),
            ItemTypeIds::RAW_IRON => VanillaItems::IRON_INGOT()->setCount($item->getCount()),
            ItemTypeIds::RAW_GOLD => VanillaItems::GOLD_INGOT()->setCount($item->getCount()),
            BlockTypeIds::ANCIENT_DEBRIS => VanillaItems::NETHERITE_SCRAP()->setCount($item->getCount()),
            default => $item,
        };
    }

    /**
     * @param Player          $player
     * @param BlockBreakEvent $ev
     * @param int             $enchantmentlevel
     */
    public function onActivation(Player $player, BlockBreakEvent $ev, int $enchantmentlevel) : void {
        $drops = [];
        foreach ($ev->getDrops() as $drop) {
            $drops[] = $this->convertItem($drop);
        }
        $ev->setDrops($drops);
    }

}