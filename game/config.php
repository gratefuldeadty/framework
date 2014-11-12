<?php

// Default Elitewars RPG Configuration File 
// @return array

return new GameConfig([
    'database' => [
        'adapter' => 'Mysql',
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname' => 'dbname',
    ],
    'application' => [
        'controllerDir' => __DIR__ . '/../controllers/',
        'modelsDir' => __DIR__ . '/../models/',
        'viewsDir' => __DIR__ . '/../views/',
        'libraryDir' => __DIR__ . '/../library/',
        'pluginsDir' => __DIR__ . '/../plugin/',
        'baseUri' => '/'
    ]
]);
