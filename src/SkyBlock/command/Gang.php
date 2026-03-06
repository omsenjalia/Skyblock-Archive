<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;
use SkyBlock\Main;
use SkyBlock\util\Util;

class Gang extends BaseCommand {

    /**
     * Gang constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'g', 'Gang Help', 'help');
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendUsage($sender, $commandLabel, false);
            return;
        }
        $user = $this->um->getOnlineUser($sender->getName());
        if (empty($args)) {
            $this->sendMessage($sender, "§4[Error]§c Please use /g help or /gang help for a list of commands");
            return;
        }
        if (isset($args[0])) {
            switch (strtolower($args[0])) {

                case "help":

                    $commands = [
                        "§ehelp"            => "§7Show gang command help page.",
                        "§ecreate"          => "§7Create a new gang",
                        "§erename"          => "§7Rename your gang",
                        "§ekick"            => "§7Remove someone from your gang.",
                        "§epg"              => "§7Check gang of any player",
                        "§elevel"           => "§7Check gang level of gangs",
                        "§eonline"          => "§7Shows all online players of a gang",
                        "§echatsize"        => "§7Toggle Gang Chat size",
                        "§egonline"         => "§7Shows all top online gangs",
                        "§emembers"         => "§7Shows all members of a gang.",
                        "§etakeover"        => "§7Takeover the Gang Leader position.",
                        "§eleader"          => "§7Shows leader of a gang.",
                        "§einfo"            => "§7Shows all info of the gang.",
                        "§emotd"            => "§7Set your gang's motd/desc",
                        "§etop"             => "§7Shows all top gangs by level.",
                        "§einvite"          => "§7Invite a player to be member of your gang.",
                        "§eaccept <player>" => "§7accept a player's gang invitation.",
                        "§edeny <player>"   => "§7Deny a player's gang invitation.",
                        "§eleave"           => "§7Leave your gang",
                        "§ebackup"          => "§7Send a backup request to your gang online players.",
                        "§echat"            => "§7Enable/Disable Gang chat.",
                        "§eabout"           => "§7Gang's version info",
                        "§edelete"          => "§7Delete your gang."
                    ];
                    if (isset($args[2])) {
                        $this->sendMessage($sender, "§4Usage: /g help <page>");
                        break;
                    }
                    if (!isset($args[1])) {
                        $args[1] = 1;
                    }
                    if (!is_int((int) $args[1]) or $args[1] < 1) {
                        $this->sendMessage($sender, "§4[Error]§e Please enter a valid page number!");
                        break;
                    }
                    $args[1] = (int) $args[1];
                    $total = count($commands);
                    $pages = ceil($total / 8);
                    $page = $args[1];
                    if ($pages < $page) {
                        $this->sendMessage($sender, "§4[Error]§e That page cannot be found.\nLast page = $pages");
                        break;
                    }
                    $endnum = $page * 8;
                    $startnum = $endnum - 7;
                    $i = 1;
                    $str = TextFormat::DARK_GREEN . "-----------" . TextFormat::AQUA . " [" . TextFormat::GREEN . "GangsPE Help" . TextFormat::AQUA . "] " . TextFormat::RESET . TextFormat::DARK_GREEN . "-----------\n";
                    foreach ($commands as $command => $description) {
                        if ($i >= $startnum and $i <= $endnum)
                            $str .= TextFormat::AQUA . "§e/" . TextFormat::GREEN . "§eg {$command}: " . TextFormat::RESET . TextFormat::DARK_GREEN . $description . "\n";
                        $i++;
                    }
                    $sender->sendMessage($str . "§6Gang Members can't hit each other! Kill players to increase gang level to open more member slots and more kill money!");
                    $sender->sendMessage(TextFormat::DARK_GREEN . "-----------------" . TextFormat::AQUA . " [" . TextFormat::GREEN . "$page/$pages" . TextFormat::AQUA . "] " . TextFormat::RESET . TextFormat::DARK_GREEN . "-----------------");

                    break;

                case "chatsize":
                case "cs":

                    if (isset($args[1])) {
                        $this->sendMessage($sender, "§6Usage: /g cs");
                        break;
                    }
                    if (!$user->hasGang()) {
                        $this->sendMessage($sender, "§4[Error] §cYou dont have a gang!");
                        break;
                    }
                    if (isset($this->pl->gchatsize[$sender->getName()])) {
                        unset($this->pl->gchatsize[$sender->getName()]);
                        $this->sendMessage($sender, "§eBigger Gang Chat!");
                    } else {
                        $this->pl->gchatsize[$sender->getName()] = true;
                        $this->sendMessage($sender, "§eSmaller Gang Chat!");
                    }

                    break;

                case "rename":

                    if (!isset($args[1]) or isset($args[2])) {
                        $this->sendMessage($sender, "§6Usage: /g rename <name>");
                        break;
                    }
                    if (!$user->hasGang()) {
                        $this->sendMessage($sender, "§4[Error] §cYou dont have a gang!");
                        break;
                    }
                    $gang = $user->getGang();
                    if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                        $this->sendMessage($sender, "§4[Error]§c Gang not online");
                        break;
                    }
                    if (!$gangc->isLeader($sender->getName())) {
                        $this->sendMessage($sender, "§4[Error] §cYou must be a gang leader to rename your gang!");
                        break;
                    }
                    if (isset($this->pl->gangrename[$user->getGang()])) {
                        $this->sendMessage($sender, "§4[Error] §cYou have already sent a rename request for this restart, your gang will be renamed after restart!");
                        break;
                    }
                    $cost = 50000;
                    if (!$user->hasMoney($cost)) {
                        $this->sendMessage($sender, "§4[Error] §cYou need $cost$ to rename your gang everytime!");
                        break;
                    }
                    if (!(ctype_alnum($args[1]))) {
                        $this->sendMessage($sender, "§4[Error] §cNames can only include letters or numbers");
                        break;
                    }
                    if ($this->db->isGangNameUsed($args[1])) {
                        $this->sendMessage($sender, "§4[Error] §cThat gang name is already in use!");
                        break;
                    }
                    if (strlen($args[1]) > 10) {
                        $this->sendMessage($sender, "§4[Error] §cThat name is too long!");
                        break;
                    }
                    $flag = false;
                    foreach ($this->pl->gangrename as $renaming) {
                        if (strtolower($renaming) == strtolower($args[1])) {
                            $flag = true;
                            break;
                        }
                    }
                    if ($flag) {
                        $this->sendMessage($sender, "§4[Error] §cSomeone else is already renaming their gang that name, next restart!");
                        break;
                    }
                    $user->removeMoney($cost);
                    $this->pl->gangrename[$user->getGang()] = $args[1];
                    $this->sendMessage($sender, "§eYour §b{$user->getGang()} §egang will be renamed to §b{$args[1]} §enext restart! Used §6$cost$");

                    break;

                case "makeowner":
                case "makeleader":
                case "newleader":
                case "newowner":
                    if (!$user->hasGang()) {
                        $this->sendMessage($sender, "§4[Error] §cYou must be a Gang owner to set a new leader to!");
                        break;
                    } else {
                        if (!isset($args[1])) {
                            $this->sendMessage($sender, "§cUsage: /g makeleader <player> or /g newleader <player>");
                            break;
                        }
                        if (strtolower($sender->getName()) == strtolower($args[1])) {
                            $this->sendMessage($sender, "§4[Error] §cYou are already the owner of this Gang!");
                            break;
                        }
                        if (($user2 = $this->um->getOnlineUser($args[1])) === null) {
                            $this->sendMessage($sender, "§4[Error] §c{$args[1]} is not online!");
                            break;
                        }
                        if (strtolower($user2->getGang()) != strtolower($user->getGang())) {
                            $this->sendMessage($sender, "§4[Error] §cThat player isnt in your Gang!");
                            break;
                        }
                        $gangName = $user->getGang();
                        $gang = $this->gm->getOnlineGang($gangName);
                        if (!$gang->isMember($args[1])) {
                            $this->sendMessage($sender, "§4[Error] §cThe player should be a member of your gang! Use /g invite <player>");
                            break;
                        }
                        //$this->db->setGangOwner(strtolower($args[1]), $gangName);
                        $gang->setLeader($args[1]);
                        $this->sendMessage($sender, "§eYou have set the gang leadership to §a{$user2->getName()} §esuccessfully! You are now member of §b$gangName §egang!");
                        $this->sendMessage($user2->getPlayer(), "§eYou are now the Leader of §a{$gangName} §egang!");
                    }
                    break;

                case "create":
                case "make":

                    if ($user->hasGang()) {
                        $gang = $user->getGang();
                        $this->sendMessage($sender, "§4[Error] §cYou already got a Gang - §a$gang! §6Do /g leave to leave gang to create one!");
                        break;
                    }
                    if (!isset($args[1]) or isset($args[2])) {
                        $this->sendMessage($sender, "§4[Error] §cUsage: /g create <gang name>");
                        break;
                    }
                    if (!(ctype_alnum($args[1]))) {
                        $this->sendMessage($sender, "§4[Error] §cGang names can only include letters or numbers");
                        break;
                    }
                    if ($this->db->isGangNameUsed($args[1])) {
                        $this->sendMessage($sender, "§4[Error] §cThat gang name is already in use!");
                        break;
                    }
                    $flag = false;
                    foreach ($this->pl->gangrename as $renaming) {
                        if (strtolower($renaming) == strtolower($args[1])) {
                            $flag = true;
                            break;
                        }
                    }
                    if ($flag) {
                        $this->sendMessage($sender, "§4[Error] §cThat gang name is already in use!");
                        break;
                    }
                    if (strlen($args[1]) > 10) {
                        $this->sendMessage($sender, "§4[Error] §cThat gang name is too long!");
                        break;
                    }
                    if (!$user->removeMoney(10000)) {
                        $this->sendMessage($sender, "§4[Error] §cYou need §610,000$ §cto create a gang! You only have §6{$user->getMoney()}$");
                        break;
                    }
                    $user->setGang($args[1]);
                    $this->gm->createGang($sender, $args[1]);
                    $this->sendMessage($sender, "§aYou successfully created a gang called §e{$args[1]}§a! §6Do §5/g motd §6to set description of your gang! §6Do §5/g invite §6to invite players to your gang!");

                    break;

                case "invite":
                    if (!isset($args[1])) {
                        $this->sendMessage($sender, "§6Usage: /g invite <player>");
                        break;
                    }
                    if (!$user->hasGang()) {
                        $this->sendMessage($sender, "§4[Error] §cYou must be in a gang to use this command!");
                        break;
                    }
                    $gang = $user->getGang();
                    if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                        $this->sendMessage($sender, "§4[Error] §cGang not online!");
                        break;
                    }
                    if ($gangc->isGangFull()) {
                        $this->sendMessage($sender, "§4[Error] §cGang is full. Increase gang level to open more gang member slots, kill players to increase levels!");
                        break;
                    }
                    $player = strtolower($args[1]);
                    if (strtolower($sender->getName()) == $player) {
                        $this->sendMessage($sender, "§4[Error] §cYou cannot invite yourself!");
                        break;
                    }
                    if (($user2 = $this->um->getOnlineUser($player)) === null) {
                        $this->sendMessage($sender, "§4[Error] §cPlayer not online!");
                        break;
                    }
                    if ($user2->hasGang()) {
                        $status = ($user2->getGangLowerCase() == $user->getGangLowerCase()) ? "your" : "a";
                        $this->sendMessage($sender, "§4[Error] §cThat player is already in $status Gang!");
                        break;
                    }
                    if (!$gangc->isLeader($sender->getName())) {
                        $this->sendMessage($sender, "§4[Error] §cOnly Leader can invite players to gang");
                        break;
                    }
                    if (isset($this->pl->ginvitations[strtolower($sender->getName())][$player])) {
                        $time = $this->pl->ginvitations[strtolower($sender->getName())][$player]["time"];
                        $now = time();
                        if (($now - $time) <= 60) {
                            $this->sendMessage($sender, "§4[Error] §cYou've already sent a gang invite request to that player! Wait till it gets timed out or till they respond!");
                            break;
                        } else unset($this->pl->ginvitations[strtolower($sender->getName())][$player]);
                    }
                    $this->pl->ginvitations[strtolower($sender->getName())][$player]["time"] = time();
                    $this->pl->ginvitations[strtolower($sender->getName())][$player]["gang"] = $gang;
                    $this->sendMessage($sender, "§a$player §ehas been invited to your gang!");
                    $this->sendMessage($user2->getPlayer(), "§eYou have been invited to §a$gang §eby §a{$sender->getName()}§e. Use '§6/g accept {$sender->getName()}§e' or '§6/g deny {$sender->getName()}§e' §dRequest will timeout in a minute!");
                    break;
                case "accept":
                    if (!isset($args[1])) {
                        $this->sendMessage($sender, "§6Usage: /g accept <player>");
                        break;
                    }
                    $player = strtolower($args[1]);
                    if (!isset($this->pl->ginvitations[$player][strtolower($sender->getName())])) {
                        $this->sendMessage($sender, "§4[Error] §cYou haven't received any gang requests from §a$player");
                        break;
                    }
                    if (($user2 = $this->um->getOnlineUser($player)) === null) {
                        unset($this->pl->ginvitations[$player][strtolower($sender->getName())]);
                        $this->sendMessage($sender, "§4[Error] §a{$player} §cis offline!");
                        break;
                    }
                    if ($user->hasGang()) {
                        unset($this->pl->ginvitations[$player][strtolower($sender->getName())]);
                        $this->sendMessage($sender, "§4[Error] §cYou are already in a gang! Leave your gang by /g leave to join other gangs!");
                        break;
                    }
                    $time = $this->pl->ginvitations[$player][strtolower($sender->getName())]["time"];
                    $now = time();
                    if (($now - $time) > 60) {
                        unset($this->pl->ginvitations[$player][strtolower($sender->getName())]);
                        $this->sendMessage($sender, "§4[Error] §cRequest timed out!");
                        break;
                    }
                    $gang = $this->pl->ginvitations[$player][strtolower($sender->getName())]["gang"];
                    if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                        unset($this->pl->ginvitations[$player][strtolower($sender->getName())]);
                        $this->sendMessage($sender, "§4[Error] §cGang not online!");
                        break;
                    }
                    if ($gangc->isGangFull()) {
                        unset($this->pl->ginvitations[$player][strtolower($sender->getName())]);
                        $this->sendMessage($sender, "§4[Error] §cGang is full now!");
                        break;
                    }
                    $gangc->addMember(strtolower($sender->getName()));
                    $gangc->addOnline(strtolower($sender->getName()));
                    $user->setGang($gang);
                    $gangc->setMemberKill(strtolower($sender->getName()));
                    $gangc->setMemberDeath(strtolower($sender->getName()));
                    $this->sendMessage($user2->getPlayer(), "§ePlayer §a{$sender->getName()} §ejoined your gang!");
                    $this->sendMessage($sender, "§eYou joined §a$gang §eGang! Leader of gang - §a{$gangc->getLeader()}§e! Do /g info to see all the Gang's info!");
                    unset($this->pl->ginvitations[$player][strtolower($sender->getName())]);

                    break;
                case "deny":
                    if (!isset($args[1])) {
                        $this->sendMessage($sender, "§6Usage: /g deny <player>");
                        break;
                    }
                    $player = strtolower($args[1]);
                    if (!isset($this->pl->ginvitations[$player][strtolower($sender->getName())])) {
                        $this->sendMessage($sender, "§4[Error] §cYou haven't received any gang requests from §a$player");
                        break;
                    }
                    if (($user2 = $this->um->getOnlineUser($player)) === null) {
                        unset($this->pl->ginvitations[$player][strtolower($sender->getName())]);
                        $this->sendMessage($sender, "§4[Error] §a{$player} §cis offline!");
                        break;
                    }
                    $gang = $this->pl->ginvitations[$player][strtolower($sender->getName())]["gang"];
                    if ($this->gm->getOnlineGang($gang) === null) {
                        unset($this->pl->ginvitations[$player][strtolower($sender->getName())]);
                        $this->sendMessage($sender, "§4[Error] §cGang not online!");
                        break;
                    }
                    unset($this->pl->ginvitations[$player][strtolower($sender->getName())]);
                    $this->sendMessage($user2->getPlayer(), "§cPlayer §a{$sender->getName()} §cdenied gang request!");
                    $this->sendMessage($sender, "§cYou denied §a$gang §cgang request successfully!");
                    break;
                case "gonline":
                    if (isset($args[2])) {
                        $this->sendMessage($sender, "§cUsage: /g online <page>");
                        break;
                    }
                    if (!isset($args[1])) {
                        $args[1] = 1;
                    }
                    if (isset($args[1]) and !is_int((int) $args[1]) or $args[1] < 1) {
                        $this->sendMessage($sender, "§4[Error] §cEnter a valid page number!");
                        break;
                    }
                    $args[1] = (int) $args[1];
                    $gangs = $this->gm->getOnlineGangs();
                    $total = count($gangs);
                    $pages = ceil($total / 8);
                    $page = $args[1];
                    if ($pages < $page) {
                        $this->sendMessage($sender, "§4[Error]§e That page cannot be found.\nLast page = $pages");
                        break;
                    }
                    $endnum = $page * 8;
                    $startnum = $endnum - 7;
                    $i = 1;
                    $str = TF::GREEN . "All online Gangs list -\n";
                    $str .= TF::YELLOW . '[+]' . TF::WHITE . str_repeat('=', 10) . TF::AQUA . '[ ' . $page . '/' . $pages . ' ]' . TF::WHITE . str_repeat('=', 10) . TF::YELLOW . '[+]' . "\n";
                    foreach ($gangs as $gang) {
                        if ($i <= $endnum and $i >= $startnum) {
                            $str .= $i . ". §fName: §e{$gang->getName()} §fLeader: §a{$gang->getLeader()} §fLevel: §d{$gang->getLevel()} §fMembers: §9{$gang->getMembersCount()}\n§fMotd: §b{$gang->getMotd()}\n";
                        }
                        ++$i;
                    }
                    $this->sendMessage($sender, $str . TF::YELLOW . '[+]' . TF::WHITE . str_repeat('=', 26) . TF::YELLOW . '[+]' . "\n§e=> §6For more info on a gang, use /g info <gang>! §e<=");
                    break;
                case "members":
                case "ourmembers":
                case "membersof":

                    if (!isset($args[1])) {
                        if (!$user->hasGang()) {
                            $this->sendMessage($sender, "§4[Error]§e You are not in any gang to see members of, §cuse /g members <gang name>");
                            break;
                        }
                        $gang = $user->getGang();
                        if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                            $this->sendMessage($sender, "§4[Error]§c Gang not online");
                            break;
                        }
                        $this->sendMessage($sender, "§a{$gang} §2Gang Members -");
                        $members = $gangc->getMembers();
                        $kills = $gangc->getKills();
                        $deaths = $gangc->getDeaths();
                    } else {
                        if (!$this->db->isGangNameUsed($args[1])) {
                            $this->sendMessage($sender, "§4[Error] §cGang not found!");
                            break;
                        }
                        $gang = $args[1];
                        if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                            $members = $this->db->getGangMembers($gang);
                            $this->sendMessage($sender, "§a{$gang} §2Gang Members -");
                            $kills = $this->db->getGangMemberKills($gang);
                            $deaths = $this->db->getGangMemberDeaths($gang);
                        } else {
                            $kills = $gangc->getKills();
                            $deaths = $gangc->getDeaths();
                            $this->sendMessage($sender, "§a{$gang} §2Gang Members -");
                            $members = $gangc->getMembers();
                        }
                    }
                    $this->func->sendMembersList($sender, $members, $kills, $deaths);
                    break;

                case "leader":
                case "owner":
                case "leaderof":

                    if (!isset($args[1])) {
                        if (!$user->hasGang()) {
                            $this->sendMessage($sender, "§4[Error]§e You are not in any gang to see leader of, §cuse /g leader <gang name>");
                            break;
                        }
                        $gang = $user->getGang();
                        if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                            $this->sendMessage($sender, "§4[Error]§c Gang not online");
                            break;
                        }
                        $this->sendMessage($sender, "_==_| {$gang}'s §aLeader: |_==_");
                        $leader = $gangc->getLeader();
                        $kills = $gangc->getMemberKills($leader);
                        $deaths = $gangc->getMemberDeaths($leader);
                    } else {
                        if (!$this->db->isGangNameUsed($args[1])) {
                            $this->sendMessage($sender, "§4[Error] §cGang not found!");
                            break;
                        }
                        if (($gangc = $this->gm->getOnlineGang($args[1])) === null) {
                            $leader = $this->db->getGangLeader($args[1]);
                            $this->sendMessage($sender, "_==_| {$args[1]}'s §aLeader: |_==_");
                            $kills = $this->db->getMemberKills($args[1], $leader);
                            $deaths = $this->db->getMemberDeaths($args[1], $leader);
                        } else {
                            $this->sendMessage($sender, "_==_| {$args[1]}'s §aLeader: |_==_");
                            $leader = $gangc->getLeader();
                            $kills = $gangc->getMemberKills($leader);
                            $deaths = $gangc->getMemberDeaths($leader);
                        }
                    }
                    if ($this->um->getOnlineUser($leader) !== null) {
                        $sender->sendMessage("-> {$leader} §a[ON] §6Kills: {$kills} §4Deaths: {$deaths}");
                    } else {
                        $sender->sendMessage("-> {$leader} §c[OFF] §6Kills: {$kills} §4Deaths: {$deaths}");
                    }
                    break;

                case "backup":
                case "call":

                    if (isset($args[1])) {
                        $this->sendMessage($sender, "§6Usage: /g backup");
                        break;
                    }
                    if (!$user->hasGang()) {
                        $this->sendMessage($sender, "§4[Error] §cYou are not in any gang!");
                        break;
                    }
                    $gang = strtolower($user->getGang());
                    if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                        $this->sendMessage($sender, "§4[Error] §cGang not online!");
                        break;
                    }
                    $online = $gangc->getOnline();
                    foreach ($online as $mem) {
                        if (strtolower($mem) != strtolower($sender->getName())) {
                            if (($user2 = $this->um->getOnlineUser($mem)) !== null) {
                                $this->sendMessage($user2->getPlayer(), "§eYou've received a gang pvp backup request by §a{$sender->getName()}!");
                            }
                        }
                    }
                    $this->sendMessage($sender, "§eA gang backup request to all online gang members has been sent!");

                    break;
                case "leave":

                    if (isset($args[1])) {
                        $this->sendMessage($sender, "§cUsage: /g leave");
                        break;
                    }
                    if (!$user->hasGang()) {
                        $this->sendMessage($sender, "§4[Error] §cYou must be in a gang to leave it!");
                        break;
                    }
                    $gang = $user->getGang();
                    if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                        $this->sendMessage($sender, "§4[Error] §cGang not online!");
                        break;
                    }
                    if ($gangc->isLeader($sender->getName())) {
                        $this->sendMessage($sender, "§4[Error] §cYou can't leave your gang if you're the leader! Delete your gang by /g delete");
                        break;
                    }
                    $this->plugin->getGangChatHandler()->removePlayerFromChat($sender);
                    $gangc->removeMember($sender->getName());
                    $user->setGang();
                    $gangc->removeMemberKill($sender->getName());
                    $gangc->removeMemberDeath($sender->getName());
                    $gangc->removeOnline($sender->getName());
                    if (empty($gangc->getOnline())) {
                        $this->gm->setGangOffline($gang);
                        $gangc->update();
                        $this->plugin->getGangChatHandler()->setChatOffline($gang);
                    }
                    $this->db->removePlayerGang($sender->getName(), $gang);
                    $this->sendMessage($sender, "§eYou left the gang §a{$gang} §esuccessfully!");
                    break;

                case "user":
                case "search":
                case "gf":
                case "pg":
                case "player":

                    if (!isset($args[1])) {
                        $this->sendMessage($sender, "§6Usage: /g user <player>");
                        break;
                    }
                    $player = strtolower($args[1]);
                    if (!$this->db->isPlayerRegistered($player)) {
                        $this->sendMessage($sender, "§4[Error]§c That player never connected!");
                        break;
                    }
                    if (($user2 = $this->um->getOnlineUser($player)) !== null) {
                        if (!$user2->hasGang()) {
                            $this->sendMessage($sender, "§4[Error]§c That player doesn't have a gang!");
                            break;
                        }
                        $gang = $user2->getGang();
                    } else {
                        if (($gang = $this->db->getPlayerGang($player)) == null) {
                            $this->sendMessage($sender, "§4[Error]§c That player doesn't have a gang!");
                            break;
                        }
                    }
                    $this->sendMessage($sender, "§a{$player}§e's gang: §b$gang");
                    break;

                case "delete":

                    if (isset($args[1])) {
                        $this->sendMessage($sender, "§6Usage: /g delete");
                        break;
                    }
                    if (!$user->hasGang()) {
                        $this->sendMessage($sender, "§4[Error] §cYou dont have a gang!");
                        break;
                    } else {
                        $gang = $user->getGang();
                        if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                            $this->sendMessage($sender, "§4[Error]§c Gang not online");
                            break;
                        }
                        if (!$gangc->isLeader($sender->getName())) {
                            $this->sendMessage($sender, "§4[Error] §cYou must be a gang leader to delete your gang!");
                            break;
                        }
                        $online = $gangc->getOnline();
                        if (!empty($online)) {
                            foreach ($online as $mem) {
                                if (strtolower($mem) != strtolower($sender->getName())) {
                                    if (($user2 = $this->um->getOnlineUser($mem)) !== null) {
                                        $this->sendMessage($user2->getPlayer(), "§4[Error]§c Leader §a{$sender->getName()} §cdeleted §a$gang §cGang!");
                                        $user2->setGang();
                                    }
                                }
                            }
                        }
                        $this->db->delGang($gang);
                        $user->setGang();
                        $this->pl->getGangChatHandler()->setChatOffline(strtolower($gang));
                        $this->gm->setGangOffline($gang);
                        $this->sendMessage($sender, "§aYou successfully permanently deleted the gang!");
                    }

                    break;

                case "info":

                    if (!isset($args[1])) {
                        if (!$user->hasGang()) {
                            $this->sendMessage($sender, "§4[Error] §cYou are not in any gang! Use /g info <gang> to see info of other gangs!");
                            break;
                        }
                        $gang = $user->getGang();
                        if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                            $this->sendMessage($sender, "§4[Error] §cGang not online!");
                            break;
                        }
                        $sender->sendMessage("==================== YOUR GANG ====================\n§fGang: §a$gang\n§fLeader: §a{$gangc->getLeader()}\n§fMembers: §9{$gangc->getMembersCount()}§7/§9{$gangc->getMembersLimit()}\n§fLevel: §d{$gangc->getLevel()}\n§fPoints: §6{$gangc->getPoints()}§7/§6{$gangc->getPointsNeeded()}\n§fMotd: §b{$gangc->getMotd()}\n§fK/D: §a{$gangc->getTotalKills()}§7/§a{$gangc->getTotalDeaths()}\n§fMembers: §a{$gangc->getMemberString()}\n§f=========================================================");
                    } else {
                        $gang = strtolower($args[1]);
                        if (!$this->db->isGangNameUsed($gang)) {
                            $this->sendMessage($sender, "§4[Error] §cGang doesn't exist!");
                            break;
                        }
                        if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                            $members = $this->db->getGangMembers($gang);
                            $member = implode(", ", $members);
                            $info = $this->db->getGangData($gang);
                            $needed = $info["level"] * 150;
                            $sender->sendMessage("==================== $gang GANG ====================\n§fGang: §a$gang\n§fLeader: §a{$info["leader"]}\n§fMembers: §9{$this->db->getMembersCount($gang)}§7/§9{$this->db->getMembersLimit($gang)}\n§fLevel: §d{$info["level"]}\n§fPoints: §6{$info["points"]}§7/§6{$needed}\n§fMotd: §b{$info["motd"]}\n§fK/D: §a{$this->db->getTotalKills($gang)}§7/§a{$this->db->getTotalDeaths($gang)}\n§fMembers: §a{$member}\n§f=========================================================");
                        } else {
                            $sender->sendMessage("==================== $gang GANG ====================\n§fGang: §a$gang\n§fLeader: §a{$gangc->getLeader()}\n§fMembers: §9{$gangc->getMembersCount()}§7/§9{$gangc->getMembersLimit()}\n§fLevel: §d{$gangc->getLevel()}\n§fPoints: §6{$gangc->getPoints()}§7/§6{$gangc->getPointsNeeded()}\n§fMotd: §b{$gangc->getMotd()}\n§fK/D: §a{$gangc->getTotalKills()}§7/§a{$gangc->getTotalDeaths()}\n§fMembers: §a{$gangc->getMemberString()}\n§f=========================================================");
                        }
                    }
                    break;

                case "takeover":

                    if (isset($args[1])) {
                        $this->sendMessage($sender, "§6Usage: /g takeover");
                        break;
                    }
                    if (!$user->hasGang()) {
                        $this->sendMessage($sender, "§4[Error] §cYou are not in any gang!");
                        break;
                    }
                    $gang = $user->getGang();
                    if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                        $this->sendMessage($sender, "§4[Error] §cGang not online!");
                        break;
                    }
                    if (!$gangc->isAMember($sender->getName())) {
                        $this->sendMessage($sender, "§4[Error] §cYou already have the Gang leadership!");
                        break;
                    }
                    $days = 10;
                    $leader = $gangc->getLeader();
                    $player = $this->pl->getServer()->getOfflinePlayer($leader);
                    if ($player instanceof Player) {
                        $this->sendMessage($sender, "§4[Error] §cLeader of the Gang is online. Leader needs to be offline for more than $days days for a member to take over the leadership!");
                        break;
                    }
                    $last = (int) ($player->getLastPlayed() / 1000);
                    $diff = strtotime("now") - $last;
                    $offset = $days * 24 * 60 * 60;
                    if ($diff < $offset) {
                        $this->sendMessage($sender, "§4[Error] §cLeader needs to be offline for more than $days for a member to take over the leadership! §aLeader `{$leader}`'s Last seen - §6" . Util::getTimePlayed($diff));
                        break;
                    } else {
                        $gangc->setLeader($sender->getName());
                        $this->sendMessage($sender, "§eYou have successfully taken over Gang §a{$gangc->getName()}! §6You are now the §bLeader §6of §b{$gangc->getName()} §6gang!");
                    }

                    break;

                case "chat":
                case "teamchat":
                case "tc":
                case "c":

                    if ($this->plugin->getChatHandler()->isInChat($sender)) {
                        $this->plugin->getChatHandler()->removePlayerFromChat($sender);
                        $this->sendMessage($sender, "§eYou successfully left the island chat!");
                    }
                    if ($this->plugin->getGangChatHandler()->isInChat($sender)) {
                        $this->plugin->getGangChatHandler()->removePlayerFromChat($sender);
                        $this->sendMessage($sender, "§eYou successfully left the gang chat!");
                    } else {
                        if (isset($args[1])) {
                            $this->sendMessage($sender, "§6Usage: /g chat");
                            break;
                        }
                        if (!$user->hasGang()) {
                            $this->sendMessage($sender, "§4[Error]§e You are not in any gang to chat!");
                            break;
                        }
                        $gang = strtolower($user->getGang());
                        if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                            $this->sendMessage($sender, "§4[Error]§c Gang not online");
                            break;
                        }
                        if ($gangc->getOnlineCount() <= 1 and !$this->plugin->hasOp($sender)) {
                            $this->sendMessage($sender, "§4[Error]§c Only you are online in your gang!");
                            break;
                        }
                        $this->plugin->getGangChatHandler()->addPlayerToChat($sender, $gang);
                        $this->sendMessage($sender, "§eYou joined §a{$gang} §egang's chat room! §3Now only your gang members will see your messages!");
                        break;
                    }

                    break;

                case "motd":
                case "desc":

                    if (!isset($args[1])) {
                        $this->sendMessage($sender, "§cUsage: /g motd <message>");
                        break;
                    }
                    if (!$user->hasGang()) {
                        $this->sendMessage($sender, "§4[Error] §cYou must be a gang leader to set the motd/desc!");
                        break;
                    }
                    $gang = $user->getGang();
                    if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                        $this->sendMessage($sender, "§4[Error]§c Gang not online");
                        break;
                    }
                    if (!$gangc->isLeader($sender->getName())) {
                        $this->sendMessage($sender, "§4[Error] §cYou must be a gang leader to set the motd/desc!");
                        break;
                    }
                    array_shift($args);
                    $m = implode(" ", $args);
                    if (!$this->pl->isStringValid($m)) {
                        $this->sendMessage($sender, "§4[Error]§c MOTD not valid, Please do not use special characters!");
                        break;
                    }
                    if (str_contains($m, "'") or str_contains($m, '"')) {
                        $this->sendMessage($sender, "§4[Error] §cMOTD cannot contain quotes!");
                        break;
                    }
                    if (strlen($m) > 30) {
                        $this->sendMessage($sender, "§4[Error] §cGang motd can only have 30 letters or numbers!");
                        break;
                    }
                    $gangc->setMotd(TextFormat::clean($m));
                    $this->sendMessage($sender, "§eGang's motd/desc set successfully! Use /g info to check");

                    break;
                case "remove":
                case "kick":
                case "removemember":

                    if (!$user->hasGang()) {
                        $this->sendMessage($sender, "§4[Error] §cYou must be a gang leader to remove a member!");
                        break;
                    } else {
                        if (!isset($args[1])) {
                            $this->sendMessage($sender, "§cUsage: /g kick <player>");
                            break;
                        }
                        $gang = $user->getGang();
                        if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                            $this->sendMessage($sender, "§4[Error]§c Gang not online");
                            break;
                        }
                        if (!$gangc->isLeader($sender->getName())) {
                            $this->sendMessage($sender, "§4[Error] §cYou must be a gang leader to remove a member!");
                            break;
                        }
                        $player = strtolower($args[1]);
                        if (strtolower($sender->getName()) == $player) {
                            $this->sendMessage($sender, "§4[Error] §cYou can't kick yourself from the gang! Use /g delete to delete the gang!");
                            break;
                        }
                        if (!$gangc->isAMember($player)) {
                            $this->sendMessage($sender, "§4[Error] §a{$player} §cisn't a member of your gang!");
                            break;
                        }
                        $gangc->removeMember($player);
                        $gangc->removeMemberKill($player);
                        $gangc->removeMemberDeath($player);
                        if (($user2 = $this->um->getOnlineUser($player)) !== null) {
                            $user2->setGang();
                            $gangc->removeOnline($player);
                            $this->plugin->getGangChatHandler()->removePlayerFromChat($user2->getPlayer());
                            $this->sendMessage($user2->getPlayer(), "§cYou have been kicked from gang §a{$gang}§c!");
                        }
                        $this->db->removePlayerGang($player, $gang);
                        $this->sendMessage($sender, "§a{$player} §ewas kicked from your gang §a$gang §esuccessfully!");
                    }
                    break;

                case "about":
                case "version":

                    $this->sendMessage($sender, "§6Gangs §bv1.0.0 §eby §aInfernus101");

                    break;

                case "level":
                case "seelevel":

                    if (!isset($args[1])) {
                        if (!$user->hasGang()) {
                            $this->sendMessage($sender, "§4[Error] §cYou must be in a gang to do this");
                            break;
                        }
                        $gang = $user->getGang();
                        if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                            $this->sendMessage($sender, "§4[Error] §cGang not online!");
                            break;
                        }
                        $this->sendMessage($sender, "§eYour Gang is on §b{$gangc->getLevel()} §elevel");
                    } else {
                        if (!$this->db->isGangNameUsed($args[1])) {
                            $this->sendMessage($sender, "§4[Error] §cGang does not exist");
                            return;
                        }
                        $gang = strtolower($args[1]);
                        if (($gangc = $this->gm->getOnlineGang($gang)) !== null) {
                            $this->sendMessage($sender, "§a{$gang} §egang is on §b{$gangc->getLevel()} §elevel");
                        } else {
                            $this->sendMessage($sender, "§a{$gang} §egang is on §b{$this->db->getGangLevel($gang)} §elevel");
                        }
                    }
                    break;

                case "gdel":

                    if (!isset($args[2])) {
                        $this->sendMessage($sender, "§6Usage: /g gdel <gang> <reason>");
                        break;
                    }
                    if (!$this->plugin->hasOp($sender)) {
                        $this->sendMessage($sender, "§4[Error] §cNo permission!");
                        return;
                    }
                    $gang = strtolower($args[1]);
                    if (!$this->db->isGangNameUsed($gang)) {
                        $this->sendMessage($sender, "The requested Gang does not exist");
                        break;
                    }
                    array_shift($args);
                    array_shift($args);
                    $reason = implode(" ", $args);
                    $reason = trim($reason, "'");
                    if (strlen($reason) < 5) {
                        $this->sendMessage($sender, "§4[Error]§c> Please write a reason more than 5 letters!");
                        break;
                    }
                    if (($gangc = $this->gm->getOnlineGang($gang)) !== null) {
                        $online = $gangc->getOnline();
                        if (!empty($online)) {
                            foreach ($online as $mem) {
                                if (strtolower($mem) != strtolower($sender->getName())) {
                                    if (($user2 = $this->um->getOnlineUser($mem)) !== null) {
                                        $this->sendMessage($user2->getPlayer(), "> §cStaff deleted your §a$gang §cGang for reason - §a`$reason`!");
                                        $user2->setGang();
                                    }
                                }
                            }
                        }
                        $this->gm->setGangOffline($gang);
                        $this->pl->getGangChatHandler()->setChatOffline(strtolower($gang));
                    }
                    $this->db->delGang($gang);
                    $this->sendMessage($sender, "§eGang §a$gang §ewas successfully deleted.");
                    $this->pl->sendDiscordMessage("Gang Deletion!", "Gang `$gang` was deleted by {$sender->getName()} for Reason - **$reason**!\n", 5);

                    break;

                case "gsetlevel":

                    if (!$this->plugin->hasOp($sender)) {
                        $this->sendMessage($sender, "§4[Error] §cNo permission!");
                        return;
                    }
                    if (!isset($args[1]) or !isset($args[2])) {
                        $this->sendMessage($sender, "§6Usage: /g gsetlevel <gang> <level>");
                        break;
                    }
                    $gang = strtolower($args[1]);
                    if (!$this->db->isGangNameUsed($gang)) {
                        $this->sendMessage($sender, "§4[Error] §cThe requested gang does not exist");
                        break;
                    }
                    if (!is_int((int) $args[2]) || $args[2] < 0) {
                        $this->sendMessage($sender, "§4[Error] §cInvalid level given!");
                        break;
                    }
                    $level = (int) $args[2];
                    if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                        $this->db->setGangLevel($gang, $level);
                    } else {
                        $gangc->setLevel($level);
                    }
                    $this->sendMessage($sender, "§eSuccessfully set {$gang} gang to {$level} level!");

                    break;

                case "gsetpoints":

                    if (!$this->plugin->hasOp($sender)) {
                        $this->sendMessage($sender, "§4[Error] §cNo permission!");
                        return;
                    }
                    if (!isset($args[1]) or !isset($args[2])) {
                        $this->sendMessage($sender, "§6Usage: /g gsetpoints <gang> <points>");
                        break;
                    }
                    $gang = strtolower($args[1]);
                    if (!$this->db->isGangNameUsed($gang)) {
                        $this->sendMessage($sender, "§4[Error] §cThe requested gang does not exist");
                        break;
                    }
                    if (!is_int((int) $args[2]) || $args[2] < 0) {
                        $this->sendMessage($sender, "§4[Error] §cInvalid points given!");
                        break;
                    }
                    $points = (int) $args[2];
                    if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                        $this->db->setGangPoints($gang, $points);
                    } else {
                        $gangc->hardsetPoints($points);
                    }
                    $this->sendMessage($sender, "§eSuccessfully set {$gang} gang to {$points} points!");

                    break;

                case "online":

                    if (!isset($args[1])) {
                        if (!$user->hasGang()) {
                            $this->sendMessage($sender, "§4[Error] §cYou must be in a gang to do this, or use /g online <gang>");
                            break;
                        }
                        $gang = $user->getGang();
                        if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                            $this->sendMessage($sender, "§4[Error]§c Gang not online");
                            break;
                        }
                        $this->sendMessage($sender, "§eONLINE LIST OF YOUR GANG §a$gang :-\n");
                    } else {
                        $gang = strtolower($args[1]);
                        if (!$this->db->isGangNameUsed($gang)) {
                            $this->sendMessage($sender, "§4[Error]§c Gang does not exist");
                            break;
                        }
                        if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                            $this->sendMessage($sender, "§4[Error]§c No member of that gang is online!");
                            break;
                        }
                        $this->sendMessage($sender, "§eONLINE LIST OF GANG §a$gang :-\n");
                    }
                    $sender->sendMessage("= §a{$gangc->getOnlineString()}");
                    break;

                case "top":

                    if (!isset($args[1])) {
                        $args[1] = 1;
                    }
                    if (isset($args[1]) and !is_int((int) $args[1]) or $args[1] < 1) {
                        $this->sendMessage($sender, "§4[Error]§e Please enter a valid page number!");
                        break;
                    }
                    $args[1] = (int) $args[1];
                    $array = $this->plugin->db->prepare("SELECT count(*) as count FROM creator ORDER BY level DESC;")->execute();
                    $array = $array->fetchArray(SQLITE3_ASSOC);
                    $total = $array['count'];
                    $pages = ceil($total / 10);
                    $page = $args[1];
                    if ($pages < $page) {
                        $this->sendMessage($sender, "§4[Error]§e That page cannot be found.\nLast page = $pages");
                        break;
                    }
                    $startnum = ($page - 1) * 10;
                    $str = TF::GREEN . "Top gangs by level list -\n";
                    $str .= TF::YELLOW . '[+]' . TF::WHITE . str_repeat('=', 10) . TF::AQUA . '[ ' . $page . '/' . $pages . ' ]' . TF::WHITE . str_repeat('=', 10) . TF::YELLOW . '[+]' . "\n";
                    $array = $this->plugin->db->prepare("SELECT gang, level FROM creator ORDER BY level DESC LIMIT $startnum, 10;")->execute();
                    while ($result = $array->fetchArray(SQLITE3_ASSOC)) {
                        $gang = $result['gang'];
                        $level = $result['level'];
                        ++$startnum;
                        if (($gangc = $this->gm->getOnlineGang($gang)) === null) {
                            $str .= $startnum . ". §fGang: §a$gang §e=> §fLevel: §d$level \n";
                        } else {
                            $str .= $startnum . ". §fGang: §a$gang §e=> §fLevel: §d{$gangc->getLevel()}\n";
                        }
                    }
                    $this->sendMessage($sender, $str . TF::YELLOW . '[+]' . TF::WHITE . str_repeat('=', 26) . TF::YELLOW . '[+]' . "\n=> Pages will be reloaded after restart! <=\n§e=> §6For more info on a gang, use /g info <gang>! §e<=");

                    break;

            }
        }
    }
}