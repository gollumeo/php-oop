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