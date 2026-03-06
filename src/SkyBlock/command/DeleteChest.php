<?php


namespace SkyBlock\command;


use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\perms\Permission;

class DeleteChest extends BaseCommand {
    /**
     * Breakit constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'deletechest', "Delete the chest youre looking at", "", true, [], "core.delchest");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        $island = Main::getInstance()->getIslandManager()->getOnlineIslandByWorld($sender->getPosition()->getWorld()->getDisplayName());
        if ($island === null) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can only delete chests on a private island!");
            return;
        }
        if (!Main::getInstance()->hasOp($sender) && !$island->hasPerm($sender->getName(), Permission::DELETE_CHEST)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You do not have `delete chest` permissions on this island!");
            return;
        }
        $block = $sender->getTargetBlock(25);
        if ($block->getTypeId() !== BlockTypeIds::CHEST) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You are not looking at a chest. Look at the chest you would like to delete!");
            return;
        }
        if (Main::getInstance()->isPrivateChest($block, $island->getOwner())) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't delete a private chest!");
            return;
        }
        $tile = $sender->getWorld()->getTile($block->getPosition());
        $tile?->close(); // todo add contents to inv
        $sender->getWorld()->setBlock($block->getPosition(), VanillaBlocks::AIR());
    }
}