<?php
/*
Plugin Name: Document Manager
Author: Richard Crowther
Version: 1.0
*/

// Register class autoloader.
spl_autoload_register(function ($class) {
    $filename = __DIR__ . '/classes/' . (str_replace('\\', '/', $class)) . '.php';
    if (file_exists($filename)) require($filename);
});

// Add the WP Document Manager actions.
new RCrowt\WPDocMan\Actions();