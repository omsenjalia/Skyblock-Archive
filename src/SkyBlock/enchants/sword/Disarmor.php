<?php


namespace SkyBlock\enchants\sword;


use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Armor;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use SkyBlock\enchants\armor\DisarmorProtection;
use SkyBlock\enchants\BaseEnchantment;

class Disarmor extends BaseMeleeEnchant {

    static int $id = 180;

    /**
     * @param Player $holder
     *
     * @return bool
     */
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
        $i = mt_rand(1, 4);
        $aitem = match ($i) {
            1 => $attacker->getArmorInventory()->getHelmet(),
            2 => $attacker->getArmorInventory()->getChestplate(),
            3 => $attacker->getArmorInventory()->getLeggings(),
            4 => $attacker->getArmorInventory()->getBoots(),
        };
        if (!BaseEnchantment::hasEnchantment($aitem, DisarmorProtection::$id)) {
            switch ($i) {
                case 1:
                    $attacker->getArmorInventory()->setHelmet(VanillaItems::AIR());
                    $this->sendActivation($attacker, "§cYOUR HELMET HAS BEEN DISARMORED!");
                    break;
                case 2:
                    $attacker->getArmorInventory()->setChestplate(VanillaItems::AIR());
                    $this->sendActivation($attacker, "§cYOUR CHESTPLATE HAS BEEN DISARMORED!");
                    break;
                case 3:
                    $attacker->getArmorInventory()->setLeggings(VanillaItems::AIR());
                    $this->sendActivation($attacker, "§cYOUR LEGGINGS HAVE BEEN DISARMORED!");
                    break;
                case 4:
                    $attacker->getArmorInventory()->setBoots(VanillaItems::AIR());
                    $this->sendActivation($attacker, "§cYOUR BOOTS HAVE BEEN DISARMORED!");
                    break;
            }
            if ($aitem instanceof Armor)
                $attacker->getWorld()->dropItem($attacker->getPosition()->asVector3(), $aitem);
            $this->sendActivation($player, "§bDisarmor §aActivated!");
        } else {
            $this->sendActivation($player, "§bDisarmor §cDeactivated! Player's armor has Disarmor Protection!");
        }
    }
}