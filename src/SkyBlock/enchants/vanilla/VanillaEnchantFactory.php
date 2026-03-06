<?php

namespace SkyBlock\enchants\vanilla;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use SkyBlock\enchants\BaseEnchantment;
use SkyBlock\Main;

class VanillaEnchantFactory {

    /** @var Main */
    private Main $plugin;

    /** @var array */
    private array $enchants
        = [
            EnchantmentIds::LOOTING            => ["Looting", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Sword', Looting::class],
            EnchantmentIds::FORTUNE            => ["Fortune", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_TOOLS, 'Tool', Fortune::class],
            EnchantmentIds::KNOCKBACK          => ["Knockback", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_SWORD, 'Sword', Knockback::class],
            EnchantmentIds::DEPTH_STRIDER      => ["DepthStrider", BaseEnchantment::TYPE_BOOTS, BaseEnchantment::ITEM_TYPE_BOOTS, 'Boots', DepthStrider::class],
            EnchantmentIds::AQUA_AFFINITY      => ["AquaAffinity", BaseEnchantment::TYPE_HELMET, BaseEnchantment::ITEM_TYPE_HELMET, 'Helmet', AquaAffinity::class],
            EnchantmentIds::SMITE              => ["Smite", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_WEAPON, 'Sword', Smite::class],
            EnchantmentIds::BANE_OF_ARTHROPODS => ["BaneOfArthropods", BaseEnchantment::TYPE_HAND, BaseEnchantment::ITEM_TYPE_WEAPON, 'Sword', BaneOfArthropods::class],
        ];

    /**
     * EnchantFactory constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function init() : void {
        foreach ($this->enchants as $id => $data) {
            $enchant = new $data[4]($this->plugin, $id, $data[0]);
            EnchantmentIdMap::getInstance()->register($id, $enchant);
            StringToEnchantmentParser::getInstance()->override($data[0], fn() => $enchant);
        }
        $this->plugin->getServer()->getLogger()->info("§f=> §eRegistered §a" . count($this->enchants) . " §evanilla enchants! §f<=");
    }

}