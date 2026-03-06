<?php

namespace SkyBlock\spawner;

use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\world\World;
use SkyBlock\Main;

class SpawnerFactory {

    /** @var array[] */
    public const SPAWNER_CLASSES
        = [
            EntityIds::BLAZE         => [Blaze::class, 'SBlaze'],
            EntityIds::CHICKEN       => [Chicken::class, 'SChicken'],
            EntityIds::COW           => [Cow::class, 'SCow'],
            EntityIds::CREEPER       => [Creeper::class, 'SCreeper'],
            EntityIds::IRON_GOLEM    => [IronGolem::class, 'SIronGolem'],
            EntityIds::PIG           => [Pig::class, 'SPig'],
            EntityIds::ZOMBIE_PIGMAN => [PigZombie::class, 'SPigZombie'],
            EntityIds::SHEEP         => [Sheep::class, 'SSheep'],
            EntityIds::SKELETON      => [Skeleton::class, 'SSkeleton'],
            EntityIds::SPIDER        => [Spider::class, 'SSpider'],
            EntityIds::SQUID         => [Squid::class, 'SSquid'],
            EntityIds::ZOMBIE        => [Zombie::class, 'SZombie'],
            EntityIds::CAMEL         => [Camel::class, 'SCamel'],
            EntityIds::GLOW_SQUID    => [GlowSquid::class, 'SGlowSquid'],
            EntityIds::GOAT          => [Goat::class, 'SGoat'],
            EntityIds::POLAR_BEAR    => [PolarBear::class, 'SPolarBear'],
            EntityIds::SILVERFISH    => [Silverfish::class, 'SSilverfish'],
            EntityIds::PANDA         => [Panda::class, "SPanda"],
        ];

    /** @var Main $plugin */
    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function init() : void {
        foreach (self::SPAWNER_CLASSES as $eClass) {
            EntityFactory::getInstance()->register($eClass[0], function(World $world, CompoundTag $nbt) use ($eClass) : Entity {
                return new $eClass[0](EntityDataHelper::parseLocation($nbt, $world), $nbt);
            },                                     [$eClass[1]]
            );
        }
        $this->plugin->getServer()->getLogger()->info("§f=> §fRegistered §7" . count(self::SPAWNER_CLASSES) . " §eSpawners! §f<=");
    }

}