<?php


namespace SkyBlock\enchants\armor;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class Bloom extends BaseArmorEnchant {

    static int $id = 147;

    /**
     * @param Player $p
     * @param        $growlevel
     */
    public function onSneak(Player $p, $growlevel) : void {
        $name = strtolower($p->getName());
        if (isset($this->pl->grew[$name]) && $this->pl->grew[$name] > time()) {
            $this->pl->growremaining[$name] = $this->pl->grew[$name] - time();
            unset($this->pl->growcd[$name]);
            unset($this->pl->grew[$name]);
            $p->setScale(1);
            $this->sendActivation($p, TextFormat::RED . "You are now normal.");
        } else {
            if (!isset($this->pl->growcd[$name]) || $this->pl->growcd[$name] <= time()) {
                $scale = $p->getScale() + 0.30 + (($growlevel / 4) * 0.05);
                if ($scale > 0) $p->setScale($scale);
                $this->pl->grew[$name] = isset($this->pl->growremaining[$name]) ? time() + $this->pl->growremaining[$name] : time() + 60;
                $this->pl->growcd[$name] = isset($this->pl->growremaining[$name]) ? time() + (75 - (60 - $this->pl->growremaining[$name])) : time() + 75;
                $this->sendActivation($p, TextFormat::GREEN . "You have grown. Sneak again to be normal.");
                if (isset($this->pl->growremaining[$name])) unset($this->pl->growremaining[$name]);
            }
        }
    }

    /**
     * @param Player $attacker
     * @param Player $victim
     * @param int    $enchantmentLevel
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel) : void {
    }

}