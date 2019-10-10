<?php

namespace MyExampleApp;

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer

/**
 * LOADING TAD Worker
 * 1. through require_once(), not modify composer.json
 * 2. set "MyExampleApp\\": "example" in autoload psr-4 section of composer.json
 */
require_once __DIR__ . '/ExampleWorker.php';

$json_example_data = '{
    "tk": "qazwsx",
    "pl": [{
        "a": "action1",
        "d": [{
            "key": "value1"
        },{
            "key": "value2"
        }],
        "i": "1234",
        "t": "type1"
    }, {
        "a": "action2",
        "d": [{
            "key": "value"
        }],
        "i": "5678",
        "t": "type2"
    }, {
        "a": "action2",
        "d": [{
            "key": "value"
        }],
        "i": "9234",
        "t": "type2"
    }, {
        "a": "action",
        "d": [{
            "key": "value"
        }],
        "i": "6464",
        "t": "type1"
    }, {
        "a": "ac",
        "d1": [{
            "key": "value"
        }],
        "i1": "6464",
        "t": "type1"
    }],
    "hh": ""
}';

use SdotB\TADCore\TADManager;

try {
    $tman = new TADManager;

    $tman->setInputDataJson($json_example_data);

    $tman->parseInput('json');

    $ay_types = [
		"type1"=>"type1",
		"type2"=>"type2",
    ];

    $tman->types($ay_types);
    
    $ay_actions = [
		"action1"=>"action1",
		"action2"=>"action2",
    ];
    
    $tman->actions($ay_actions);

    $tman->runWorker(new ExampleWorker());

	$response = $tman->exportCollection();
    
    print json_encode($response);

} catch (\Throwable $th) {
    var_dump($th);
    print $th->getMessage();
}

