<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class Create extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'create', 'Create a new island', ['make']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if ($user->isIslandSet()) {
            $islandName = $user->getIsland();
            $this->sendMessage($sender, "§4[Error] §cYou already got a Skyblock island §a$islandName! §cDo /is go, use /is leave or /is delete to remove!");
            return;
        }
        if (!isset($args[1]) or isset($args[2])) {
            $this->sendMessage($sender, "§4[Error] §cUsage: /is create <island name>");
            return;
        }
        if (isset($this->pl->resettime[strtolower($sender->getName())])) {
            $resetTime = $this->pl->resettime[strtolower($sender->getName())];
            if (strtolower($sender->getName()) == "infern101") $resetTime = 601;
            $currentTime = time();
            if (($left = $currentTime - $resetTime) <= 600) {
                $minutes = (int) ((600 - $left) / 60);
                $seconds = ((600 - $left) % 60);
                $this->sendMessage($sender, "§5You'll be able to create a new island in §4$minutes §cminutes §5and §4$seconds §cseconds.");
                return;
            } else {
                unset($this->pl->resettime[strtolower($sender->getName())]);
            }
        }
        if (!(ctype_alnum($args[1]))) {
            $this->sendMessage($sender, "§4[Error] §cNames can only include letters or numbers");
            return;
        }
        $len = strlen($args[1]);
        if ($len < 3 or $len > 15) {
            $this->sendMessage($sender, "§4[Error] §cName needs to be longer than 2 and smaller than 16 characters!");
            return;
        }
        if ($this->db->isNameUsed($args[1])) {
            $this->sendMessage($sender, "§4[Error] §cThat island name is already in use!");
            return;
        }
        $flag = false;
        foreach ($this->pl->rename as $renaming) {
            if (strtolower($renaming) == strtolower($args[1])) {
                $flag = true;
                break;
            }
        }
        if ($flag) {
            $this->sendMessage($sender, "§4[Error] §cThat island name is already in use!");
            return;
        }
        $this->plugin->getSkyBlockManager()->generateIsland($sender, $user, $args[1]);
        $this->sendMessage($sender, "§aYou successfully created an island named §e{$args[1]}§a! §bDo §5/is go §ato go on your island!");

    }

}