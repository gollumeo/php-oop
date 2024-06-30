<?php

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

$whoops = new Run;
$whoops->pushHandler(new PrettyPageHandler);
$whoops->register();

/**
 * Génère une directive @vite() pour inclure les fichiers JavaScript et CSS.
 *
 * @param array $files Tableau des chemins des fichiers à inclure.
 * @return string Directive @vite() générée.
 */
function vite(array $files): string
{
    $output = '';

    foreach ($files as $file) {
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        switch ($extension) {
            case 'js':
                $output .= "<script type=\"module\" src=\"$file\"></script>\n";
                break;
            case 'css':
                $output .= "<link rel=\"stylesheet\" href=\"$file\">\n";
                break;
        }
    }

    return $output;
}