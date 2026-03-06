<?php


namespace SkyBlock\tiles;


use pocketmine\block\tile\TileFactory as TFA;
use pocketmine\data\bedrock\block\BlockTypeNames;
use SkyBlock\Main;

class TileFactory {

    /** @var array */
    public const TILE_CLASSES
        = [
            BlockTypeNames::SLIME       => [AutoMinerTile::class, "autominer"],
            BlockTypeNames::MOB_SPAWNER => [MobSpawner::class, "MobSpawner"],
            BlockTypeNames::HOPPER      => [Hopper::class, "hopper"],
            BlockTypeNames::BEACON      => [AutoSellerTile::class, "autoseller"],
            "fallentech:catalyst"       => [CatalystTile::class, "catalyst"],
        ];

    /** @var Main $pl */
    public Main $pl;

    public function __construct(Main $plugin) {
        $this->pl = $plugin;
    }

    public function init() : void {
        foreach (self::TILE_CLASSES as $class) {
            TFA::getInstance()->register($class[0], [$class[1], "minecraft:" . strtolower($class[1])]);
            //            echo("minecraft:" . strtolower($class[1]) . ": " . TFA::getInstance()->getSaveId("fallentech:" . strtolower($class[1])) . "}\n");
        }
        $this->pl->getServer()->getLogger()->info("§f=> §eRegistered §c" . count(self::TILE_CLASSES) . " §dtiles! §f<=");
    }
}