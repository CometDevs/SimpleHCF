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
        $languagesFolder = Loader::getInstance()->getDataFolder() . "languages" . DIRECTORY_SEPARATOR;
        @mkdir($languagesFolder);
        Loader::getInstance()->saveResource($languagesFolder . $language . ".ini");

        if (!$language || !file_exists($languagesFolder . DIRECTORY_SEPARATOR . $language.".ini")) {
            $language = self::DEFAULT_LANGUAGE;
        }

        $this->language = $language;
        $this->translations = parse_ini_file(
            $languagesFolder . DIRECTORY_SEPARATOR . $language . ".ini"
        );
    }

    public function getTranslations(): array{
        return $this->translations;
    }

    public function getTranslation(string $translation, array $params = []): ?string{
        return $this->hasTranslation($translation) ?
            TextFormat::colorize($this->translate($translation, $params)) :
            "No translation of the text $translation was found in the $this->language language.";
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