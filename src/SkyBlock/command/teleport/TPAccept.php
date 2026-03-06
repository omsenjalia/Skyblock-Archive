<?php


namespace SkyBlock\command\teleport;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\Values;

class TPAccept extends BaseCommand {
    /**
     * TPAccept constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'tpaccept', 'Accept a teleport request');
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendUsage($sender, $commandLabel);
            return;
        }
        if (isset($args[0])) {
            $this->sendMessage($sender, "§cUsage: /tpaccept");
            return;
        }
        if (!isset($this->pl->teleport[strtolower($sender->getName())])) {
            $this->sendMessage($sender, "§4[Error]§c You haven't got any teleport requests!");
            return;
        }
        $requester = implode("", array_keys($this->pl->teleport[strtolower($sender->getName())]));
        if (($user2 = $this->um->getOnlineUser($requester)) === null) {
            $this->sendMessage($sender, "§4[Error]§c Requester is not online anymore! Request deleted!");
            unset($this->pl->teleport[strtolower($sender->getName())]);
            return;
        }
        $time = $this->pl->teleport[strtolower($sender->getName())][$requester]['time'];
        $currentTime = time();
        if ($currentTime - $time >= 60) {
            $this->sendMessage($sender, "§4[Error]§c Request timed out! Request deleted!");
            unset($this->pl->teleport[strtolower($sender->getName())]);
            return;
        }
        $type = $this->pl->teleport[strtolower($sender->getName())][$requester]['type'];
        if ($type == 'tpa') {
            if (($island = $this->im->getOnlineIslandByWorld($sender->getPosition()->getWorld()->getDisplayName())) !== null) {
                if ($island->isLocked() && !$island->isMember($requester)) {
                    $this->sendMessage($sender, "§4[Error]§c You're in an locked island! Only members of this island can teleport here! Request deleted!");
                    unset($this->pl->teleport[strtolower($sender->getName())]);
                    return;
                }
                if ($island->isBanned($requester)) {
                    $this->sendMessage($sender, "§4[Error]§c That player is in an island where you're banned! Request deleted");
                    unset($this->pl->teleport[strtolower($sender->getName())]);
                    return;
                }
            }
            if ($sender->getPosition()->getWorld()->getDisplayName() === Values::PVP_WORLD) {
                $this->sendMessage($sender, "§4[Error]§c You're in PvP world! Request deleted!");
                unset($this->pl->teleport[strtolower($sender->getName())]);
                return;
            }
            if ($user2->getPlayer()->getPosition()->getWorld()->getDisplayName() === Values::PVP_WORLD) {
                $this->sendMessage($sender, "§4[Error]§c Requester is in PvP world! Request deleted!");
                unset($this->pl->teleport[strtolower($sender->getName())]);
                return;
            }
            if ($sender->getPosition()->getWorld()->getDisplayName() === Values::NETHER_WORLD) {
                $func = function(Player $player2, ?bool $data) use ($requester, $sender, $user2) : void {
                    unset($this->pl->teleport[strtolower($sender->getName())]);
                    if ($data) {
                        if (($island = $this->im->getOnlineIslandByWorld($sender->getPosition()->getWorld()->getDisplayName())) !== null) {
                            if ($island->isLocked() && !$island->isMember($requester)) {
                                $this->sendMessage($sender, "§4[Error]§c You're in an locked island! Only members of this island can teleport here! Request deleted!");
                                unset($this->pl->teleport[strtolower($sender->getName())]);
                                return;
                            }
                            if ($island->isBanned($requester)) {
                                $this->sendMessage($sender, "§4[Error]§c That player is in an island where you're banned! Request deleted");
                                unset($this->pl->teleport[strtolower($sender->getName())]);
                                return;
                            }
                        }
                        if ($this->pl->isInCombat($sender)) {
                            $this->sendMessage($sender, "§4[Error]§c You're in combat! Request deleted");
                            unset($this->pl->teleport[strtolower($sender->getName())]);
                            return;
                        }
                        if ($this->pl->isInCombat($user2->getPlayer())) {
                            $this->sendMessage($sender, "§4[Error]§c That player is in combat! Request deleted");
                            unset($this->pl->teleport[strtolower($sender->getName())]);
                            return;
                        }
                        if ($sender->getPosition()->getWorld()->getDisplayName() === Values::PVP_WORLD) {
                            $this->sendMessage($sender, "§4[Error]§c You're in PvP world! Request deleted!");
                            unset($this->pl->teleport[strtolower($sender->getName())]);
                            return;
                        }
                        if ($user2->getPlayer()->getPosition()->getWorld()->getDisplayName() === Values::PVP_WORLD) {
                            $this->sendMessage($sender, "§4[Error]§c Requester is in PvP world! Request deleted!");
                            unset($this->pl->teleport[strtolower($sender->getName())]);
                            return;
                        }
                        $player2->teleport($sender->getLocation());
                        $this->sendMessage($player2, "§a{$sender->getName()} §eaccepted your teleport request! §bTeleporting...");
                        $this->sendMessage($sender, "§eTeleport request accepted! §bTeleporting §a{$requester} §bto you...");
                        return;
                    }
                    $this->sendMessage($sender, "§a{$player2->getName()} §edenied your teleport request!");
                    $this->sendMessage($player2, "§eTeleport request denied!");
                };
                $this->formfunc->sendModalForm($user2->getPlayer(), "Teleportation Warning", "§cThe player you're teleporting to is in Nether world where PvP is on, its possible you'll get killed/trapped. P.S: Tp trapping is allowed.", ["Accept", "Deny"], $func);
                return;
            }
            $user2->getPlayer()->teleport($sender->getPosition());
            unset($this->pl->teleport[strtolower($sender->getName())]);
            $this->sendMessage($user2->getPlayer(), "§a{$sender->getName()} §eaccepted your teleport request! §bTeleporting...");
            $this->sendMessage($sender, "§eTeleport request accepted! §bTeleporting §a{$requester} §bto you...");
        }
        if ($type == 'tpahere') {
            if (($island = $this->im->getOnlineIslandByWorld($user2->getPlayer()->getPosition()->getWorld()->getDisplayName())) !== null) {
                if ($island->isLocked() && !$island->isMember($sender->getName())) {
                    $this->sendMessage($sender, "§4[Error]§c Requester is in an locked island! Only members of that island can teleport there! Request deleted!");
                    unset($this->pl->teleport[strtolower($sender->getName())]);
                    return;
                }
                if ($island->isBanned($sender->getName())) {
                    $this->sendMessage($sender, "§4[Error]§c That player is banned on the island you're on! Request deleted!");
                    unset($this->pl->teleport[strtolower($sender->getName())]);
                    return;
                }
            }
            if ($sender->getPosition()->getWorld()->getDisplayName() === Values::PVP_WORLD) {
                $this->sendMessage($sender, "§4[Error]§c You're in PvP world! Request deleted!");
                unset($this->pl->teleport[strtolower($sender->getName())]);
                return;
            }
            if ($user2->getPlayer()->getPosition()->getWorld()->getDisplayName() === Values::PVP_WORLD) {
                $this->sendMessage($sender, "§4[Error]§c Requester is in PvP world! Request deleted!");
                unset($this->pl->teleport[strtolower($sender->getName())]);
                return;
            }
            if ($user2->getPlayer()->getPosition()->getWorld()->getDisplayName() === Values::NETHER_WORLD) {
                $func = function(Player $sender, ?bool $data) use ($user2) : void {
                    unset($this->pl->teleport[strtolower($sender->getName())]);
                    if ($data) {
                        if (($island = $this->im->getOnlineIslandByWorld($user2->getPlayer()->getPosition()->getWorld()->getDisplayName())) !== null) {
                            if ($island->isLocked() && !$island->isMember($sender->getName())) {
                                $this->sendMessage($sender, "§4[Error]§c Requester is in an locked island! Only members of that island can teleport there! Request deleted!");
                                return;
                            }
                            if ($island->isBanned($sender->getName())) {
                                $this->sendMessage($sender, "§4[Error]§c That player is banned on the island you're on! Request deleted!");
                                return;
                            }
                        }
                        if ($this->pl->isInCombat($sender)) {
                            $this->sendMessage($sender, "§4[Error]§c You're in combat! Request deleted");
                            unset($this->pl->teleport[strtolower($sender->getName())]);
                            return;
                        }
                        if ($this->pl->isInCombat($user2->getPlayer())) {
                            $this->sendMessage($sender, "§4[Error]§c That player is in combat! Request deleted");
                            unset($this->pl->teleport[strtolower($sender->getName())]);
                            return;
                        }
                        if ($user2->getPlayer()->getPosition()->getWorld()->getDisplayName() === Values::PVP_WORLD) {
                            $this->sendMessage($sender, "§4[Error]§c Requester is in PvP world! Request deleted!");
                            unset($this->pl->teleport[strtolower($sender->getName())]);
                            return;
                        }
                        if ($sender->getPosition()->getWorld()->getDisplayName() === Values::PVP_WORLD) {
                            $this->sendMessage($sender, "§4[Error]§c You're in PvP world! Request deleted!");
                            unset($this->pl->teleport[strtolower($sender->getName())]);
                            return;
                        }
                        $sender->teleport($user2->getPlayer()->getLocation());
                        $this->sendMessage($user2->getPlayer(), "§a{$sender->getName()} §eaccepted your teleport request! §bTeleporting...");
                        $this->sendMessage($sender, "§eTeleport request accepted! §bTeleporting §a{$sender->getName()} §bto you...");
                        return;
                    }
                    $this->sendMessage($user2->getPlayer(), "§a{$sender->getName()} §edenied your teleport request!");
                    $this->sendMessage($sender, "§eTeleport request denied!");
                };
                $this->formfunc->sendModalForm($sender, "Teleportation Warning", "§cThe player you're teleporting to is in Nether world where PvP is on, its possible you'll get killed/trapped. P.S: Tp trapping is allowed.", ["Accept", "Deny"], $func);
                return;
            }
            $sender->teleport($user2->getPlayer()->getPosition());
            unset($this->pl->teleport[strtolower($sender->getName())]);
            $this->sendMessage($user2->getPlayer(), "§a{$sender->getName()} §eaccepted your teleport request! §bTeleporting...");
            $this->sendMessage($sender, "§eTeleport request accepted! §bTeleporting you to §a{$requester}...");
        }
    }
}