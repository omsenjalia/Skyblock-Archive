<?php


namespace SkyBlock\enchants\armor;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class Deflate extends BaseArmorEnchant {

    static int $id = 156;

    /**
     * @param Player $p
     * @param        $shrinklevel
     */
    public function onSneak(Player $p, $shrinklevel) : void {
        $name = strtolower($p->getName());
        if (isset($this->pl->shrunk[$name]) && $this->pl->shrunk[$name] > time()) {
            $this->pl->shrinkremaining[$name] = $this->pl->shrunk[$name] - time();
            unset($this->pl->shrinkcd[$name]);
            unset($this->pl->shrunk[$name]);
            $p->setScale(1);
            $this->sendActivation($p, TextFormat::RED . "You are now normal.");
        } else {
            if (!isset($this->pl->shrinkcd[$name]) || $this->pl->shrinkcd[$name] <= time()) {
                $scale = $p->getScale() - 0.30 - (($shrinklevel / 4) * 0.05);
                if ($scale > 0) $p->setScale($scale);
                $this->pl->shrunk[$name] = isset($this->pl->shrinkremaining[$name]) ? time() + $this->pl->shrinkremaining[$name] : time() + 60;
                $this->pl->shrinkcd[$name] = isset($this->pl->shrinkremaining[$name]) ? time() + (75 - (60 - $this->pl->shrinkremaining[$name])) : time() + 75;
                $this->sendActivation($p, TextFormat::GREEN . "You have shrunk. Sneak again to be normal.");
                if (isset($this->pl->shrinkremaining[$name])) unset($this->pl->shrinkremaining[$name]);
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