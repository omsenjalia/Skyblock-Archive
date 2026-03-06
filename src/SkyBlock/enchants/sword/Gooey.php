<?php


namespace SkyBlock\enchants\sword;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

class Gooey extends BaseMeleeEnchant {

    static int $id = 103;

    public function isApplicableTo(Player $holder) : bool {
        return mt_rand(1, 60) === 1;
    }

    /**
     * @param Player            $player
     * @param Player            $attacker
     * @param EntityDamageEvent $ev
     * @param int               $enchantmentlevel
     */
    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $this->sendActivation($attacker, "§cStruck by §bGooey §cEnchant!");
        $this->sendActivation($player, "§bGooey §aActivated!");
        $attacker->setNoClientPredictions();
        $this->pl->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($attacker) : void {
            $attacker->setNoClientPredictions(false);
        }
                                                       ), 0.1 * 20
        );
    }

}