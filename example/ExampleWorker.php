<?php

namespace MyExampleApp;

use SdotB\TADCore\abTADWorker;

class ExampleWorker extends abTADWorker
{
    public function __construct(array $options = [])
    {
        abTADWorker::__construct('example', $options);
    }

    public function parseData(): array
    {
        $parsed_data = [];
        foreach ($this->data as $key => $value) {
            $parsed_data[] = ["data" => $value, "status" => "OK"];
        }
        return $parsed_data;
    }
}