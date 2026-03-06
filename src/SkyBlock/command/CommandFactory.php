<?php


namespace SkyBlock\command;


use SkyBlock\command\ce\{Carver,
    CEBooks,
    CEInfo,
    CEList,
    CEShop,
    CEVote,
    Combiner,
    Enchanter,
    Fixer,
    Inferno,
    LevelUp,
    Maxer,
    Merger,
    OPSword,
    Removece,
    Renew,
    Scroll,
    Surge,
    Vaulted,
    Vulcan};
use SkyBlock\command\mana\AddMana;
use SkyBlock\command\mana\MyMana;
use SkyBlock\command\mana\PayMana;
use SkyBlock\command\mana\SeeMana;
use SkyBlock\command\mana\SetMana;
use SkyBlock\command\mana\TakeMana;
use SkyBlock\command\mana\TopMana;
use SkyBlock\command\mcmmo\{MCMMO, MCStats, MCTop};
use SkyBlock\command\mobcoin\AddMobCoin;
use SkyBlock\command\mobcoin\MyMobCoin;
use SkyBlock\command\mobcoin\PayMC;
use SkyBlock\command\mobcoin\SeeMC;
use SkyBlock\command\mobcoin\SetMobCoin;
use SkyBlock\command\mobcoin\TakeMobCoin;
use SkyBlock\command\mobcoin\TopMobCoin;
use SkyBlock\command\money\{AddMoney, MyMoney, Pay, SeeMoney, SetMoney, TakeMoney, TopMoney};
use SkyBlock\command\npcs\FloristNPC;
use SkyBlock\command\npcs\FoodCriticNPC;
use SkyBlock\command\npcs\HunterNPC;
use SkyBlock\command\npcs\LightsNPC;
use SkyBlock\command\npcs\MineNPC;
use SkyBlock\command\npcs\WizardNPC;
use SkyBlock\command\quests\BakerQuests;
use SkyBlock\command\quests\IslandQuest;
use SkyBlock\command\sell\{SA, SAXP, SellChest, SellChestXP, SH, SHXP};
use SkyBlock\command\shop\{Pets, Shop};
use SkyBlock\command\skyblock\Oregen;
use SkyBlock\command\teleport\{TPA, TPAccept, TPAHere, TPDeny, TPHere};
use SkyBlock\command\warps\{IsWorld, SetPos, Spawn, Warp, World};
use SkyBlock\Main;

final class CommandFactory {

    /** @var string[] */
    private const COMMAND_CLASSES
        = [
            KothKit::class,
            Trade::class,
            Bounty::class,
            PayMana::class,
            DeleteChest::class,
            XPBank::class,
            Pref::class,
            TypeId::class,
            Test::class,
            //        DelHome::class,
            //        Home::class,
            //        Homes::class,
            //        SetHome::class,
            //        MobCoinShop::class,
            AddMobCoin::class,
            MyMobCoin::class,
            PayMC::class,
            SeeMC::class,
            SetMobCoin::class,
            TakeMobCoin::class,
            TopMobCoin::class,
            Surge::class,
            Renew::class,
            Dupe::class,
            //        HeadSell::class,
            TempFly::class,
            CondenseChest::class,
            Number::class,
            PlayerSearch::class,
            SellChestXP::class,
            BlocksBroken::class,
            TopBlocks::class,
            Carver::class,
            Vulcan::class,
            Tag::class,
            //        ManaShop::class,
            AddMana::class,
            MyMana::class,
            SeeMana::class,
            SetMana::class,
            TakeMana::class,
            TopMana::class,
            Upgrade::class,
            IsWorld::class,
            Update::class,
            SetPos::class,
            Warp::class,
            Vaulted::class,
            SellChest::class,
            ClearLagTime::class,
            //        Casino::class,
            Top::class,
            FixAll::class,
            Fix::class,
            CheckEnch::class,
            ClearOfflineInv::class,
            ClearEffects::class,
            BreakMe::class,
            TopWins::class,
            Fly::class,
            Rename::class,
            Gamble::class,
            AddChips::class,
            MyXP::class,
            MyChips::class,
            Ench::class,
            Clearlagg::class,
            Brag::class,
            SHXP::class,
            SH::class,
            SAXP::class,
            SA::class,
            //        Spawner::class,
            OPSword::class,
            CEShop::class,
            CEBooks::class,
            CEVote::class,
            CEList::class,
            CEInfo::class,
            Removece::class,
            BCK::class,
            Merger::class,
            LevelUp::class,
            Enchanter::class,
            Combiner::class,
            Fixer::class,
            Maxer::class,
            Inferno::class,
            //        Enchants::class,
            TPAccept::class,
            TPDeny::class,
            TPHere::class,
            TPAHere::class,
            TPA::class,
            MCStats::class,
            MCTop::class,
            MCMMO::class,
            MyMoney::class,
            SeeMoney::class,
            Pay::class,
            TopMoney::class,
            TakeMoney::class,
            SetMoney::class,
            AddMoney::class,
            Spawn::class,
            ClearInv::class,
            God::class,
            Reply::class,
            Tell::class,
            Feed::class,
            Gmc::class,
            Gms::class,
            GetPos::class,
            Heal::class,
            ItemCommand::class,
            Effectids::class,
            Enchantids::class,
            Broadcast::class,
            Time::class,
            Enchant::class,
            Scroll::class,
            CheckVote::class,
            Voted::class,
            TimePlayed::class,
            Shop::class,
            Servers::class,
            Profile::class,
            ItemCloudIC::class,
            Auction::class,
            Envoy::class,
            Goals::class,
            GKitPerm::class,
            Kit::class,
            Koth::class,
            DelFolder::class,
            RemoveVanillaEnch::class,
            //        ChatSize::class,
            Pets::class,
            GivePet::class,
            MyIslands::class,
            Crops::class,
            Rainbow::class,
            ClaimAllKits::class,
            Ranks::class,
            Helpme::class,
            //        Breakit::class,
            Vanish::class,
            Gang::class,
            Skyblock::class,
            Effect::class,
            SetSpawn::class,
            World::class,
            Tutorial::class,
            TopXP::class,
            SeeXP::class,
            Records::class,
            AutoSprint::class,
            AutoSprintList::class,
            SetXp::class,
            NewUser::class,
            Oregen::class,
            ClaimAllGKits::class,
            IslandQuest::class,
            BakerQuests::class,
            FoodCriticNPC::class,
            WizardNPC::class,
            FloristNPC::class,
            LightsNPC::class,
            HunterNPC::class,
            MineNPC::class,

            //		Stash::class,
        ];

    /** @var Main */
    private Main $plugin;

    /**
     * CommandFactory constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->init();
    }

    private function init() : void {
        $commands = [];
        foreach (self::COMMAND_CLASSES as $commandClass) {
            /** @var BaseCommand $cmdClass */
            $cmdClass = new $commandClass($this->plugin);
            $cmd = $this->plugin->getServer()->getCommandMap()->getCommand($cmdClass->getName());
            if ($cmd !== null) $this->plugin->getServer()->getCommandMap()->unregister($cmd);
            $commands[] = $cmdClass;
        }
        $this->plugin->getServer()->getCommandMap()->registerAll('Skyblock-Core', $commands);
        $this->plugin->getServer()->getLogger()->info("§f=> §eRegistered §d" . count($commands) . " §ecommands! §f<=");
    }

}