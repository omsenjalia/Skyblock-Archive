<?php

declare(strict_types=1);

namespace SkyBlock\util\data_object;

use JsonSerializable;
use function is_array;
use function json_decode;

class JsonObject implements JsonSerializable {

    /**
     * JsonObject constructor.
     *
     * @param string $json
     */
    public function __construct(string $json = "") {
        $this->jsonDeserialize(json_decode($json, true));
    }

    /**
     * @param mixed $data
     */
    protected function jsonDeserialize(mixed $data) : void {
        if (!is_array($data)) return;

        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array {
        return (array) $this;
    }

}