<?php

declare(strict_types=1);

namespace App\Helpers;

final class PersianTextHelper
{
    /**
     * Normalize Persian/Arabic text for consistent search matching.
     */
    public static function normalize(string $text): string
    {
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        $text = self::toEnglishDigits($text);
        $text = self::unifyPersianCharacters($text);
        $text = self::removeDiacritics($text);
        $text = preg_replace('/[\x{200C}\x{200D}\x{FEFF}]/u', '', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return mb_strtolower(trim($text), 'UTF-8');
    }

    public static function toEnglishDigits(string $text): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace(array_merge($persian, $arabic), array_merge($english, $english), $text);
    }

    public static function unifyPersianCharacters(string $text): string
    {
        $replacements = [
            'ي' => 'ی',
            'ك' => 'ک',
            'ة' => 'ه',
            'ۀ' => 'ه',
            'ؤ' => 'و',
            'إ' => 'ا',
            'أ' => 'ا',
            'آ' => 'ا',
            '‌' => ' ',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    public static function removeDiacritics(string $text): string
    {
        return preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $text) ?? $text;
    }

    /**
     * @return list<string>
     */
    public static function tokenize(string $text): array
    {
        $normalized = self::normalize($text);

        if ($normalized === '') {
            return [];
        }

        $tokens = preg_split('/\s+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY);

        return is_array($tokens) ? array_values($tokens) : [];
    }
}
