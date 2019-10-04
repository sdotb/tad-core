<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer

use SdotB\TADCore\TADManager;

try {
    $tman = new TADManager;
    $tman->setInputDataJson('
    {
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
    }
    ');

    $tman->parseInput('json');

    $ay_types = [
		"type1"=>"type1",
		"type2"=>"type2",
    ];
    
    $ay_actions = [
		"action1"=>"action1",
		"action2"=>"action2",
	];

    foreach ($tman->getCollection() as $key => $tad) {
		$tad->setParsingStatus('parsing');
		if($tad->isHealth() === true) {
            print $tad->getI()." health:  ";
			$type = $tad->getTReq();
			$action = $tad->getAReq();
            $data = $tad->getDReq();
            var_dump($type,$action,$data);
            print "<br><br>";
            // Here start Mood Implementation
			if(isset($ay_types[$type])){
                //$debugInfo[':getTk'] = $tman->getTk();
                //$debugInfo[':type'] = $type;
                if (isset($ay_actions[$action])) {
                    foreach ($tad->getDReq() as $key => $value) {
                        $tad->work($value);
                    }
                    $tad->setParsingStatus('parsed');
                } else {
                    $tad->setAWrong();
                    $tad->setParsingStatus('parsed');
                }
			} else {
				$tad->setTWrong();
				$tad->setParsingStatus('parsed');
			}
		} else {
            print $tad->getI()." not health<br><br>";
            $tad->setParsingStatus('parsed');
			//	Nothing at the moment
		}
	}
	$response = $tman->exportCollection();
    
    print json_encode($tman->exportCollection());
    
    
} catch (\Throwable $th) {
    var_dump($th);
    print $th->getMessage();
}

