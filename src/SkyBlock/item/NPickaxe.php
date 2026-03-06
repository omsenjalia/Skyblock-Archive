<?php


namespace SkyBlock\item;


use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\player\Player;
use SkyBlock\util\Lore;
use SkyBlock\util\Values;

class NPickaxe extends \SkyBlock\item\Pickaxe {

    /**
     * @param Block $block
     * @param array $returnedItems
     *
     * @return bool
     */
    public function onDestroyBlock(Block $block, array &$returnedItems) : bool {
        $count = (int) Lore::getLoreInfo($this->getLore(), Values::BLOCKS_BROKEN_LORE, Lore::BLOCKS_BROKEN_STR);
        Lore::setLoreInfo($this, Values::BLOCKS_BROKEN_LORE, Lore::BLOCKS_BROKEN_STR . number_format(++$count));
        return parent::onDestroyBlock($block, $returnedItems);
    }

    /**
     * @param Entity $victim
     * @param array  $returnedItems
     *
     * @return bool
     */
    public function onAttackEntity(Entity $victim, array &$returnedItems) : bool {
        if ($victim->getHealth() <= 0 && $victim instanceof Player) {
            $count = (int) Lore::getLoreInfo($this->getLore(), Values::PLAYERS_KILLED_LORE, Lore::KILL_STR);
            Lore::setLoreInfo($this, Values::PLAYERS_KILLED_LORE, Lore::KILL_STR . number_format(++$count));
        }
        return parent::onAttackEntity($victim, $returnedItems);
    }
}
