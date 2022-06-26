<?php

namespace AsuraNetwork\utils;

final class ConfigurationUtils{

    public static function load(string $file): mixed{
        if (!file_exists($file)) return false;
        return yaml_parse_file($file);
    }

}