<?php

namespace App\Http\Factories;

use App\Interfaces\ChatBotStrategy;
use Exception;

class MessageFactory
{
    final public function makeMessage(string $className) : ChatBotStrategy
    {
        $classe = $this->getClasse($className);
        self::validaClasse($classe);

        return (new $classe);
    }

    private function getClasse(string $className): string
    {
        return "\App\Http\Strategies\\{$className}Strategy";
    }

    private static function validaClasse(string $pathClasse): void
    {
        if (!class_exists($pathClasse)) {
            throw new Exception("Classe {$pathClasse} não encontrada.", 500);
        }
    }
}
