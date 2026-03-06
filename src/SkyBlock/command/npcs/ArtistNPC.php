<?php

namespace SkyBlock\command\npcs;

use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\ShopUtil;

class ArtistNPC extends BaseCommand {

    public const PREFIX = "§l§e[§aBob The Artist§e]§r ";
    public static array $interacting = [];

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'artistnpc');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        $player = $sender;
        if (!$sender instanceof Player) {
            $this->sendUsage($sender, $commandLabel);
            return;
        }
        assert($player instanceof Player);
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
        if ($user === null) {
            return;
        }

        if (isset(self::$interacting[$player->getName()])) {
            return;
        }

        $player->sendMessage(self::PREFIX . "Let there be light!!!");
        $this->sendEquipmentMenu($player);
    }

    public function sendArtistMenu(Player $player) {
        $buttons = ["Back", "Wool", "Concrete", "Concrete Powder", "Terracotta", "Glass", "Glass Panes"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        $this->sendWoolBlocksMenu($player);
                        break;
                    case 2:
                        $this->sendConcreteMenu($player);
                        break;
                    case 3:
                        $this->sendConcretePowderMenu($player);
                        break;
                    case 4:
                        $this->sendTerracottaMenu($player);
                        break;
                    case 5:
                        $this->sendStainedGlassMenu($player);
                        break;
                    case 6:
                        $this->sendStainedGlassPaneMenu($player);
                        break;
                    default:
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a block menu -", $buttons, $func);
    }

    public function sendWoolBlocksMenu(Player $player) : void {
        $buttons = ["Back", "White Wool", "Orange Wool", "Magenta Wool", "Light Blue Wool", "Yellow Wool", "Lime Wool", "Pink Wool", "Gray Wool", "Light Gray Wool", "Cyan Wool", "Purple Wool", "Blue Wool", "Brown Wool", "Green Wool", "Red Wool", "Black Wool"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::WHITE())->asItem());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::ORANGE())->asItem());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::MAGENTA())->asItem());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::LIGHT_BLUE())->asItem());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::YELLOW())->asItem());
                        break;
                    case 6:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::LIME())->asItem());
                        break;
                    case 7:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::PINK())->asItem());
                        break;
                    case 8:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::GRAY())->asItem());
                        break;
                    case 9:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::LIGHT_GRAY())->asItem());
                        break;
                    case 10:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::CYAN())->asItem());
                        break;
                    case 11:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::PURPLE())->asItem());
                        break;
                    case 12:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::BLUE())->asItem());
                        break;
                    case 13:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::BROWN())->asItem());
                        break;
                    case 14:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::GREEN())->asItem());
                        break;
                    case 15:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::RED())->asItem());
                        break;
                    case 16:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::WOOL()->setColor(DyeColor::BLACK())->asItem());
                        break;
                    default:
                        $this->sendArtistMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Wool type -", $buttons, $func);
    }

    public function sendTerracottaMenu(Player $player) : void {
        $buttons = ["Back", "White Stained Clay", "Orange Stained Clay", "Magenta Stained Clay", "Light Blue Stained Clay", "Yellow Stained Clay", "Lime Stained Clay", "Pink Stained Clay", "Gray Stained Clay", "Light Gray Stained Clay", "Cyan Stained Clay", "Purple Stained Clay", "Blue Stained Clay", "Brown Stained Clay", "Green Stained Clay", "Red Stained Clay", "Black Stained Clay"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::WHITE())->asItem());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::ORANGE())->asItem());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::MAGENTA())->asItem());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::LIGHT_BLUE())->asItem());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::YELLOW())->asItem());
                        break;
                    case 6:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::LIME())->asItem());
                        break;
                    case 7:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::PINK())->asItem());
                        break;
                    case 8:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::GRAY())->asItem());
                        break;
                    case 9:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::LIGHT_GRAY())->asItem());
                        break;
                    case 10:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::CYAN())->asItem());
                        break;
                    case 11:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::PURPLE())->asItem());
                        break;
                    case 12:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::BLUE())->asItem());
                        break;
                    case 13:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::BROWN())->asItem());
                        break;
                    case 14:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::GREEN())->asItem());
                        break;
                    case 15:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::RED())->asItem());
                        break;
                    case 16:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::BLACK())->asItem());
                        break;
                    default:
                        $this->sendArtistMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Terracotta(Not glazed) type -", $buttons, $func);
    }

    public function sendGlazedTerracottaMenu(Player $player) : void {
        $buttons = ["Back", "Purple Glazed Terracotta", "White Glazed Terracotta", "Orange Glazed Terracotta", "Magenta Glazed Terracotta", "Light Blue Glazed Terracotta", "Yellow Glazed Terracotta", "Lime Glazed Terracotta", "Pink Glazed Terracotta", "Gray Glazed Terracotta", "Silver Glazed Terracotta", "Cyan Glazed Terracotta", "Blue Glazed Terracotta", "Brown Glazed Terracotta", "Green Glazed Terracotta", "Red Glazed Terracotta", "Black Glazed Terracotta"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::PURPLE)->asItem());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::WHITE)->asItem());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::ORANGE)->asItem());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::MAGENTA)->asItem());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::LIGHT_BLUE)->asItem());
                        break;
                    case 6:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::YELLOW)->asItem());
                        break;
                    case 7:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::LIME)->asItem());
                        break;
                    case 8:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::PINK)->asItem());
                        break;
                    case 9:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::GRAY)->asItem());
                        break;
                    case 10:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::LIGHT_GRAY)->asItem());
                        break;
                    case 11:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::CYAN)->asItem());
                        break;
                    case 12:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::BLUE)->asItem());
                        break;
                    case 13:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::BROWN)->asItem());
                        break;
                    case 14:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::GREEN)->asItem());
                        break;
                    case 15:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::RED)->asItem());
                        break;
                    case 16:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::BLACK)->asItem());
                        break;
                    default:
                        $this->sendArtistMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Glazed Terracotta type -", $buttons, $func);
    }

    public function sendConcreteMenu(Player $player) : void {
        $buttons = ["Back", "White Concrete", "Orange Concrete", "Magenta Concrete", "Light Blue Concrete", "Yellow Concrete", "Lime Concrete", "Pink Concrete", "Gray Concrete", "Light Gray Concrete", "Cyan Concrete", "Purple Concrete", "Blue Concrete", "Brown Concrete", "Green Concrete", "Red Concrete", "Black Concrete"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::WHITE)->asItem());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::ORANGE)->asItem());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::MAGENTA)->asItem());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::LIGHT_BLUE)->asItem());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::YELLOW)->asItem());
                        break;
                    case 6:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::LIME)->asItem());
                        break;
                    case 7:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::PINK)->asItem());
                        break;
                    case 8:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::GRAY)->asItem());
                        break;
                    case 9:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::LIGHT_GRAY)->asItem());
                        break;
                    case 10:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::CYAN)->asItem());
                        break;
                    case 11:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::PURPLE)->asItem());
                        break;
                    case 12:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::BLUE)->asItem());
                        break;
                    case 13:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::BROWN)->asItem());
                        break;
                    case 14:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::GREEN)->asItem());
                        break;
                    case 15:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::RED)->asItem());
                        break;
                    case 16:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE()->setColor(DyeColor::BLACK)->asItem());
                        break;
                    default:
                        $this->sendArtistMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Concrete type -", $buttons, $func);
    }

    public function sendStainedGlassMenu(Player $player) : void {
        $buttons = ["Back", "White Stained Glass", "Orange Stained Glass", "Magenta Stained Glass", "Light Blue Stained Glass", "Yellow Stained Glass", "Lime Stained Glass", "Pink Stained Glass", "Gray Stained Glass", "Light Gray Stained Glass", "Cyan Stained Glass", "Purple Stained Glass", "Blue Stained Glass", "Brown Stained Glass", "Green Stained Glass", "Red Stained Glass", "Black Stained Glass", "Glass Block"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE)->asItem());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::ORANGE)->asItem());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::MAGENTA)->asItem());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::LIGHT_BLUE)->asItem());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::YELLOW)->asItem());
                        break;
                    case 6:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::LIME)->asItem());
                        break;
                    case 7:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::PINK)->asItem());
                        break;
                    case 8:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GRAY)->asItem());
                        break;
                    case 9:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::LIGHT_GRAY)->asItem());
                        break;
                    case 10:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::CYAN)->asItem());
                        break;
                    case 11:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::PURPLE)->asItem());
                        break;
                    case 12:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLUE)->asItem());
                        break;
                    case 13:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BROWN)->asItem());
                        break;
                    case 14:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GREEN)->asItem());
                        break;
                    case 15:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED)->asItem());
                        break;
                    case 16:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK)->asItem());
                        break;
                    case 17:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLASS_PANE()->asItem());
                        break;
                    default:
                        $this->sendArtistMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Stained Glass type -", $buttons, $func);
    }

    public function sendStainedGlassPaneMenu(Player $player) : void {
        $buttons = ["Back", "White Stained Glass Pane", "Orange Stained Glass Pane", "Magenta Stained Glass Pane", "Light Blue Stained Glass Pane", "Yellow Stained Glass Pane", "Lime Stained Glass Pane", "Pink Stained Glass Pane", "Gray Stained Glass Pane", "Light Gray Stained Glass Pane", "Cyan Stained Glass Pane", "Purple Stained Glass Pane", "Blue Stained Glass Pane", "Brown Stained Glass Pane", "Green Stained Glass Pane", "Red Stained Glass Pane", "Black Stained Glass Pane", "Glass Pane"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::WHITE)->asItem());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::ORANGE)->asItem());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::MAGENTA)->asItem());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::LIGHT_BLUE)->asItem());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::YELLOW)->asItem());
                        break;
                    case 6:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::LIME)->asItem());
                        break;
                    case 7:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::PINK)->asItem());
                        break;
                    case 8:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY)->asItem());
                        break;
                    case 9:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::LIGHT_GRAY)->asItem());
                        break;
                    case 10:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::CYAN)->asItem());
                        break;
                    case 11:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::PURPLE)->asItem());
                        break;
                    case 12:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BLUE)->asItem());
                        break;
                    case 13:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BROWN)->asItem());
                        break;
                    case 14:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GREEN)->asItem());
                        break;
                    case 15:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::RED)->asItem());
                        break;
                    case 16:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BLACK)->asItem());
                        break;
                    case 17:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::GLASS_PANE()->asItem());
                        break;
                    default:
                        $this->sendArtistMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Stained Glass Pane type -", $buttons, $func);
    }

    public function sendConcretePowderMenu(Player $player) : void {
        $buttons = ["Back", "White Concrete Powder", "Orange Concrete Powder", "Magenta Concrete Powder", "Light Blue Concrete Powder", "Yellow Concrete Powder", "Lime Concrete Powder", "Pink Concrete Powder", "Gray Concrete Powder", "Light Gray Concrete Powder", "Cyan Concrete Powder", "Purple Concrete Powder", "Blue Concrete Powder", "Brown Concrete Powder", "Green Concrete Powder", "Red Concrete Powder", "Black Concrete Powder"];
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 1:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::WHITE)->asItem());
                        break;
                    case 2:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::ORANGE)->asItem());
                        break;
                    case 3:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::MAGENTA)->asItem());
                        break;
                    case 4:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::LIGHT_BLUE)->asItem());
                        break;
                    case 5:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::YELLOW)->asItem());
                        break;
                    case 6:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::LIME)->asItem());
                        break;
                    case 7:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::PINK)->asItem());
                        break;
                    case 8:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::GRAY)->asItem());
                        break;
                    case 9:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::LIGHT_GRAY)->asItem());
                        break;
                    case 10:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::CYAN)->asItem());
                        break;
                    case 11:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::PURPLE)->asItem());
                        break;
                    case 12:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::BLUE)->asItem());
                        break;
                    case 13:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::BROWN)->asItem());
                        break;
                    case 14:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::GREEN)->asItem());
                        break;
                    case 15:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::RED)->asItem());
                        break;
                    case 16:
                        ShopUtil::getInstance()->sendAmountWindowForItem($player, VanillaBlocks::CONCRETE_POWDER()->setColor(DyeColor::BLACK)->asItem());
                        break;
                    default:
                        $this->sendArtistMenu($player);
                        break;
                }
            }
        };
        $title = "§b§lShop";
        if (Main::getInstance()->getUserManager()->getOnlineUser($player->getName())->getPref()->button_size) {
            $title .= "§l§a§r";
        }
        Main::getInstance()->getFormFunctions()->sendSimpleForm($player, $title, "§6Select a Concrete Powder type -", $buttons, $func);
    }
}