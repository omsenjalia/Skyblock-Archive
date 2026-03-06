<?php


namespace SkyBlock\enchants\block;


use pocketmine\block\BlockToolType;
use pocketmine\block\BlockTypeIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\StringToItemParser;
use pocketmine\item\TieredTool;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class Lucky extends BaseBlockBreakEnchant {

    static int $id = 138;

    /** @var int[] */
    public const ORE_TIER
        = [
            BlockTypeIds::STONE                      => 1,
            BlockTypeIds::COAL_ORE                   => 2,
            BlockTypeIds::IRON_ORE                   => 3,
            BlockTypeIds::LAPIS_LAZULI_ORE           => 4,
            BlockTypeIds::GOLD_ORE                   => 5,
            BlockTypeIds::DIAMOND_ORE                => 6,
            BlockTypeIds::EMERALD_ORE                => 7,
            BlockTypeIds::DEEPSLATE_COAL_ORE         => 2,
            BlockTypeIds::DEEPSLATE_IRON_ORE         => 3,
            BlockTypeIds::DEEPSLATE_LAPIS_LAZULI_ORE => 4,
            BlockTypeIds::DEEPSLATE_GOLD_ORE         => 5,
            BlockTypeIds::DEEPSLATE_DIAMOND_ORE      => 6,
            BlockTypeIds::DEEPSLATE_EMERALD_ORE      => 7,
        ];

    public function getLevel(int $level = 1) : int {
        return (($int = ceil($level / 2)) > 7) ? 8 : $int;
    }

    /**
     * @param Player $holder
     * @param int    $level
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder, int $level = 0) : bool {
        return mt_rand(0, 100) <= 5 * $this->getLevel($level);
    }

    /**
     * @param Player          $player
     * @param BlockBreakEvent $ev
     * @param int             $enchantmentlevel
     */
    public function onActivation(Player $player, BlockBreakEvent $ev, int $enchantmentlevel) : void {
        $item = $ev->getItem();
        if (!$item instanceof TieredTool or !$item instanceof \SkyBlock\item\TieredTool) return;
        if ($ev->getBlock()->getBreakInfo()->getToolType() !== BlockToolType::PICKAXE) return;

        if (!isset(self::ORE_TIER[$ev->getBlock()->getTypeId()])) return;

        $tier = self::ORE_TIER[$ev->getBlock()->getTypeId()];
        if (($tierkey = array_search($tier + 1, self::ORE_TIER, true)) === false) return;

        $drops = $ev->getDrops();
        foreach ($drops as $key => $drop) {
            foreach ($ev->getBlock()->getDrops($item) as $originaldrop) {
                unset($drops[$key]);
                //                foreach (Main::getInstance()->getLegacyItemMap()->getItemFromLegacyId($tierkey, $originaldrop->getStateId())->getDrops(Main::getInstance()->getLegacyItemMap()->getItemFromLegacyId(ItemTypeIds::DIAMOND_PICKAXE)) as $newdrop) {
                foreach (StringToItemParser::getInstance()->parse($tierkey)->getBlock()->getDrops(VanillaItems::DIAMOND_PICKAXE()) as $newdrop) {
                    if ($newdrop->getTypeId() != Smelting::convertItem($newdrop)->getTypeId()) {
                        $drops = [StringToItemParser::getInstance()->parse((string) Smelting::convertItem($newdrop))];
                    } else {
                        $drops = [StringToItemParser::getInstance()->parse((string) $newdrop->getTypeId())];
                    }
                }
                $ev->setDrops($drops);
                break;
            }
        }
    }

}