<?php

namespace SkyBlock;

use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\sound\ItemBreakSound;

class SBPlayer extends Player {

    private const MAX_REACH_DISTANCE_CREATIVE = 13;
    private const MAX_REACH_DISTANCE_SURVIVAL = 7;
    public int $lastBreak = -1;

    /**
     * @param Vector3 $pos
     *
     * @return bool
     */
    public function breakBlock(Vector3 $pos) : bool {
        $this->removeCurrentWindow();

        if ($this->canInteract($pos->add(0.5, 0.5, 0.5), $this->isCreative() ? self::MAX_REACH_DISTANCE_CREATIVE : self::MAX_REACH_DISTANCE_SURVIVAL)) {
            $this->broadcastAnimation(new ArmSwingAnimation($this), $this->getViewers());
            $this->stopBreakBlock($pos);
            $item = $this->inventory->getItemInHand();
            $oldItem = clone $item;
            if ($this->getWorld()->useBreakOn($pos, $item, $this, false)) { // added to remove particles
                if ($this->hasFiniteResources() && !$item->equalsExact($oldItem) && $oldItem->equalsExact($this->inventory->getItemInHand())) {
                    if ($item instanceof Durable && $item->isBroken()) {
                        $this->broadcastSound(new ItemBreakSound());
                    }
                    $this->inventory->setItemInHand($item);
                }
                $this->hungerManager->exhaust(0.025, PlayerExhaustEvent::CAUSE_MINING);
                return true;
            }
        } else {
            $this->logger->debug("Cancelled block break at $pos due to not currently being interactable");
        }

        return false;
    }

    public function breakCheck(BlockBreakEvent $event) : bool {
        $target = $event->getBlock();
        $player = $event->getPlayer();
        $item = $event->getItem();

        $breakTime = $target->getBreakInfo()->getBreakTime($item);

        if ($player->isCreative() && $breakTime > 0.15) {
            $breakTime = 0.15;
        }

        if (($haste = $player->getEffects()->get(VanillaEffects::HASTE())) !== null) {
            $breakTime *= 1 - (0.2 * ($haste->getEffectLevel() + 1));
        }

        if (($miningFatigue = $player->getEffects()->get(VanillaEffects::MINING_FATIGUE())) !== null) {
            $breakTime *= 1 - (0.3 * ($miningFatigue->getEffectLevel() + 1));
        }

        $efficiency = $item->getEnchantment(VanillaEnchantments::EFFICIENCY());

        if ($efficiency !== null && $efficiency->getLevel() > 0) {
            $breakTime *= 1 - (0.3 * $efficiency->getLevel());
        }

        $breakTime -= 0.15;

        if ($player->isSurvival() && !$target->getBreakInfo()->isBreakable()) {
            return false;
        }
        $time = floor(microtime(true) * 1000);
        if (!$player->isCreative() && ($this->lastBreak + $breakTime * 1000) > $time) {
            return false;
        }

        $this->lastBreak = (int) floor(microtime(true) * 1000);

        return true;
    }

}