<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$TRANSLATIONS = require __DIR__ . '/translations.php';

if (empty($_SESSION['lang']) || !isset($TRANSLATIONS[$_SESSION['lang']])) {
    $_SESSION['lang'] = 'en';
}

$currentLang = $_SESSION['lang'];

function current_lang(): string
{
    return $_SESSION['lang'] ?? 'en';
}

function t(string $key): string
{
    global $TRANSLATIONS, $currentLang;

    if (isset($TRANSLATIONS[$currentLang][$key])) {
        return $TRANSLATIONS[$currentLang][$key];
    }

    if (isset($TRANSLATIONS['en'][$key])) {
        return $TRANSLATIONS['en'][$key];
    }

    return $key;
}