<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class SetReceiver extends BaseSkyblock {

    /**
     * AutoMinerReceiver constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, "sr", "Choose which owner receives rewards of islands (mana, xp, money, mobcoin)", ['setreceiver']);
    }

    /**
     * @param Player $sender
     * @param User   $user
     * @param array  $args
     */
    public function execute(Player $sender, User $user, array $args) : void {
        if (!$user->hasIsland()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be the Island Owner to use that command!");
            return;
        }
        $islandName = $user->getIsland();
        if (($island = $this->im->getOnlineIsland($islandName)) === null) {
            $this->sendMessage($sender, "§4[Error] §cIsland not online!");
            return;
        }
        if (!isset($args[1]) or isset($args[2])) {
            $this->sendMessage($sender, "§cUsage: /is sr <player>");
            return;
        }
        $playerName = strtolower($args[1]);
        if (!$island->isAnOwner($playerName)) {
            $this->sendMessage($sender, "§4[Error] §cPlayer must be Owner or CoOwner of your Island!");
            return;
        }
        $island->setReceiver($playerName);
        $this->sendMessage($sender, "§a$playerName §ewill receive Island's rewards now (mana, xp, mobcoin)! They need to be online to receive.");
        if (($player = $this->pl->getServer()->getPlayerExact($playerName)) instanceof Player) {
            $this->sendMessage($player, "§eYou will receive Island's AutoMiner & AutoSeller rewards now (mana, xp, mobcoin)! §fIf you're offline, your island's random online owner will receive it instead.");
        }
    }

}