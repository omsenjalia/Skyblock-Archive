<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\StringToEffectParser;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Data;
use SkyBlock\Main;
use SkyBlock\util\Values;

class Effect extends BaseCommand {

    const MAX_DURATION = 900;
    const MAX_LEVEL = 4;
    const MAX_EFFECTS = 4;

    /**
     * Effect constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'effect', 'Add Effect');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!$sender->hasPermission("core.effect") && !$sender->hasPermission('effect.me')) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy /effect on " . TextFormat::AQUA . "shop.fallentech.io");
            return;
        }
        if ($sender->getPosition()->getWorld()->getDisplayName() === Values::PVP_WORLD) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot use this command here!");
            return;
        }
        if (!isset($args[0]) || !isset($args[1]) || !isset($args[2])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /effect <effect id> <seconds> <level>");
            return;
        }
        $effect = StringToEffectParser::getInstance()->parse($args[0]);
        if ($effect === null) {
            $effect = EffectIdMap::getInstance()->fromId((int) $args[0]);
        }
        if ($effect === null) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is not a valid effect id. Use /effectids to view available effect ids!");
            return;
        }
        $invalidEffects = [VanillaEffects::INSTANT_DAMAGE(), VanillaEffects::JUMP_BOOST(), VanillaEffects::INSTANT_HEALTH(), VanillaEffects::RESISTANCE()];
        if (in_array($effect, $invalidEffects) || EffectIdMap::getInstance()->toId($effect) >= \pocketmine\data\bedrock\EffectIds::ABSORPTION) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That is not a valid effect id. Use /effectids to view available effect ids!");
            return;
        }
        if (!is_int((int) $args[1]) || $args[1] < 1 || $args[1] > self::MAX_DURATION) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Your duration must be between 1 and " . self::MAX_DURATION);
            return;
        }
        $duration = (int) $args[1];
        if (!is_int((int) $args[2]) || $args[2] < 1 || $args[2] > self::MAX_LEVEL) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Your level must be between 1 and " . self::MAX_LEVEL);
            return;
        }
        $level = (int) $args[2];
        $effects = count($sender->getEffects()->all());
        if ($effects > self::MAX_EFFECTS) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot give yourself more than " . self::MAX_EFFECTS . " effects!");
            return;
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
        $money = Data::$commandEffectPerLevel * $level;
        if (!$user->hasMoney($money)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You do not have enough money to add a level " . $level . " effect. You will need " . $money . " to add this effect!");
            return;
        }
        $user->removeMoney($money);
        $effect = new EffectInstance($effect, $duration * 20, $level - 1, false);
        $sender->getEffects()->add($effect);
        $this->sendMessage($sender, TextFormat::GREEN . "Successfully bought " . $effect->getType()->getName() . " " . $level . " effect for " . $duration . " seconds!");
    }
}