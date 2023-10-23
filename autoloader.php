<?php


spl_autoload_register(function ($fullyQualifiedClassName) {
    // Base directory
    $baseDir = __DIR__ ;  // Adjust this to your actual base directory

    // Convert namespace to full file path
    $path = $baseDir . '/' . str_replace('\\', '/', $fullyQualifiedClassName) . '.php';
    // echo $path . '<br>'; // To visualise
    if (file_exists($path)) {
        require $path;
    } else {
        echo "File not found: $path";  // TODO Remove before flight
    }
});

