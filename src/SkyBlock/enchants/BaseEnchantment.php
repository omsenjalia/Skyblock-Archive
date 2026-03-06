<?php


namespace SkyBlock\enchants;


use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\Item;
use pocketmine\player\Player;
use SkyBlock\enchants\armor\Antidote;
use SkyBlock\enchants\armor\Berserker;
use SkyBlock\enchants\armor\Cursed;
use SkyBlock\enchants\armor\Dispatch;
use SkyBlock\enchants\armor\effect\Horde;
use SkyBlock\enchants\armor\effect\Infuriate;
use SkyBlock\enchants\armor\effect\Snorkel;
use SkyBlock\enchants\armor\Endershift;
use SkyBlock\enchants\armor\Enlighten;
use SkyBlock\enchants\armor\Frozen;
use SkyBlock\enchants\armor\Gears;
use SkyBlock\enchants\armor\Implants;
use SkyBlock\enchants\armor\Inspirit;
use SkyBlock\enchants\armor\LifeShield;
use SkyBlock\enchants\armor\Molten;
use SkyBlock\enchants\armor\Poisoned;
use SkyBlock\enchants\armor\Protector;
use SkyBlock\enchants\armor\Shielded;
use SkyBlock\enchants\armor\Tank;
use SkyBlock\enchants\armor\Virtuous;
use SkyBlock\enchants\block\Barter;
use SkyBlock\enchants\block\Booster;
use SkyBlock\enchants\block\Devour;
use SkyBlock\enchants\block\Explosion;
use SkyBlock\enchants\block\Firma;
use SkyBlock\enchants\block\Karma;
use SkyBlock\enchants\block\LuckOfTheSky;
use SkyBlock\enchants\block\Quickening;
use SkyBlock\enchants\block\Tinkerer;
use SkyBlock\enchants\block\Woodcutter;
use SkyBlock\enchants\bow\player\Healing;
use SkyBlock\enchants\bow\player\Launcher;
use SkyBlock\enchants\bow\player\Paralyze;
use SkyBlock\enchants\bow\player\Piercing;
use SkyBlock\enchants\bow\shoot\Molotov;
use SkyBlock\enchants\bow\shoot\Volley;
use SkyBlock\enchants\sword\Aerial;
use SkyBlock\enchants\sword\Backstabber;
use SkyBlock\enchants\sword\Blind;
use SkyBlock\enchants\sword\Brawler;
use SkyBlock\enchants\sword\Charge;
use SkyBlock\enchants\sword\Chisel;
use SkyBlock\enchants\sword\CripplingStrike;
use SkyBlock\enchants\sword\DeathBringer;
use SkyBlock\enchants\sword\DeepWounds;
use SkyBlock\enchants\sword\Detonate;
use SkyBlock\enchants\sword\DisorderProtection;
use SkyBlock\enchants\sword\IceAspect;
use SkyBlock\enchants\sword\Lifesteal;
use SkyBlock\enchants\sword\MobSlayer;
use SkyBlock\enchants\sword\OverPower;
use SkyBlock\enchants\sword\Poison;
use SkyBlock\enchants\sword\Potshot;
use SkyBlock\enchants\sword\Ram;
use SkyBlock\enchants\sword\Serpent;
use SkyBlock\enchants\sword\Smasher;
use SkyBlock\enchants\sword\SoulSnatcher;
use SkyBlock\enchants\sword\Thunderbolt;
use SkyBlock\enchants\sword\Vampire;
use SkyBlock\enchants\sword\Witch;
use SkyBlock\enchants\sword\Wither;
use SkyBlock\enchants\sword\Wizardly;
use SkyBlock\enchants\touch\Demolisher;
use SkyBlock\enchants\touch\Scythe;
use SkyBlock\Main;
use SkyBlock\util\Constants;

abstract class BaseEnchantment extends Enchantment {
    /** @var Main $pl */
    public Main $pl;
    /** @var int */
    public const MAX_LEVEL = 5;

    const TYPE_HAND = 0;
    const TYPE_ANY_INVENTORY = 1;
    const TYPE_INVENTORY = 2;
    const TYPE_ARMOR_INVENTORY = 3;
    const TYPE_HELMET = 4;
    const TYPE_CHESTPLATE = 5;
    const TYPE_LEGGINGS = 6;
    const TYPE_BOOTS = 7;

    const ITEM_TYPE_GLOBAL = 0;
    const ITEM_TYPE_DAMAGEABLE = 1;
    const ITEM_TYPE_WEAPON = 2;
    const ITEM_TYPE_SWORD = 3;
    const ITEM_TYPE_BOW = 4;
    const ITEM_TYPE_TOOLS = 5;
    const ITEM_TYPE_PICKAXE = 6;
    const ITEM_TYPE_AXE = 7;
    const ITEM_TYPE_SHOVEL = 8;
    const ITEM_TYPE_HOE = 9;
    const ITEM_TYPE_ARMOR = 10;
    const ITEM_TYPE_HELMET = 11;
    const ITEM_TYPE_CHESTPLATE = 12;
    const ITEM_TYPE_LEGGINGS = 13;
    const ITEM_TYPE_BOOTS = 14;
    const ITEM_TYPE_COMPASS = 15;


    /**
     * @param Main   $plugin
     * @param string $name
     * @param int    $rarity
     * @param int    $maxLevel
     */
    public function __construct(Main $plugin, string $name, int $rarity, int $maxLevel) {
        $this->pl = $plugin;
        parent::__construct($name, $rarity, ItemFlags::ALL, ItemFlags::ALL, $maxLevel);
    }

    /**
     * @param Item $item
     * @param int  $id
     *
     * @return bool
     */
    public static function hasEnchantment(Item $item, int $id) : bool {
        if (EnchantmentIdMap::getInstance()->fromId($id) == null) {
            return false;
        }
        return $item->hasEnchantment(EnchantmentIdMap::getInstance()->fromId($id));
    }

    /**
     * @param Item  $item
     * @param array $ids
     *
     * @return bool
     */
    public static function hasAnyEnchantment(Item $item, array $ids) : bool {
        foreach ($ids as $id) {
            if (BaseEnchantment::hasEnchantment($item, $id)) return true;
        }
        return false;
    }

    /**
     * @param Item $item
     * @param int  $id
     *
     * @return int
     */
    public static function getEnchantmentLevel(Item $item, int $id) : int {
        return $item->getEnchantmentLevel(EnchantmentIdMap::getInstance()->fromId($id));
    }

    /**
     * @param EnchantmentInstance $enchantment
     *
     * @return int
     */
    public static function getEnchantmentId(EnchantmentInstance $enchantment) : int {
        return EnchantmentIdMap::getInstance()->toId($enchantment->getType());
    }

    /**
     * @param int $id
     *
     * @return Enchantment
     */
    public static function getEnchantment(int $id) : Enchantment {
        return EnchantmentIdMap::getInstance()->fromId($id);
    }

    /**
     * @param $ench
     *
     * @return EnchantmentInstance|null
     */
    public static function parse($ench) : ?EnchantmentInstance {
        if (is_numeric($ench)) {
            $enchantment = EnchantmentIdMap::getInstance()->fromId((int) $ench);
        } else {
            $enchantment = StringToEnchantmentParser::getInstance()->parse($ench);
        }
        return ($enchantment === null) ? null : new EnchantmentInstance($enchantment);
    }

    /**
     * @param Player $player
     * @param string $msg
     */
    public function sendActivation(Player $player, string $msg) : void {
        if (($user = $this->pl->getUserManager()->getOnlineUser($player->getName())) !== null) {
            $type = $user->getPref()->ce_act_type;
            if ($type === Constants::CE_TIP) {
                $player->sendTip($msg);
            } elseif ($type === Constants::CE_MSG) {
                $player->sendMessage($msg);
            }
        }
    }

    public function getDuration(int $level = 1) : int {
        return (int) (ceil($level / 2) * 20);
    }

    public function getLevel(int $level = 1) : int {
        return (($int = ceil($level / 2)) > self::MAX_LEVEL) ? self::MAX_LEVEL : $int;
    }

    /**
     * @return Main
     */
    public function getPlugin() : Main {
        return $this->pl;
    }

    /**
     * @param Player $holder
     *
     * @return bool
     */
    abstract public function isApplicableTo(Player $holder) : bool;


    public static function getRandomKOTHCe() {
        $enchantIds = [
            Horde::$id, Infuriate::$id, Snorkel::$id, Antidote::$id, Berserker::$id, Cursed::$id,
            Dispatch::$id, Endershift::$id, Enlighten::$id, Frozen::$id, Gears::$id, Implants::$id, Inspirit::$id,
            LifeShield::$id, Molten::$id, Poisoned::$id, Protector::$id, Shielded::$id, Tank::$id, Virtuous::$id,
            Barter::$id, Booster::$id, Devour::$id, Explosion::$id, Firma::$id, Karma::$id,
            LuckOfTheSky::$id, Quickening::$id, Tinkerer::$id, Woodcutter::$id, Healing::$id, Launcher::$id,
            Paralyze::$id, Piercing::$id, Molotov::$id, Volley::$id, Aerial::$id, Backstabber::$id, Blind::$id,
            Brawler::$id, Charge::$id, Chisel::$id, CripplingStrike::$id, DeathBringer::$id, DeepWounds::$id,
            Detonate::$id, IceAspect::$id, Lifesteal::$id, DisorderProtection::$id, MobSlayer::$id, OverPower::$id,
            Poison::$id, Potshot::$id, Ram::$id, Serpent::$id, Smasher::$id, SoulSnatcher::$id, Thunderbolt::$id,
            Vampire::$id, Witch::$id, Wither::$id, Wizardly::$id, Demolisher::$id, Scythe::$id
        ];
        return $enchantIds[array_rand($enchantIds, 1)];
    }

    public static function weightedChoice(array $values, array $weights) {
        if (count($values) !== count($weights)) {
            return 1;
        }

        $totalWeight = array_sum($weights);
        $random = mt_rand(0, $totalWeight - 1);

        $cumulativeWeight = 0;
        for ($i = 0; $i < count($weights); $i++) {
            $cumulativeWeight += $weights[$i];
            if ($random < $cumulativeWeight) {
                return $values[$i];
            }
        }

        return 1;
    }
}
