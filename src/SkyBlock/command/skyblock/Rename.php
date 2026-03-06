<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Data;
use SkyBlock\Main;
use SkyBlock\user\User;

class Rename extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'rename', 'Rename your island');
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!isset($args[1]) or isset($args[2])) {
            $this->sendMessage($sender, "§6Usage: /is rename <name>");
            return;
        }
        if (!$user->hasIsland()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be an island owner to rename your island!");
            return;
        }
        if (isset($this->pl->rename[$user->getIsland()])) {
            $this->sendMessage($sender, "§4[Error] §cYou have already sent a rename request for this restart, your island will be renamed after restart!");
            return;
        }
        $cost = Data::$commandIslandRenameCost;
        if (!$user->hasMoney($cost)) {
            $this->sendMessage($sender, "§4[Error] §cYou need $cost$ to rename your island everytime!");
            return;
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
            $this->sendMessage($sender, "§4[Error] §cSomeone else is already renaming their island that name, next restart!");
            return;
        }
        $user->removeMoney($cost);
        $this->pl->rename[$user->getIsland()] = $args[1];
        $this->sendMessage($sender, "§eYour §b{$user->getIsland()} §eisland will be renamed to §b{$args[1]} §enext restart! §eUsed §6$cost$");

    }

}