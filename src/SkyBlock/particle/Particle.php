<?php

namespace SkyBlock\particle;

use SkyBlock\Main;

class Particle {
    /** @var Main $pl */
    private Main $pl;
    /** @var FloatingTextParticle|null $particle */
    private ?FloatingTextParticle $particle;

    private string $type;

    private string $update;

    private string $text;

    private string $world;

    public function __construct(Main $plugin, FloatingTextParticle $particle, string $text, string $type, string $world, string $update) {
        $this->pl = $plugin;
        $this->particle = $particle;
        $this->text = $text;
        $this->type = $type;
        $this->world = $world;
        $this->update = $update;

        $this->setText($text);
    }

    public function getPlugin() : Main {
        return $this->pl;
    }

    public function getParticle() : ?FloatingTextParticle {
        return $this->particle;
    }

    public function getWorld() : string {
        return $this->world;
    }

    public function getText() : string {
        return $this->text;
    }

    public function setText(string $text = '') {
        $this->particle->setText($text);
    }

    public function getType() : string {
        return $this->type;
    }

    public function getUpdate() : string {
        return $this->update;
    }

    public function setInvisible(bool $state = true) {
        $this->particle->setInvisible($state);
    }

}