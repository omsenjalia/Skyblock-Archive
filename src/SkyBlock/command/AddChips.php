<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class AddChips extends BaseCommand {
    /**
     * AddChips constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'addchips', 'Give chips to a player', '[player] <chips>', true, [], 'core.add.chips');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!Main::getInstance()->hasOp($sender) || ($sender instanceof Player && !Main::getInstance()->isTrusted($sender->getName()))) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (!isset($args[0]) || !isset($args[1])) {
            $this->sendUsage($sender, $commandLabel);
            return;
        }

        $player = $args[0];
        $chips = $args[1];
        if (!is_int((int) $chips)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Chip amount must be a number!");
            return;
        }
        $chips = (int) $chips;
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player);
        if ($user === null) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Player is not online!");
            return;
        }
        $user->addChips($chips);
        $this->sendMessage($user->getPlayer(), TextFormat::YELLOW . "You have claimed $chips chips! Use them at /casino. Check how many chips you have with /mychips!");
    }
}