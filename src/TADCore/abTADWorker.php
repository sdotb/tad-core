<?php

namespace SdotB\TADCore;

abstract class abTADWorker implements iTADWorker
{
    protected $action;
    protected $actionsResolver;
    protected $data;
    protected $name;
    protected $options;
    protected $tad;
    protected $type;
    protected $typesResolver;

    public function __construct(string $name = 'default', array $options = [])
    {
        $this->name = $name;
        $this->options = $options;
    }

    /**
     * Action Setter.
     * 
     * If match with a resolver name will translate the action
     * otherwise just set the action name as is 
     */
    public function setAction(string $action): iTADWorker
    {
        if (!empty($this->actionsResolver[$action])) {
            $this->action = $this->actionsResolver[$action];
        } else {
            $this->action = $action;
        }

        return $this;
    }

    /**
     * Actions name resolver.
     * 
     * Expect a key => value array to resolve action names
     * i.e. "MyFrontendCreateName" => "create"
     */
    public function setActionsResolver(array $actionsResolver): iTADWorker
    {
        $this->actionsResolver = $actionsResolver;

        return $this;
    }

    public function setData(?array $data): iTADWorker
    {
        $this->data = $data ?? [];

        return $this;
    }

    public function setTad(TAD $tad): iTADWorker
    {
        $this->tad = $tad;
        $this->setAction($this->tad->requestedAction());
        $this->setData($this->tad->requestedData());
        $this->setType($this->tad->requestedType());

        return $this;
    }

    /**
     * Type Setter.
     * 
     * If match with a resolver name will translate the type
     * otherwise just set the type name as is 
     */
    public function setType(string $type): iTADWorker
    {
        if (!empty($this->typesResolver[$type])) {
            $this->type = $this->typesResolver[$type];
        } else {
            $this->type = $type;
        }

        return $this;
    }

    /**
     * Types name resolver.
     * 
     * Expect a key => value array to resolve type names
     * i.e. "User" => "SdotB\\Mood\\User"
     */
    public function setTypesResolver(array $typesResolver): iTADWorker
    {
        $this->typesResolver = $typesResolver;

        return $this;
    }

    abstract public function parseData();
}
