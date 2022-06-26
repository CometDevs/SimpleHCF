<?php

namespace AsuraNetwork\factions\utils;

final class FactionData{

    public function __construct(
      private array $data
    ){}

    /**
     * @return array
     */
    public function getData(): array{
        return $this->data;
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function getSimple(string $key, mixed $default = null): mixed{
        if (!isset($this->data[$key])){
            $this->data[$key] = $default;
            return $default;
        }
        return $this->data[$key];
    }

    /**
     * @return string
     */
    public function serialize(): string{
        return yaml_emit($this->data);
    }

}