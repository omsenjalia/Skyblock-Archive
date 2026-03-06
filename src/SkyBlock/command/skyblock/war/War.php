<?php


namespace SkyBlock\command\skyblock\war;


use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

/** @deprecated */
class War extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'war', "Send a war request to an island");
    }

    public function execute(Player $sender, User $user, array $args) : void {
        return;
        //        if (!isset($args[1])) {
        //            $this->sendMessage($sender, "§6Usage: /is war <island>");
        //            return;
        //        }
        //        if (!ctype_alnum($args[1])) {
        //            $this->sendMessage($sender, "§4[Error] §cOnly letters and numbers allowed!");
        //            return;
        //        }
        //        if (!$user->hasIsland()) {
        //            $this->sendMessage($sender, "§4[Error] §cYou need to be an island owner to go at war with an island!");
        //            return;
        //        }
        //        $island = $user->getIsland();
        //        $enemy = strtolower($args[1]);
        //        if (strtolower($island) == $enemy) {
        //            $this->sendMessage($sender, "§4[Error] §cCan't go on war with your own island!");
        //            return;
        //        }
        //        if (($enemyclass = $this->im->getOnlineIsland($enemy)) === null) {
        //            $this->sendMessage($sender, "§4[Error] §cThat island is not online!");
        //            return;
        //        }
        //        $islandclass = $this->im->getOnlineIsland($island);
        //        if (isset($this->pl->donewar[$islandclass->getName()])) {
        //            $this->sendMessage($sender, "§4[Error] §cYou already went on a war this restart! Try again next restart");
        //            return;
        //        }
        //        if (isset($this->pl->donewar[$enemyclass->getName()])) {
        //            $this->sendMessage($sender, "§4[Error] §cEnemy island already went on a war this restart! Try different enemy island");
        //            return;
        //        }
        //        $owner = $enemyclass->getOwnerLowerCase();
        //        if (($user2 = $this->um->getOnlineUser($owner)) === null) {
        //            $this->sendMessage($sender, "§4[Error] §cThat island's owner is not online!");
        //            return;
        //        }
        //        if (isset($this->pl->warreq[$owner])) {
        //            $time = $this->pl->warreq[$owner]["time"];
        //            $now = time();
        //            if (($now - $time) > 60) unset($this->pl->warreq[$owner]);
        //            else {
        //                $this->sendMessage($sender, "§4[Error] §cYou have already sent request for war to that island, wait for timeout!");
        //                return;
        //            }
        //        }
        //        $helpers = $islandclass->getHelpers();
        //        $ehelpers = $enemyclass->getHelpers();
        //        $diff = false;
        //        foreach ($helpers as $h) {
        //            if (in_array(strtolower($h), $ehelpers, true)) {
        //                $this->sendMessage($sender, "§4[Error] §a$h §cis helper on §d$enemy §cisland too! Either kick him from your island or ask him to leave enemy's island to start the war!");
        //                $diff = true;
        //            }
        //        }
        //        if ($diff == true) return;
        //        if (in_array(strtolower($sender->getName()), $ehelpers, true)) {
        //            $this->sendMessage($sender, "§4[Error] §cYou are helper on §d$enemy §cisland! Leave enemy's island to start the war!");
        //            return;
        //        }
        //        $timeleft = (int)($this->pl->gandalf->rtime / 60);
        //        if ($timeleft <= 5) {
        //            $this->sendMessage($sender, "§eOnly §c$timeleft §emins left for restart, start War next restart!");
        //            return;
        //        }
        //        if (!$islandclass->hasPoints(5000)) {
        //            $this->sendMessage($sender, "§4[Error] §cYour island needs to have 5000 total island points to start war! Do /is points to see your island's total points till now!");
        //            return;
        //        }
        //        if (!$enemyclass->hasPoints(5000)) {
        //            $this->sendMessage($sender, "§4[Error] §cEnemy island needs to have 5000 total island points to start war!");
        //            return;
        //        }
        //        $this->pl->donewar[] = $islandclass->getName();
        //        $this->pl->donewar[] = $enemyclass->getName();
        //        $this->pl->warreq[$user2->getLowerCaseName()] = ["requester" => $user->getLowerCaseName(), "time" => time()];
        //        $this->sendMessage($sender, "- §eYou successfully sent war request to owner §a{$user2->getName()} §eof island §d{$enemy}! §bRequest will timeout in 60 seconds!");
        //        $this->sendMessage($user2->getPlayer(), "- §eYou got a war request from §a{$user->getName()} §efrom island §d{$island}§e, use /is waraccept to accept war request, /is wardeny to deny war request! §bRequest will timeout in 60 seconds!");

    }

}