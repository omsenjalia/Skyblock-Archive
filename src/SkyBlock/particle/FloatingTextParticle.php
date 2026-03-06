<?php

namespace SkyBlock\particle;

use pocketmine\math\Vector3;

class FloatingTextParticle extends \pocketmine\world\particle\FloatingTextParticle {

    public Vector3 $pos;

    public function __construct(Vector3 $pos, string $text, string $title = "") {
        $this->pos = $pos;
        parent::__construct($text, $title);
    }

    /**
     * @return Vector3
     */
    public function getPos() : Vector3 {
        return $this->pos;
    }

}