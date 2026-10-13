<?php

namespace App\Support;

/**
 * Utility for handling Nepali Unicode, Preeti-to-Unicode conversion,
 * and text normalization.
 */
class NepaliUnicode
{
    /**
     * Map of legacy Preeti key characters to modern Unicode Devanagari equivalents.
     */
    protected static array $preetiToUnicodeMap = [
        '~' => 'ग्', '!' => '१', '@' => '२', '#' => '३', '$' => '४', '%' => '५',
        '^' => '६', '&' => '७', '*' => '८', '(' => '९', ')' => '०',
        '-' => '(', '_' => ')', '+' => 'ं', '=' => '.',
        'q' => 'त्र', 'w' => 'ध', 'e' => 'भ', 'r' => 'च', 't' => 'त', 'y' => 'थ',
        'u' => 'ग', 'i' => 'ष', 'o' => 'य', 'p' => 'उ', '[' => 'ृ', '{' => 'र्‍',
        ']' => 'े', '}' => 'ै', '\\' => '्', '|' => '्र',
        'a' => 'ब', 's' => 'क', 'd' => 'म', 'f' => 'ा', 'g' => 'न', 'h' => 'ज',
        'j' => 'व', 'k' => 'प', 'l' => 'ि', ';' => 'स', ':' => 'श', '\'' => 'ु', '"' => 'ू',
        'z' => 'श', 'x' => 'ह', 'c' => 'अ', 'v' => 'ख', 'b' => 'द', 'n' => 'ल',
        'm' => 'फ', ',' => ',', '.' => '।', '/' => 'र', '<' => '?', '>' => 'श्र', '?' => 'रु',
        'Q' => 'त्त', 'W' => 'द्ध', 'E' => 'भ्', 'R' => 'च्', 'T' => 'त्', 'Y' => 'थ्',
        'U' => 'ग्', 'I' => 'क्ष', 'O' => 'इ', 'P' => 'ए',
        'A' => 'ब्', 'S' => 'क्', 'D' => 'म्', 'F' => 'ँ', 'G' => 'न्', 'H' => 'ज्',
        'J' => 'व्', 'K' => 'प्', 'L' => 'ी',
        'Z' => 'झ्', 'X' => 'ह्', 'C' => 'ऋ', 'V' => 'ख्', 'B' => 'द्य', 'N' => 'ल्',
        'M' => 'फ्',
    ];

    /**
     * Convert legacy Preeti text to standard Unicode UTF-8.
     */
    public static function preetiToUnicode(string $text): string
    {
        if (trim($text) === '') {
            return $text;
        }

        // Reorder pre-characters in Preeti like 'l' (Hraswo Ikara) which precedes consonants in typewriter layout
        // and '{' (Reph) which precedes characters in legacy layout
        $length = mb_strlen($text, 'UTF-8');
        $output = '';

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');

            // Handle 'l' (ि) in Preeti: placed before consonant in Preeti, but after consonant in Unicode
            if ($char === 'l' && $i + 1 < $length) {
                $nextChar = mb_substr($text, $i + 1, 1, 'UTF-8');
                $mappedNext = self::$preetiToUnicodeMap[$nextChar] ?? $nextChar;
                $output .= $mappedNext . 'ि';
                $i++;
                continue;
            }

            // Handle '{' (reph) in Preeti: placed after consonant in Preeti, but before consonant in Unicode
            if ($char === '{' && $output !== '') {
                // Insert reph (र्) before the previous consonant sequence
                $lastChar = mb_substr($output, -1, 1, 'UTF-8');
                $output = mb_substr($output, 0, -1, 'UTF-8') . 'र्' . $lastChar;
                continue;
            }

            $output .= self::$preetiToUnicodeMap[$char] ?? $char;
        }

        return self::normalize($output);
    }

    /**
     * Normalize Unicode string using Unicode Normalization Form C (Canonical Decomposition, followed by Canonical Composition).
     */
    public static function normalize(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        if (class_exists(\Normalizer::class)) {
            $normalized = \Normalizer::normalize($text, \Normalizer::FORM_C);
            if ($normalized !== false) {
                return $normalized;
            }
        }

        return $text;
    }

    /**
     * Detect if a string appears to be legacy Preeti instead of proper Unicode.
     */
    public static function isLikelyPreeti(string $text): bool
    {
        // Check for absence of Devanagari Unicode range U+0900..U+097F
        // and presence of common Preeti patterns like 's]', 'k|', 'f]', 'g]', ';k'
        $hasDevanagari = preg_match('/[\x{0900}-\x{097F}]/u', $text);
        if ($hasDevanagari) {
            return false;
        }

        return (bool) preg_match('/[sSwWqQrRtTyYuUiIoOpP\[\]\\aAsSdDfFgGhHjJkKlL;\'zZxXcCvVbBnNmM,\.\/]{4,}/', $text);
    }
}
