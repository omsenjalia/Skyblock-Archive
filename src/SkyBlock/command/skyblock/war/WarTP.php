<?php


namespace SkyBlock\command\skyblock\war;


use pocketmine\player\Player;
use pocketmine\scheduler\TaskHandler;
use pocketmine\world\Position;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;
use SkyBlock\util\Values;

/** @deprecated */
class WarTP extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'wartp', "Teleport to the war arena to fight");
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!isset($this->pl->warplayers[strtolower($sender->getName())])) {
            $this->sendMessage($sender, "§4[Error]§e You are not in any war!");
            return;
        }
        $islandName = $this->pl->warplayers[strtolower($sender->getName())];
        if ($this->pl->war[1]["island1"] == strtolower($islandName)) {
            $warp = new Position((float) $this->pl->wars[0]['x'], (float) $this->pl->wars[0]['y'] + 1, (float) $this->pl->wars[0]['z'], $this->pl->getServer()->getWorldManager()->getWorldByName(Values::PVP_WORLD));
        } else {
            $warp = new Position((float) $this->pl->wars[1]['x'], (float) $this->pl->wars[1]['y'] + 1, (float) $this->pl->wars[1]['z'], $this->pl->getServer()->getWorldManager()->getWorldByName(Values::PVP_WORLD));
        }
        if (isset($this->plugin->countdown[strtolower($sender->getName())])) {
            /** @var TaskHandler $task */
            $task = $this->plugin->countdown[strtolower($sender->getName())];
            $task->cancel();
            unset($this->plugin->countdown[strtolower($sender->getName())]);
        }
        $warp->getWorld()->loadChunk($warp->getFloorX() >> 4, $warp->getFloorZ() >> 4);
        $sender->teleport($warp);
    }

}