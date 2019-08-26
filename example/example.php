<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer

use TADCore\TADManager;

try {
    $tman = new TADManager;
    $tman->setInputDataJson('
    {
        "tk": "qazwsx",
        "pl": [{
            "a": "action",
            "d": [{
                "key": "value"
            }],
            "i": "1234",
            "t": "type"
        }, {
            "az": "bad",
            "d": [{
                "key": "value"
            }],
            "i": "5678",
            "t": "type"
        }],
        "hh": ""
    }
    ');

    $tman->parseInput('json');
    print json_encode($tman->exportCollection());

} catch (\Exception $e) {
    print $e->getMessage();
}

