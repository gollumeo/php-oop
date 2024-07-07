<?php

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

$whoops = new Run;
$whoops->pushHandler(new PrettyPageHandler);
$whoops->register();

/**
 * Generate the @vite() directive.
 *
 * @param array $files Array of files to include.
 * @return string The generated Vite directive.
 */
function vite(array $files): string
{
    $isDev = $_ENV['APP_ENV'] === 'development';
    $devServerUrl = 'http://ratatata.test:5173';

    $output = '';

    if ($isDev) {
        $output = "<script type=\"module\" src=\"{$devServerUrl}/@vite/client\"></script>\n";
        foreach ($files as $file) {
            $output .= "<script type=\"module\" src=\"{$devServerUrl}/{$file}\"></script>\n";
        }
    } else {
        $manifest = json_decode(file_get_contents(__DIR__ . '/public/build/manifest.json'), true);
        foreach ($files as $file) {
            $output .= "<script type=\"module\" src=\"/build/{$manifest[$file]['file']}\"></script>\n";
        }
    }

    return $output;
}