<?php

namespace SdotB\TADCore;

interface iTADWorker
{
    public function __construct();

    public function setAction(string $action);

    public function setData(array $data);

    public function setType(string $type);

    public function parseData();
}
