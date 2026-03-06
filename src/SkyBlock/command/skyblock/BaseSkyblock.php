<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\command\Functions;
use SkyBlock\db\SQLite3;
use SkyBlock\gang\GangManager;
use SkyBlock\island\IslandManager;
use SkyBlock\Main;
use SkyBlock\user\User;
use SkyBlock\user\UserManager;
use SkyBlock\util\Values;

abstract class BaseSkyblock {

    /** @var array */
    public array $list = [], $alias = [];
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
    /** @var string */
    public string $command = "", $info = "";

    /**
     * BaseSkyblock constructor.
     *
     * @param Main   $plugin
     * @param string $command
     * @param string $info
     * @param array  $alias
     */
    public function __construct(Main $plugin, string $command, string $info = "", array $alias = []) {
        $this->pl = $this->plugin = $plugin;
        $this->db = $this->pl->getDb();
        $this->im = $this->pl->getIslandManager();
        $this->um = $this->pl->getUserManager();
        $this->gm = $this->pl->getGangManager();
        $this->func = $this->pl->getFunctions();
        $this->command = $command;
        $this->info = $info;
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getCommand() : string {
        return $this->command;
    }

    /**
     * @return array
     */
    public function getAlias() : array {
        return $this->alias;
    }

    /**
     * @param Player $sender
     * @param string $message
     */
    public function sendMessage(Player $sender, string $message) {
        $sender->sendMessage(Values::FT_PREFIX . $message);
    }

    /**
     * @param Player $sender
     * @param User   $user
     * @param array  $args
     *
     * @return mixed
     */
    abstract public function execute(Player $sender, User $user, array $args);


}