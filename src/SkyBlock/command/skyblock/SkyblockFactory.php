<?php


namespace SkyBlock\command\skyblock;


use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use SkyBlock\command\skyblock\admin\DAutoMiner;
use SkyBlock\command\skyblock\admin\DAutoSeller;
use SkyBlock\command\skyblock\admin\DGen;
use SkyBlock\command\skyblock\admin\FDel;
use SkyBlock\command\skyblock\admin\SetLevel;
use SkyBlock\command\skyblock\admin\SetPoints;
use SkyBlock\command\skyblock\ban\Ban;
use SkyBlock\command\skyblock\ban\BanList;
use SkyBlock\command\skyblock\ban\Unban;
use SkyBlock\command\skyblock\bank\Bank;
use SkyBlock\command\skyblock\bank\Donate;
use SkyBlock\command\skyblock\bank\Withdraw;
use SkyBlock\command\skyblock\helper\Accept;
use SkyBlock\command\skyblock\helper\Deny;
use SkyBlock\command\skyblock\helper\Helpers;
use SkyBlock\command\skyblock\helper\Invite;
use SkyBlock\command\skyblock\helper\Leave;
use SkyBlock\command\skyblock\helper\Remove;
use SkyBlock\command\skyblock\home\DelHome;
use SkyBlock\command\skyblock\home\Home;
use SkyBlock\command\skyblock\home\Homes;
use SkyBlock\command\skyblock\home\SetHome;
use SkyBlock\command\skyblock\job\Fire;
use SkyBlock\command\skyblock\job\FireAll;
use SkyBlock\command\skyblock\job\Ignore;
use SkyBlock\command\skyblock\job\Request;
use SkyBlock\command\skyblock\job\Sign;
use SkyBlock\command\skyblock\job\Workers;
use SkyBlock\Main;

final class SkyblockFactory {

    /** @var string[] */
    public const SKYBLOCK_COMMANDS
        = [
            VLimit::class,
            SetReceiver::class,
            AutoSeller::class,
            Farming::class,
            KickAll::class,
            WBroadcast::class,
            Chatsize::class,
            FireAll::class,
            TakeOver::class,
            AutoMiner::class,
            Fire::class,
            Help::class,
            Version::class,
            SetPoints::class,
            SetLevel::class,
            DGen::class,
            FDel::class,
            Expand::class,
            Perks::class,
            Teleport::class,
            Motd::class,
            Remove::class,
            Delete::class,
            Reset::class,
            PF::class,
            Bank::class,
            Donate::class,
            Leave::class,
            MakeOwner::class,
            Helpers::class,
            Top::class,
            Online::class,
            SetSpawn::class,
            RandomTP::class,
            Info::class,
            PList::class,
            Islands::class,
            Deny::class,
            Accept::class,
            Invite::class,
            Ignore::class,
            Sign::class,
            Request::class,
            Demote::class,
            Promote::class,
            BanList::class,
            //        Drain::class,
            Unban::class,
            Ban::class,
            Mining::class,
            Lock::class,
            TeamChat::class,
            Kick::class,
            DelHome::class,
            SetHome::class,
            Home::class,
            Homes::class,
            Perms::class,
            Create::class,
            Points::class,
            Freeze::class,
            //        WarTP::class,
            //        WarDeny::class,
            //        WarAccept::class,
            //        War::class,
            Broadcast::class,
            Go::class,
            Rename::class,
            Workers::class,
            Withdraw::class,
            DAutoSeller::class,
            DAutoMiner::class
        ];
    /** @var Main */
    private Main $plugin;
    /** @var array */
    public array $commands;

    /**
     * SkyblockFactory constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function getSkyblockCommands() : array {
        $arr = [];
        foreach ($this->commands as $cmd => $data) {
            if (($info = $data["instance"]->info) != "") $arr[$cmd] = $info;
        }
        return $arr;
    }

    public function init() {
        foreach (self::SKYBLOCK_COMMANDS as $commandClass) {
            /** @var BaseSkyblock $inst */
            $inst = new $commandClass($this->plugin);
            $this->commands[$inst->getCommand()] = ["instance" => $inst, "alias" => $inst->getAlias()];
        }
        $this->plugin->getServer()->getLogger()->info("§f=> §eRegistered §f" . count($this->commands) . " §eSkyblock commands! §f<=");
    }

    /**
     * @param CommandSender $sender
     * @param array         $args
     *
     * @return bool
     */
    public function execute(CommandSender $sender, array $args) : bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cRun command in game!");
            return false;
        }
        $args[0] = strtolower($args[0]);
        if (($inst = $this->commandExists($args[0])) == null) {
            $this->plugin->sendMessage($sender, "§4[Error]§c Command not found! Use /is help <page>");
            return false;
        } else {
            $inst->execute($sender, $inst->um->getOnlineUser($sender->getName()), $args);
            return true;
        }
    }

    /**
     * @param string $command
     *
     * @return BaseSkyblock|null
     */
    public function commandExists(string $command) : ?BaseSkyblock {
        foreach ($this->commands as $cmd => $data) {
            if ($command == $cmd or in_array($command, $data["alias"], true)) return $data["instance"];
        }
        return null;
    }

}