<?php


namespace SkyBlock\command\skyblock\war;


use pocketmine\player\Player;
use pocketmine\world\Position;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\tasks\IslandWar;
use SkyBlock\user\User;
use SkyBlock\util\Values;

/** @deprecated */
class WarAccept extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'waraccept');
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (isset($args[1])) {
            $this->sendMessage($sender, "§6Usage: /is waraccept");
            return;
        }
        if (!$user->hasIsland()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be an island owner to go at war with an island!");
            return;
        }
        if (!isset($this->pl->warreq[strtolower($sender->getName())])) {
            $this->sendMessage($sender, "§4[Error] §cYou haven't recieved any war requests!");
            return;
        }
        $requester = $this->pl->warreq[strtolower($sender->getName())]["requester"];
        if (($user2 = $this->um->getOnlineUser($requester)) === null) {
            unset($this->pl->warreq[strtolower($sender->getName())]);
            $this->sendMessage($sender, "§4[Error] §cRequester went offline!");
            return;
        }
        if (!$user2->hasIsland()) {
            unset($this->pl->warreq[strtolower($sender->getName())]);
            $this->sendMessage($sender, "§4[Error] §cRequester doesn't have an island!");
            return;
        }
        $island1Name = $user->getIsland();
        $island2Name = $user2->getIsland();
        if ($this->im->getOnlineIsland($island2Name) === null) {
            unset($this->pl->warreq[strtolower($sender->getName())]);
            $this->sendMessage($sender, "§4[Error] §cRequester's island not online!");
            return;
        }
        $time = $this->pl->warreq[strtolower($sender->getName())]["time"];
        $now = time();
        if (($now - $time) > 60) {
            unset($this->pl->warreq[strtolower($sender->getName())]);
            $this->sendMessage($sender, "§4[Error] §cRequest timed out!");
            return;
        }
        if (!$this->pl->vacant) {
            $island1 = $this->pl->war[1]["island1"];
            $island2 = $this->pl->war[1]["island2"];
            unset($this->pl->warreq[strtolower($sender->getName())]);
            $this->sendMessage($sender, "§4[Error] §cA war is already in progress between §a$island1 §cand §a$island2 §cislands!");
            return;
        }
        $timeleft = (int) ($this->pl->gandalf->rtime / 60);
        if ($timeleft <= 5) {
            $this->sendMessage($sender, "§eOnly §c$timeleft §emins left for restart, start War next restart!");
            return;
        }
        $island1 = $this->im->getOnlineIsland($island1Name);
        $island2 = $this->im->getOnlineIsland($island2Name);
        if (!$island1->hasPoints(5000)) {
            unset($this->pl->warreq[strtolower($sender->getName())]);
            $this->sendMessage($sender, "§4[Error] §cYour island needs to have 5000 total island points to start war! Do /is points to see your island's total points till now!");
            return;
        }
        if (!$island2->hasPoints(5000)) {
            unset($this->pl->warreq[strtolower($sender->getName())]);
            $this->sendMessage($sender, "§4[Error] §cEnemy island needs to have 5000 total island points to start war!");
            return;
        }
        $helpers = $island1->getHelpers();
        $ehelpers = $island2->getHelpers();
        $diff = false;
        foreach ($helpers as $h) {
            if (in_array(strtolower($h), $ehelpers, true)) {
                $this->sendMessage($sender, "§4[Error] §a$h §cis helper on §d$island2Name §cisland! Either kick him from your island or ask him to leave enemy's island to start the war!");
                $diff = true;
            }
        }
        if ($diff == true) return;
        if (in_array(strtolower($sender->getName()), $ehelpers, true)) {
            $this->sendMessage($sender, "§4[Error] §cYou are helper on §d$island2Name §cisland! Leave enemy's island to start the war!");
            return;
        }
        foreach ($helpers as $h) {
            if (($user3 = $this->um->getOnlineUser($h)) !== null) {
                $this->sendMessage($user3->getPlayer(), "§eHelper Island §a$island1Name §eis now at war with island §d$island2Name! §eWar will last for 5 mins! §6Island with the most points at the end of the war will win and collect the rewards! §bDo /is wartp to teleport to the war arena!");
                $this->pl->warplayers[strtolower($h)] = strtolower($island1Name);
            }
        }
        foreach ($ehelpers as $h) {
            if (($user3 = $this->um->getOnlineUser($h)) !== null) {
                $this->sendMessage($user3->getPlayer(), "§eHelper Island §a$island2Name §eis now at war with island §d$island1Name! §eWar will last for 5 mins! §6Island with the most points at the end of the war will win and collect the rewards! §bDo /is wartp to teleport to the war arena!");
                $this->pl->warplayers[strtolower($h)] = strtolower($island2Name);
            }
        }
        $this->pl->warplayers[strtolower($sender->getName())] = strtolower($island1Name);
        $this->pl->warplayers[$requester] = strtolower($island2Name);
        $island1->setAtWar();
        $island1->setWarIsland($island2Name);
        $island2->setAtWar();
        $island2->setWarIsland($island1Name);
        $this->pl->warstart = time();
        $this->pl->vacant = false;
        $this->pl->war[1] = ["island1" => strtolower($island1Name), "island2" => strtolower($island2Name)];
        unset($this->pl->warreq[strtolower($sender->getName())]);
        $warp = new Position((float) $this->pl->wars[0]['x'], (float) $this->pl->wars[0]['y'] + 1, (float) $this->pl->wars[0]['z'], $this->pl->getServer()->getWorldManager()->getWorldByName(Values::PVP_WORLD));
        $warp->getWorld()->loadChunk($warp->getFloorX() >> 4, $warp->getFloorZ() >> 4);
        $sender->teleport($warp);
        $warp = new Position((float) $this->pl->wars[1]['x'], (float) $this->pl->wars[1]['y'] + 1, (float) $this->pl->wars[1]['z'], $this->pl->getServer()->getWorldManager()->getWorldByName(Values::PVP_WORLD));
        $warp->getWorld()->loadChunk($warp->getFloorX() >> 4, $warp->getFloorZ() >> 4);
        $user2->getPlayer()->teleport($warp);
        $this->sendMessage($sender, "§eYour island is now at war with island §d$island2Name! §eWar will last for 5 mins! §6Island with the most points at the end of the war will win and collect the rewards! §bDo /is wartp to teleport to the war arena! §eLoser loses 5000 island points! §dDeaths will reduce war points!");
        $this->sendMessage($user2->getPlayer(), "§eYour island is now at war with island §d$island1Name! §eWar will last for 5 mins! §6Island with the most points at the end of the war will win and collect the rewards! §bDo /is wartp to teleport to the war arena! §eLoser loses 5000 island points! §dDeaths will reduce war points!");
        $this->pl->getScheduler()->scheduleDelayedTask(new IslandWar($this->pl, strtolower($island1Name), strtolower($island2Name)), 5 * 60 * 20);

    }

}