<?php

namespace SkyBlock\tasks;

use pocketmine\item\Armor;
use pocketmine\scheduler\Task;
use SkyBlock\enchants\armor\effect\BaseArmorEffectEnchant;
use SkyBlock\item\Mask;
use SkyBlock\Main;
use SkyBlock\util\Constants;

class EffectTask extends Task {

    private Main $pl, $plugin;

    /**
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->pl = $plugin;
    }

    public function onRun() : void {
        $this->pl->clt -= 5;
        if ($this->pl->clt <= 20 and $this->pl->clt > 0) $this->pl->getServer()->broadcastMessage("§e➼> §c§lEntities/Ground items will clear in §a{$this->pl->clt} §cseconds!");
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            foreach ($player->getArmorInventory()->getContents() as $item) {
                if ($item->hasEnchantments() and $item instanceof Armor and $item->getMaxDurability() >= Constants::ARMOR_TIER_CHAIN_MAX_DURABILITY) {
                    foreach ($item->getEnchantments() as $ench) {
                        $type = $ench->getType();
                        if ($type instanceof BaseArmorEffectEnchant && $type->isApplicableTo($player)) {
                            $type->onEquip($player, $ench->getLevel());
                        }
                    }
                }
                if ($item instanceof Armor and $item instanceof Mask) {
                    foreach ($item->getBaseEffects() as $effect) {
                        $player->getEffects()->add($effect);
                    }
                }
            }
        }
    }

}
