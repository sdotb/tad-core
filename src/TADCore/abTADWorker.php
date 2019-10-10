<?php
namespace SdotB\TADCore;

abstract class abTADWorker implements iTADWorker
{
    protected $action;
    protected $data;
    protected $name;
    protected $options;
    protected $type;

    function __construct(string $name = 'default', array $options = [])
    {
        $this->name = $name;
        $this->options = $options;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    abstract public function parseData();

}