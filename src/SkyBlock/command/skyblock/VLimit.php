<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class VLimit extends BaseSkyblock {

    /**
     * VLimit constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, "vlimit", "Control the number of visitors that can join island");
    }

    /**
     * @param Player $sender
     * @param User   $user
     * @param array  $args
     */
    public function execute(Player $sender, User $user, array $args) : void {
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be Island Owner/Coowner to use that command!");
            return;
        }
        $islandName = $user->getIsland();
        if (($island = $this->im->getOnlineIsland($islandName)) === null) {
            $this->sendMessage($sender, "§4[Error]§c Island not online");
            return;
        }
        if (!isset($args[1]) or isset($args[2])) {
            $this->sendMessage($sender, "§cUsage: /is vlimit <limit = off/10>");
            return;
        }
        $vlimit = $args[1];
        if (strtolower($vlimit) === "off") {
            $island->setVLimit(0);
            $this->sendMessage($sender, "§eIsland Visitor limit is now turned off!");
            return;
        }
        if ((!is_int((int) $vlimit)) or ($vlimit > 100) or ($vlimit < 1)) {
            $this->sendMessage($sender, "§4[Error]§c Island Visitor limit should be a number between 0-100 or 'off'");
            return;
        }
        $vlimit = (int) $vlimit;
        $island->setVLimit((int) $vlimit);
        $this->sendMessage($sender, "§eIsland Visitor limit changed to §d{$vlimit}\n§7Only $vlimit visitors will be able to join Island now, members excluded. Use /is vlimit off to disable the limit.");
    }

}