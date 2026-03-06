<?php


namespace SkyBlock\pets;


use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;
use SkyBlock\Main;
use SkyBlock\PetProperties;

class PetFactory {

    /** @var string[] */
    public const PET_CLASSES
        = [
            "Chicken"        => ChickenPet::class,
            "Bat"            => BatPet::class,
            "Donkey"         => DonkeyPet::class,
            "EnderDragon"    => EnderDragonPet::class,
            "Endermite"      => EndermitePet::class,
            "Evoker"         => EvokerPet::class,
            "Ghast"          => GhastPet::class,
            "Horse"          => HorsePet::class,
            "Llama"          => LlamaPet::class,
            "MagmaCube"      => MagmaCubePet::class,
            "Mooshroom"      => MooshroomPet::class,
            "Mule"           => MulePet::class,
            "Ocelot"         => OcelotPet::class,
            "PolarBear"      => PolarBearPet::class,
            "Panda"          => PandaPet::class,
            "Hoglin"         => HoglinPet::class,
            "Phantom"        => PhantomPet::class,
            "Rabbit"         => RabbitPet::class,
            "SkeletonHorse"  => SkeletonHorsePet::class,
            "Slime"          => SlimePet::class,
            "SnowGolem"      => SnowGolemPet::class,
            "Stray"          => StrayPet::class,
            "Fox"            => FoxPet::class,
            "Bee"            => BeePet::class,
            "Vex"            => VexPet::class,
            "Vindicatror"    => VindicatorPet::class,
            "Witch"          => WitchPet::class,
            "Wither"         => WitherPet::class,
            "WitherSkeleton" => WitherSkeletonPet::class,
            "Wolf"           => WolfPet::class,
            "ZombieHorse"    => ZombieHorsePet::class,
            "ZombieVillager" => ZombieVillagerPet::class,
            "Allay"          => AllayPet::class,
            "Axolotl"        => AxolotlPet::class,
            "ElderGuardian"  => ElderGuardianPet::class,
            "Frog"           => FrogPet::class,
            "Goat"           => GoatPet::class,
            "IronGolem"      => IronGolemPet::class,
            "Parrot"         => ParrotPet::class,
            "SnowFox"        => SnowFoxPet::class,
            "Turtle"         => TurtlePet::class,
            "Blaze"          => BlazePet::class,
            "Cod"            => CodPet::class,
            "Enderman"       => EndermanPet::class,
            "GlowSquid"      => GlowSquidPet::class,
            "Pig"            => PigPet::class,
            "Pufferfish"     => PufferfishPet::class,
            "Salmon"         => SalmonPet::class,
            "Sheep"          => SheepPet::class,
            "TropicalFish"   => TropicalFishPet::class,
            "Warden"         => WardenPet::class,
            "Zombie"         => ZombiePet::class,
            "Cow"            => CowPet::class,
            "Zoglin"         => ZoglinPet::class,
        ];

    /** @var Main $plugin */
    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function init() : void {
        $this->plugin->pProperties = new PetProperties($this->plugin);
        foreach (self::PET_CLASSES as $name => $petClass) {
            EntityFactory::getInstance()->register($petClass, function(World $world, CompoundTag $nbt) use ($petClass) : Entity {
                return new $petClass(EntityDataHelper::parseLocation($nbt, $world), $nbt);
            },                                     [$name]
            );
        }
        $this->plugin->getServer()->getLogger()->info("§f=> §eRegistered §b" . count(self::PET_CLASSES) . " §ePets! §f<=");
    }

}