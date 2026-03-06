<?php


namespace SkyBlock\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;
use SkyBlock\db\SQLite3;
use SkyBlock\gang\GangManager;
use SkyBlock\island\IslandManager;
use SkyBlock\Main;
use SkyBlock\UI\FormFunctions;
use SkyBlock\user\UserManager;
use SkyBlock\util\Values;

abstract class BaseCommand extends Command implements PluginOwned {

    /** @var Main */
    public Main $plugin, $pl;
    /** @var SQLite3 */
    public SQLite3 $db;
    /** @var IslandManager */
    public IslandManager $im;
    /** @var UserManager */
    public UserManager $um;
    /** @var GangManager */
    public GangManager $gm;
    /** @var Functions */
    public Functions $func;
    /** @var FormFunctions */
    public FormFunctions $formfunc;
    public $consoleUsageMessage;

    public const NO_PERMISSION = TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " No permission!";
    public const NO_CONSOLE = TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " This command is only player-runnable!";

    /**
     * BaseCommand constructor.
     *
     * @param Main   $plugin
     * @param string $name
     * @param string $description
     * @param string $usageMessage
     * @param bool   $consoleUsageMessage
     * @param array  $aliases
     * @param string $permission
     */
    public function __construct(Main $plugin, string $name, string $description = "", string $usageMessage = "", $consoleUsageMessage = true, array $aliases = [], string $permission = "") {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->pl = $plugin;
        $this->plugin = $plugin;
        $this->db = $this->pl->getDb();
        $this->im = $this->pl->getIslandManager();
        $this->um = $this->pl->getUserManager();
        $this->gm = $this->pl->getGangManager();
        $this->func = $this->pl->getFunctions();
        $this->formfunc = $this->pl->getFormFunctions();
        $this->consoleUsageMessage = $consoleUsageMessage;
        if ($permission === "") $permission = "core.hide";
        if (PermissionManager::getInstance()->getPermission($permission) === null) PermissionManager::getInstance()->addPermission(new Permission($permission));
        $this->setPermission($permission);
        $this->setDescription($description);
    }

    /**
     * @param CommandSender $target
     * @param string|null   $permission
     *
     * @return bool
     */
    public function testPermission(CommandSender $target, ?string $permission = null) : bool {
        return true; // we test them in each command's execute()
    }

    /**
     * @return string
     */
    public function getUsage() : string {
        return "/" . parent::getName() . " " . parent::getUsage();
    }

    /**
     * @return Plugin
     */
    public function getPlugin() : Plugin {
        return $this->pl;
    }

    /**
     * @param CommandSender $sender
     * @param string        $alias
     * @param bool          $isConsole
     */
    public function sendUsage(CommandSender $sender, string $alias, bool $isConsole = false) : void {
        $message = TextFormat::GOLD . "Usage: " . TextFormat::GRAY . "/$alias ";
        if (!$sender instanceof Player) {
            if (!$isConsole) {
                $message = TextFormat::RED . "[Error] Please run this command in-game";
            } elseif (is_string($this->consoleUsageMessage)) {
                $message .= $this->consoleUsageMessage;
            } else {
                $message .= str_replace("[player]", "<player>", parent::getUsage());
            }
        } else {
            $message .= parent::getUsage();
        }
        $this->sendMessage($sender, $message);
    }

    public function getOwningPlugin() : Plugin {
        return $this->plugin;
    }

    /**
     * @param CommandSender $sender
     * @param string        $message
     */
    public function sendMessage(CommandSender $sender, string $message) {
        $sender->sendMessage(Values::FT_PREFIX . $message);
    }

}