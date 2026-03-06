<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\user\User;

class Motd extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'motd', "Set your island's motd/desc", ['desc', 'description']);
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!isset($args[1])) {
            $this->sendMessage($sender, "§cUsage: /is motd <message>");
            return;
        }
        if (!$user->hasIsland()) {
            $this->sendMessage($sender, "§4[Error] §cYou must be an island owner to set the motd/desc!");
            return;
        }
        array_shift($args);
        $m = implode(" ", $args);
        if (!$this->pl->isStringValid($m)) {
            $this->sendMessage($sender, "§4[Error]§c MOTD not valid, Please do not use special characters!");
            return;
        }
        if (str_contains($m, "'") or str_contains($m, '"')) {
            $this->sendMessage($sender, "§4[Error] §cMOTD cannot contain quotes!");
            return;
        }
        if (strlen($m) > 20) {
            $this->sendMessage($sender, "§4[Error] §cMOTD can only have 20 letters or numbers!");
            return;
        }
        $islandName = $user->getIsland();
        if (($island = $this->im->getOnlineIsland($islandName)) === null) {
            $this->sendMessage($sender, "§4[Error]§c Island not online");
            return;
        }
        $island->setMotd(TextFormat::clean($m));
        $this->sendMessage($sender, "§eIsland's motd set successfully! Use /is info to check");
    }

}