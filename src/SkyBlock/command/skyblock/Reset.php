<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class Reset extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'reset', "Reset's your island.", ['clear']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!$user->hasIsland()) {
            $this->sendMessage($sender, "§4[Error] §cYou must be the island owner to reset your island!");
        } else {
            if (!isset($this->pl->reset[strtolower($sender->getName())])) {
                $this->pl->reset[strtolower($sender->getName())] = true;
                $this->sendMessage($sender, "§eThis command will reset your island and start your island from the beginning with no stats(level 1 island), to confirm it, do §a/is reset confirm");
            } else {
                if (!isset($args[1])) {
                    $this->sendMessage($sender, "§ePlease enter confirm at the end to reset, §a/is reset confirm");
                    return;
                }
                if (strtolower($args[1]) == "confirm") {
                    $islandName = $user->getIsland();
                    if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                        $this->sendMessage($sender, "§4[Error] §cIsland not online!");
                        return;
                    }
                    if (isset($this->pl->resettime[strtolower($sender->getName())])) {
                        $resetTime = $this->pl->resettime[strtolower($sender->getName())];
                        $currentTime = time();
                        if (($left = $currentTime - $resetTime) <= 600) {
                            $minutes = (int) ((600 - $left) / 60);
                            $seconds = ((600 - $left) % 60);
                            $this->sendMessage($sender, "§5You'll be able to reset your island again in §d$minutes §5minutes and §d$seconds §5seconds");
                            return;
                        } else {
                            unset($this->pl->resettime[strtolower($sender->getName())]);
                        }
                    }
                    $helpers = $island->getHelpers();
                    if (!empty($helpers)) {
                        foreach ($helpers as $h) {
                            if (($user2 = $this->um->getOnlineUser($h)) !== null) {
                                $user2->removeIsland($islandName);
                            }
                        }
                    }
                    $coowners = $island->getCoowners();
                    if (!empty($coowners)) {
                        foreach ($coowners as $coowner) {
                            if (($user2 = $this->um->getOnlineUser($coowner)) !== null) {
                                $user2->setIsland();
                            }
                        }
                    }
                    $this->pl->destroyAllPrivateChests($sender->getName());
                    $this->db->delIsland($islandName);
                    $this->im->removeIsland($island->getId());
                    $this->im->setIslandOffline($islandName);
                    if (!$this->plugin->hasOp($sender)) $this->pl->resettime[strtolower($sender->getName())] = time();
                    $user->setIsland();
                    $this->plugin->getSkyBlockManager()->generateIsland($sender, $user, $islandName);
                    $this->sendMessage($sender, "§aYou successfully reset the island!");
                } else {
                    $this->sendMessage($sender, "§ePlease enter confirm at the end to reset, §a/is reset confirm");
                }
            }
        }
    }

}