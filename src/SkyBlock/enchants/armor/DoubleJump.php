<?php


namespace SkyBlock\enchants\armor;

use pocketmine\color\Color;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\world\particle\DustParticle;
use SkyBlock\util\Values;

class DoubleJump extends BaseArmorEnchant {


    private $jumpDelay;

    static int $id = 193;


    public function onJumpPressed(Player $user) {
        if (in_array($user->getWorld()->getDisplayName(), Values::PVP_WORLDS, true)) {
            return;
        }
        if ($user->isOnGround()) {
            unset($this->jumpDelay[$user->getXuid()]);
        }
        if (!isset($this->jumpDelay[$user->getXuid()])) {
            $this->jumpDelay[$user->getXuid()] = $user->getServer()->getTick();
        } else {
            if ($this->jumpDelay[$user->getXuid()] < $user->getServer()->getTick() - 5) {
                $angle = atan2($user->getDirectionVector()->getX(), $user->getDirectionVector()->getZ());
                $z = cos($angle) * 0.5;
                $x = sin($angle) * 0.5;
                $user->setMotion(new Vector3($x, 0.5, $z));
                $pk = new PlaySoundPacket();
                $pk->soundName = "mob.enderdragon.flap";
                $pk->x = (int) $user->getPosition()->x;
                $pk->y = (int) $user->getPosition()->y;
                $pk->z = (int) $user->getPosition()->z;
                $pk->volume = 0.5;
                $pk->pitch = 1;
                $user->getNetworkSession()->sendDataPacket($pk);
                $count = 10;
                for ($iterator = 0; $iterator < $count; $iterator++) {
                    $user->getWorld()->addParticle($user->getPosition()->add(mt_rand(-10, 10) / 10, -0.5, mt_rand(-10, 10) / 10), new DustParticle(new Color(50, 50, 50)));
                }
                $this->jumpDelay[$user->getXuid()] = $user->getServer()->getTick() + 1000;
            }
        }
    }

    public function onLand(Player $user) {
        $user->sendMessage("§aLanded!");
    }

    /**
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void { }
}