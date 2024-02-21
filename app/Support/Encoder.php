<?php

namespace App\Support;

use Sqids\Sqids;

class Encoder
{
    public static function shuffle(string $characters, int $length): string
    {
        $numCharacters = strlen($characters);

        $usedCharacters = [];

        $uniqueString = '';

        while (strlen($uniqueString) < $length) {
            $randomCharacter = $characters[rand(0, $numCharacters - 1)];

            if (! in_array($randomCharacter, $usedCharacters)) {
                $uniqueString .= $randomCharacter;
                $usedCharacters[] = $randomCharacter;
            }
        }

        return $uniqueString;
    }

    public static function encode(string $value): string
    {
        return (new Sqids(minLength: 10))->encode([$value]);
    }

    public static function decode(string $salt, string $encoded): array
    {
        if (static::duplicated($salt)) {
            return [0, 0];
        }

        $decoded = (new Sqids($salt, minLength: 10))->decode($encoded);

        return count($decoded) > 1 ? $decoded : [0, 0];
    }

    private static function duplicated(string $salt): bool
    {
        $encounteredChars = [];

        for ($i = 0; $i < strlen($salt); $i++) {
            $char = $salt[$i];

            if (isset($encounteredChars[$char])) {
                return true;
            }

            $encounteredChars[$char] = true;
        }

        return false;
    }
}
