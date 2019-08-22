<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer

use sdotbertoli\TAD\TADManager;

try {
    $tman = new TADManager;
    $tman->set_json_input_var('{
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

    $tman->parse_input('json_var');
    print json_encode($tman->export_collection());

} catch (\Exception $e) {
    print $e->get_message();
}

