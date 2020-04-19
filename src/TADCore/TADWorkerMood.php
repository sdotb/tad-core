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
            $this->tad->setTWrong('wrong argument or not implemented');
        }

        $mood_instance = new $requested_class($this->options);

        if(!(in_array("permittedActions",get_class_methods($mood_instance)) && in_array($this->action,$mood_instance->permittedActions()))) {
            $this->tad->setAWrong("wrong action or not permitted");
        }

        $parsed_data = [];
        $parsed_data = $mood_instance->{$this->action}($this->data);
        unset($mood_instance);

        $this->tad->parsedData($parsed_data);

        return $parsed_data;
    }
}