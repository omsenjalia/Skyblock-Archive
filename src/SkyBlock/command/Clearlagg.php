<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Clearlagg extends BaseCommand {
    /**
     * Clearlagg constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'clearlagg', 'Clear all entities', 'count', true, ['cl']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!Main::getInstance()->hasOp($sender)) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if ($sender instanceof Player && !Main::getInstance()->isTrusted($sender->getName())) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (isset($args[0])) {
            $type = strtolower($args[0]);
            if ($type === "count") {
                $count = Main::getInstance()->getFunctions()->getEntityCount();
                $this->sendMessage($sender, "Count: " . TextFormat::YELLOW . $count[0] . " Players, " . $count[1] . " Mobs, " . $count[2] . " Entities");
                return;
            }
            if ($type === "precise") {
                $count = Main::getInstance()->getFunctions()->getPreciseEntityCount();
                $this->sendMessage($sender, "Precise entity count: ");
                foreach ($count as $key => $c) {
                    $this->sendMessage($sender, TextFormat::GREEN . ucfirst($key) . TextFormat::WHITE . " => " . TextFormat::GREEN . $c);
                }
                return;
            }
            if ($type === "item") {
                $count = Main::getInstance()->getFunctions()->getItemEntityCount();
                $this->sendMessage($sender, "Item entity count: ");
                foreach ($count as $key => $c) {
                    $this->sendMessage($sender, TextFormat::GREEN . ucfirst($key) . TextFormat::WHITE . " => " . TextFormat::GREEN . $c);
                }
                return;
            }
            if ($type === "clearall") {
                $mobs = Main::getInstance()->getFunctions()->removeMobs();
                $entities = Main::getInstance()->getFunctions()->removeEntities();
                $this->sendMessage($sender, "Removed " . $mobs . " mob" . ($mobs === 1 ? "" : "s") . " and " . $entities . " entit" . ($entities === 1 ? "y" : "ies"));
                Server::getInstance()->broadcastPopup(TextFormat::RED . TextFormat::BOLD . ">> " . TextFormat::GREEN . "Clearing Entities and Mobs, check with /clt " . TextFormat::RED . "<<" . TextFormat::RESET);
                Main::getInstance()->clt = Main::getInstance()->clearlagtime;
            }
        } else {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /clearlagg <item | precise | count | clearall>");
        }
    }
}