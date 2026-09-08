<?php

declare(strict_types=1);

namespace App\Services;


class LanguageService
{
    /**
     * Map of language locales to their full names with native names
     * A comprehensive list of languages with their native names
     */
    protected array $languageNames = [

        'en' => 'English',
        'id' => 'Indonesian (Bahasa Indonesia)',
    ];

    /**
     * Get all active languages
     */
    public function getLanguages(): array
    {
        return $this->languageNames;
    }

    /**
     * Get all active languages with detailed information
     */
    public function getActiveLanguages(): array
    {
        // Get all the languages inside /resources/lang folder as key.
        // The key is the language code (e.g., 'en', 'bn').
        $languages = array_diff(scandir(resource_path('lang')), ['..', '.', '.DS_Store']);

        // Process languages to get unique keys
        $uniqueLanguages = [];
        foreach ($languages as $language) {
            // Remove .json extension if present
            $langKey = preg_replace('/\.json$/', '', $language);
            // Add to unique languages if not already added
            $uniqueLanguages[$langKey] = [
                'name' => $this->getLanguageNameByLocale($langKey),
            ];
        }

        $languages = ld_apply_filters('languages', $uniqueLanguages);

        foreach ($languages as $code => &$language) {
            $language['code'] = strtoupper($code);
            $language['icon'] = "/images/flags/language-{$code}.svg";
        }

        return $languages;
    }

    /**
     * Get all available language names
     */
    public function getLanguageNames(): array
    {
        return $this->languageNames;
    }

    /**
     * Get language name by locale code
     */
    public function getLanguageNameByLocale(string $locale): string
    {
        // Normalize the locale (remove any region part for matching)
        $normalizedLocale = strtolower(explode('-', $locale)[0]);

        // Return the full name if available, otherwise return capitalized locale
        return $this->languageNames[$normalizedLocale] ?? ucfirst($locale);
    }
}
