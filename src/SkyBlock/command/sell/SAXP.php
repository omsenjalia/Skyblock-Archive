<?php


namespace SkyBlock\command\sell;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class SAXP extends BaseCommand {
    /**
     * SAXP constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'saxp', 'Sell all inventory for XP', '', true, ['sellallxp']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
        if (!Main::getInstance()->staffapi->hasStaffRank($sender->getName())) {
            $level = 0;
            if ($user->isIslandSet()) {
                $islandName = $user->getIsland();
                $onlineIsland = Main::getInstance()->getIslandManager()->getOnlineIsland($islandName);
                $level = $onlineIsland !== null ? $onlineIsland->getLevel() : 0;
            }
            if (!$sender->hasPermission("core.sellallxp") && $level < 50) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Get your island to level 50 or buy any premium rank on " . TextFormat::AQUA . "shop.fallentech.io");
                return;
            }
        }
        if ($sender->isCreative()) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot sell items in creative mode!");
            return;
        }
        $items = $sender->getInventory()->getContents();
        $sold = [];
        foreach ($items as $item) {
            $data = Main::getInstance()->getEvFunctions()->sellItem($user, $item, "xp");
            if ($data !== null) {
                $key = $item->getTypeId() . ":" . $item->getStateId();
                if (isset($sold[$key])) {
                    $sold[$key]["count"] += $item->getCount();
                } else {
                    $sold[$key] = ["count" => $item->getCount(), "per" => $data[0], "boost" => $data[1]];
                    $sold[$key]["name"] = Main::getInstance()->getEvFunctions()->getSellItemName($item);
                }
                $sender->getInventory()->remove($item);
            }
        }
        $this->sendMessage($sender, Main::getInstance()->getEvFunctions()->getSellString($sold, "xp"));
    }
}