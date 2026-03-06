<?php


namespace SkyBlock\enchants\sword;


use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use SkyBlock\enchants\BaseEnchantment;

class Disarm extends BaseMeleeEnchant {

    static int $id = 114;

    /**
     * @param Player $holder
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder) : bool {
        return mt_rand(1, 50) === 1;
    }

    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $hitem = $attacker->getInventory()->getItemInHand();
        if (!BaseEnchantment::hasAnyEnchantment($hitem, [DisarmProtection::$id, Serpent::$id, Brawler::$id, Wizardly::$id, Disorder::$id, Thunderbolt::$id, Disarmor::$id, Detonate::$id, Potshot::$id, Smasher::$id, SoulSnatcher::$id])) {
            $attacker->getInventory()->setItemInHand(VanillaItems::AIR());
            $attacker->getWorld()->dropItem($attacker->getPosition()->asVector3(), $hitem);
            $this->sendActivation($player, "§bDisarm §aActivated!");
            $this->sendActivation($attacker, "§cYOU HAVE BEEN DISARMED!");
        } else {
            $this->sendActivation($player, "§bDisarm §cDeactivated! Player has Disarm Protection or an exclusive enchant!");
        }
    }

}