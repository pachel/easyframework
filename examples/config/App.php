<?php



return [
    "APP" => [
        "URL" => "http://localhost/easyframe/examples/",
        "UI" => __DIR__ . "/../UI/",
        "VIEWs" => __DIR__ . "/../UI/views/",
        "LOGS" => __DIR__ . "/../logs/",
        "TEMp" => __DIR__ . "/temp",
        "CACHE_EXPIRES" => 10,
        "TEST" => true
    ],
    "PDBCLASS" => [
        "SERVER" => [
            "host" => "localhost",
            "dbname" => "dbclass_test",
            "charset" => "utf8",
            "username" => "root",
            "password" => "",
            "saveClassDir" => __DIR__ . "/../Models/",
            "modelDir" => __DIR__ . "/../inc/Models/"
        ],
        "OPTIONS"=>[
            \PDO::MYSQL_ATTR_COMPRESS => true,
            \PDO::ATTR_PERSISTENT => false,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES => false
        ]
    ]
];
