<?php

namespace SdotB\TADCore;

class TADWorkerMood extends abTADWorker
{
    public function __construct(array $options = [])
    {
        abTADWorker::__construct('mood', $options);
    }

    public function parseData(): array
    {
        $requested_class = 'SdotB\\Mood\\'.$this->type;
        if (!class_exists($requested_class)) {
            throw new Exception("worng argument or not implemented", 1);
        }

        $mood_instance = new $requested_class($this->options);

        if(!(in_array("permittedActions",get_class_methods($mood_instance)) && in_array($this->action,$mood_instance->permittedActions()))) {
            throw new Exception("wrong action or not permitted", 2);
        }

        $parsed_data = [];
        $parsed_data = $mood_instance->{$this->action}($this->data);
        unset($mood_instance);

        return $parsed_data;
    }
}