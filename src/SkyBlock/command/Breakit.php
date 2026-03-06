<?php


namespace SkyBlock\command;


use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\Data;
use SkyBlock\Main;
use SkyBlock\util\Values;

/**
 * @deprecated
 * */
class Breakit extends BaseCommand {
    /**
     * Breakit constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'breakit');
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendUsage($sender, $commandLabel);
            return;
        }
        $island = Main::getInstance()->getIslandManager()->getOnlineIslandByWorld($sender->getWorld()->getDisplayName());
        if ($island === null) {
            $this->sendMessage($sender, "§4[Error] §cYou can only break Bedrock on an island!");
            return;
        }
        if (!Main::getInstance()->hasOp($sender) && !$island->isAnOwner($sender->getName())) {
            $this->sendMessage($sender, "§4[Error] §cYou must be an Owner or CoOwner of this island to use /breakit!");
            return;
        }
        if ($sender->getPosition()->getWorld()->getDisplayName() === Values::NETHER_WORLD) {
            $this->sendMessage($sender, "§4[Error] §cYou cannot break bedrock here!");
            return;
        }

        $block = $sender->getTargetBlock(25);
        if ($block->getTypeId() !== BlockTypeIds::BEDROCK) {
            $this->sendMessage($sender, "§4[Error] §cYou are not looking at §dBedrock to break! Look at the bedrock you want to break and do /breakit!");
            return;
        }

        $item = VanillaBlocks::BEDROCK()->asItem();
        if (!$sender->getInventory()->canAddItem($item)) {
            $this->sendMessage($sender, "§4[Error] §cYour inventory is full!");
            return;
        }
        $sender->getWorld()->setBlock($block->getPosition(), VanillaBlocks::AIR());
        $island->setPoints(-Data::$bedrockIslandPoints);
        $sender->getInventory()->addItem($item);
    }
}