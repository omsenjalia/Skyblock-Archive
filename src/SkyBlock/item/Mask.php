<?php


namespace SkyBlock\item;


use pocketmine\item\Armor;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ItemIdentifier;
use pocketmine\player\Player;

abstract class Mask extends Armor {
    public function __construct(ItemIdentifier $identifier, string $name, ArmorTypeInfo $info) {
        parent::__construct($identifier, $name, $info);
    }

    abstract function getBaseEffects() : array;

    abstract function runPasiveAbility(Player $victim, Player $parent) : void;

    public function applyDamage(int $amount) : bool {
        return parent::applyDamage(0);
    }
}