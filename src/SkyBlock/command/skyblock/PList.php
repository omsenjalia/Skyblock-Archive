<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\world\World;
use SkyBlock\Main;
use SkyBlock\user\User;

class PList extends BaseSkyblock {

    /**
     * PList constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'list', 'Lists all players on your island currently');
    }

    /**
     * @param Player $sender
     * @param User   $user
     * @param array  $args
     */
    public function execute(Player $sender, User $user, array $args) : void {
        if (isset($args[1])) {
            $islandName = $args[1];
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $this->sendMessage($sender, "§cIsland not online/found");
                return;
            }
            $level = $this->pl->getServer()->getWorldManager()->getWorldByName($island->getId());
            if (is_null($level)) {
                $this->sendMessage($sender, "§4[Error] §cWorld not loaded yet!");
            } else {
                $players = self::getPlayerList($level);
                $this->sendMessage($sender, "§eList of players on island §d{$island->getName()} §ecurrently: §6" . count($players) . " §eplayers\n- §a" . implode(", ", $players));
            }
            return;
        }
        if (!isset($args[1])) {
            if (!$user->isIslandSet()) {
                $this->sendMessage($sender, "§cUsage: /is list <island>");
                return;
            }
            $islandName = $user->getIsland();
            $island = $this->im->getOnlineIsland($islandName);
            $level = $this->pl->getServer()->getWorldManager()->getWorldByName($island->getId());
            if (is_null($level)) {
                $this->sendMessage($sender, "§4[Error] §cWorld not loaded yet!");
            } else {
                $players = self::getPlayerList($level);
                $this->sendMessage($sender, "§eList of players on your island §d{$island->getName()} §ecurrently: §6" . count($players) . " §eplayers\n- §a" . implode(", ", $players));
            }
        }
    }

    /**
     * @param World $level
     *
     * @return array
     */
    private static function getPlayerList(World $level) : array {
        $rawplayers = $level->getPlayers();
        $players = [];
        foreach ($rawplayers as $p) {
            if ($p->getGamemode() !== GameMode::SPECTATOR()) {
                $players[] = $p->getDisplayName();
            }
        }
        return $players;
    }

}