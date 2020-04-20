<?php

namespace SdotB\TADCore;

interface iTADWorker
{
    public function __construct();

    public function setAction(string $action);

    public function setActionsResolver(array $actions);

    public function setData(array $data);

    public function setTad(TAD $tad);

    public function setType(string $type);

    public function setTypesResolver(array $types);

    public function parseData();
}
