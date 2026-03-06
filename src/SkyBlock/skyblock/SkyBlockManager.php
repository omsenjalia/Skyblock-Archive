<?php

namespace SkyBlock\skyblock;

use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\WorldCreationOptions;
use SkyBlock\Main;
use SkyBlock\user\User;

class SkyBlockManager {

    /** @var Main */
    private Main $plugin;

    /**
     * SkyBlockManager constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function generateIsland(Player $player, User $user, string $name) : void {
        $island = $this->plugin->getIslandManager()->createIsland($player, $user, $name);
        $islandName = $island->getId();
        $gen = GeneratorManager::getInstance()->getGenerator("basic");
        $this->plugin->getServer()->getWorldManager()->generateWorld($islandName, WorldCreationOptions::create()->setGeneratorClass($gen->getGeneratorClass()));
        $this->plugin->getServer()->getWorldManager()->loadWorld($islandName, true);
        $level = $this->plugin->getServer()->getWorldManager()->getWorldByName($islandName);
        $level->setSpawnLocation(new Vector3(3, 6, 2));
        $level->save(true);
        $island->setWorldLevel($level);
        $island->setCreator($player->getName());
    }

}