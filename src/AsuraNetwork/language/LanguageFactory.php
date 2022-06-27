<?php

namespace AsuraNetwork\language;

use AsuraNetwork\Loader;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

final class LanguageFactory{
    use SingletonTrait;

    protected const DEFAULT_LANGUAGE = "eng";

    protected string $language;
    protected array $translations;

    public function init(?string $language): void{
        $languagesFolder = Loader::getInstance()->getDataFolder() . "languages/";
        @mkdir($languagesFolder);
        Loader::getInstance()->saveResource("languages/" . $language . ".ini");

        if (!$language || !file_exists($languagesFolder . $language.".ini")) {
            $language = self::DEFAULT_LANGUAGE;
        }

        $this->language = $language;
        $this->translations = array_map("stripcslashes", parse_ini_string(file_get_contents($languagesFolder . $language . ".ini"), false, INI_SCANNER_RAW));
    }

    public function getTranslations(): array{
        return $this->translations;
    }

    public function getTranslation(string $translation, array $params = []): ?string{
        return $this->hasTranslation($translation) ?
            TextFormat::colorize($this->translate($translation, $params), "") :
            TextFormat::RED . "No translation of the text $translation was found in the $this->language language.";
    }

    public function hasTranslation(string $translation): bool{
        return isset($this->translations[$translation]);
    }

    protected function translate(string $translation, array $params = []): string{
        $message = $this->translations[$translation];
        foreach ($params as $index => $param) {
            $message = str_replace("{%$index}", $param, $message);
        }
        return $message;
    }

    public function getLanguage(): string{
        return $this->language;
    }

}