<?php


namespace SkyBlock\enchants\block;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\Durable;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\Pickaxe;
use pocketmine\player\Player;
use SkyBlock\util\Lore;
use SkyBlock\util\Values;

class Insurance extends BaseBlockBreakEnchant {

    static int $id = 192;

    /**
     * @param Player $holder
     * @param int    $level
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder, int $level = 0) : bool {
        $item = $holder->getInventory()->getItemInHand();
        if ($item instanceof Pickaxe or $item instanceof \SkyBlock\item\Pickaxe) {
            return ($item->getMaxDurability() - $item->getDamage()) <= 10;
        }
        return false;
    }

    public function onActivation(Player $player, BlockBreakEvent $ev, int $enchantmentlevel) : void {
        $ev->cancel();
        $hand = $player->getInventory()->getItemInHand();
        $new = 1;
        $max = Values::MAX_DEFAULT_FIX;
        if (($fixlore = Lore::getLoreInfo($hand->getLore(), Values::FIX_LORE, Lore::FIX_STR)) !== null) {
            $data = explode("/", $fixlore);
            [$cur, $max] = $data;
            $cur = (int) $cur;
            if ($cur >= $max) {
                $this->sendActivation($player, "§bInsurance §cDeactivated! §4Exceeding Fix limit!");
                return;
            }
            $new = $cur + 1;
        }
        $items = $player->getInventory()->getContents();
        foreach ($items as $slot => $item) {
            if ($item->hasCustomName() && $item->getTypeId() == ItemTypeIds::PRISMARINE_SHARD) {
                $spaces = explode(" ", $item->getCustomName());
                if ($hand instanceof Durable and $spaces[1] == 'Fixer' and $spaces[2] == 'Scroll') {
                    $hand->setDamage(0);
                    Lore::setLoreInfo($hand, Values::FIX_LORE, Lore::FIX_STR . "$new/$max");
                    $player->getInventory()->setItemInHand($hand);
                    $item->setCount($item->getCount() - 1);
                    $player->getInventory()->setItem($slot, $item);
                    $this->sendActivation($player, "§bInsurance §aActivated! §cUsed x1 Fixer scroll");
                    break;
                }
            }
        }
    }

}