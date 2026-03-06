<?php


namespace SkyBlock\command\skyblock;


use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

/**
 * @deprecated
 * */
class Drain extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'drain', 'Drain/Delete the water and lava in your 16 block radius of the island.');
    }

    public function execute(Player $sender, User $user, array $args) : void {
        //        if ($user->hasIsland()) {
        //            $islandName = $user->getIsland();
        //            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
        //                $this->sendMessage($sender, "§4[Error] §cIsland not online!");
        //                return;
        //            }
        //            $levelname = $island->getId();
        //            if (($level = $this->pl->getServer()->getLevelByName($levelname)) == null) {
        //                $this->sendMessage($sender, "§4[Error] §cWorld not online!");
        //                return;
        //            }
        //            if ($sender->getPosition()->getWorld()->getDisplayName() != $levelname) {
        //                $this->sendMessage($sender, "§4[Error] §cYou can use this command only on your island!");
        //                return;
        //            }
        //            if (isset($this->pl->drain[strtolower($islandName)])) {
        //                $time = 60; // 1 min
        //                if (($left = time() - $this->pl->drain[strtolower($islandName)]) < $time) {
        //                    $this->sendMessage($sender, "§cYou need to wait §a" . ($time - $left) . " §cseconds to drain the island again! This is done to reduce lag");
        //                    return;
        //                } else unset($this->pl->drain[strtolower($islandName)]);
        //            }
        //            $reqmoney = 25000;
        //            if (!$island->removeMoney($reqmoney)) {
        //                $this->sendMessage($sender, "§4[Error] §cYour island bank does not have the required money for that! §eRequired Money: §6$reqmoney$\n§e=> Add Money in island bank by /is donate Ask your island helpers to help!");
        //                return;
        //            }
        //            $this->sendMessage($sender, "§eStarting the draining... Used §625,000$ §efrom island bank");
        //            $chunks = [];
        //            $a = new Vector3((int)$sender->x - 16, (int)$sender->y - 16, (int)$sender->z - 16); // lesser than b
        //            $b = new Vector3((int)$sender->x + 16, (int)$sender->y + 16, (int)$sender->z + 16);
        //            $chunkClass = Chunk::class;
        //            for ($x = $a->getX(); $x - 16 <= $b->getX(); $x += 16) {
        //                for ($z = $a->getZ(); $z - 16 <= $b->getZ(); $z += 16) {
        //                    $chunk = $level->getChunk($x >> 4, $z >> 4, true);
        //                    $chunkClass = get_class($chunk);
        //                    $chunks[Level::chunkHash($x >> 4, $z >> 4)] = $chunk->fastSerialize();
        //                }
        //            }
        //            $this->pl->drain[strtolower($islandName)] = time();
        //            //$this->pl->getServer()->getAsyncPool()->submitTask(new DrainTask($sender->getName(), $chunks, $a, $b, $level->getId(), $chunkClass));
        //        } else {
        //            $this->sendMessage($sender, "§4[Error] §cYou need to be Island Owner to use that command!");
        //            return;
        //        }
        $this->sendMessage($sender, "WIP");
    }

}