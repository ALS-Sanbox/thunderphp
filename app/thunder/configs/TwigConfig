<?php

namespace App\Configs;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigConfig
{
    public static function initialize(): Environment
    {
        // Set the path to your views directory
        $loader = new FilesystemLoader(__DIR__ . '/../views');

        // Initialize Twig Environment
        $twig = new Environment($loader, [
            'cache' => __DIR__ . '/../cache', // Enable caching in production
            'debug' => true,                  // Disable in production
        ]);

        return $twig;
    }
}
