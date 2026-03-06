<?php


namespace SkyBlock\command;


use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class BreakMe extends BaseCommand {
    /**
     * BreakMe constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'break', 'Breaks the block you are looking at', "", true, [], "core.break.me");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!Main::getInstance()->hasOp($sender)) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        $block = $sender->getTargetBlock(100, [BlockTypeIds::AIR]);
        if ($block === null) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That block is too far!");
            return;
        }
        $tile = $sender->getWorld()->getTile($block->getPosition());
        $tile?->close();
        $sender->getWorld()->setBlock($block->getPosition(), VanillaBlocks::AIR());
    }


}