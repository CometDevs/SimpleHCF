<?php

declare(strict_types=1);

namespace AsuraNetwork\utils;

final class ConfigUtils{

    public static function load(string $file): mixed{
        if (!file_exists($file)) return false;
        return yaml_parse_file($file);
    }

}