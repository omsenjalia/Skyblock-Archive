<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class Version extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'version', "Skyblock's version info", ['vers', 'about']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        $this->sendMessage($sender, "§aSkyblock-Core §7v§6" . $this->pl->getDescription()->getVersion() . " §eby §bInfernus101");
    }

}