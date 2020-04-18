<?php

namespace SdotB\TADCore;

abstract class abTADWorker implements iTADWorker
{
    protected $action;
    protected $data;
    protected $name;
    protected $options;
    protected $type;

    public function __construct(string $name = 'default', array $options = [])
    {
        $this->name = $name;
        $this->options = $options;
    }

    public function setAction(string $action): iTADWorker
    {
        $this->action = $action;

        return $this;
    }

    public function setData(array $data): iTADWorker
    {
        $this->data = $data;

        return $this;
    }

    public function setType(string $type): iTADWorker
    {
        $this->type = $type;

        return $this;
    }

    abstract public function parseData();
}
