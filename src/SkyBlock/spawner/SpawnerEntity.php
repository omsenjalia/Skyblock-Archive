<?php

namespace SkyBlock\spawner;

interface SpawnerEntity {

    public function setStack(int $amount) : void;

    public function getStackAmount() : int;

    public function getMobcoins() : int;

}