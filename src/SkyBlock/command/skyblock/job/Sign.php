<?php


namespace SkyBlock\command\skyblock\job;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

class Sign extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'sign');
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (isset($args[1])) {
            $player = $this->plugin->getServer()->getPlayerByPrefix(strtolower($args[1]));
            if ($player instanceof Player and $player->isOnline()) {
                $playerName = strtolower($player->getName());
                if (!isset($this->pl->requests[$playerName][strtolower($sender->getName())])) {
                    $this->sendMessage($sender, "§4[Error] §cYou haven't received any island job requests from §a$playerName");
                    return;
                }
                $islandName = $this->pl->requests[$playerName][strtolower($sender->getName())]['island'];
                if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                    $this->sendMessage($sender, "§4[Error]§c Island Owners not online, request removed!");
                    unset($this->pl->requests[$playerName][strtolower($sender->getName())]);
                    return;
                }
                if ($island->isBanned(strtolower($sender->getName()))) {
                    $this->sendMessage($sender, "§cYou were banned from that island by the Island Owner!");
                    return;
                }
                if ($island->isHelper(strtolower($sender->getName()))) {
                    $this->sendMessage($sender, "§4[Error] §cYou're a helper on that island!");
                    return;
                }
                if ($island->hasARole($sender->getName())) {
                    $this->sendMessage($sender, "§cYou already have a role on that island! §6Use /is fire to remove role first.");
                    return;
                }
                if ($island->getRoleCount() >= $island->getRoleLimit()) {
                    unset($this->pl->requests[$playerName][strtolower($sender->getName())]);
                    $this->sendMessage($sender, "§4[Error] §cThat island already has max jobs they can have at their level, higher level required!");
                    return;
                }
                $invitedTime = $this->pl->requests[$playerName][strtolower($sender->getName())]['time'];
                $job = $this->pl->requests[$playerName][strtolower($sender->getName())]['job'];
                $currentTime = time();
                if (($currentTime - $invitedTime) > 120) {
                    unset($this->pl->requests[$playerName][strtolower($sender->getName())]);
                    $this->sendMessage($sender, "§4[Error] §cInvitation timed out!");
                    return;
                }
                $island->addRole($sender->getName(), $job . "s");
                $this->sendMessage($player, TextFormat::RED . "-> " . TextFormat::YELLOW . "{$sender->getName()} accepted your {$job} job request. §f" . $island->getJobDesc($job . "s") . " on the Island now!");
                $this->sendMessage($sender, TextFormat::RED . "-> " . TextFormat::YELLOW . "You signed §a$islandName §eisland's {$job} job request!");
                unset($this->pl->requests[$playerName][strtolower($sender->getName())]);
                if (strtolower($player->getName()) != strtolower($island->getOwner())) {
                    if (($owner = $this->um->getOnlineUser($island->getOwner())) !== null) {
                        $this->sendMessage($owner->getPlayer(), "§e{$sender->getName()} §ejoined your Island for §b{$job} §ejob.\n§6Invited by CoOwner - §a{$player->getName()}");
                    }
                }
            } else {
                $this->sendMessage($sender, "§4[Error] §c{$args[1]} is not online anymore!");
            }
        } else {
            $this->sendMessage($sender, "§cUsage: /is sign <player>");
        }
    }

}