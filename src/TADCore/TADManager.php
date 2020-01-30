<?php
namespace SdotB\TADCore;

use SdotB\TADCore\TADCollection;

/**
 * Classe TADManager, si occupa di parsare stringhe in ingresso e convertirle in una TADCollection
 * 
 * Descrizione *corsiva*
 * 
 */
class TADManager
{
    const DEFAULT_INPUT_TYPE = 'json_stream';
    /* define payload type ['json','array'] only when parsed from parseArray method */
    const PL_TYPE_IN_ARRAY = 'json';
    /* define behaviuor in case of wrong input tipe passed, can be 'error' or 'default' */
    const WRONG_INPUT_TYPE_BEHAVIOUR = 'error';

    private $ay_actions = [];
    private $ay_types = [];
    private $collection;
    private $data_store = [];
    private $inputdata_array = [];
    private $inputdata_json = "";
    private $input_type;
    private $input_types = [
        'json_stream',
        'json',
        'post',
        'array',
    ];
    private $hh = '';
    private $pl = [];
    private $tk = '';
    
    /**
     * new()
     * 
     * @param array $options = [] Define some instance option in runtime
     * 
     * @return void
     */
    function __construct(array $options = [])
    {
        /* check if some input_type was passed and trim it */
        $options['input_type'] = isset($options['input_type']) ? trim((string)$options['input_type']) : '';
        /* if result not empty get input_type passed, otherwise get the default one */
        $this->input_type = !empty($options['input_type']) ? $options['input_type'] : self::DEFAULT_INPUT_TYPE;
        
        if (!in_array($this->input_type,$this->input_types)) {
            switch (self::WRONG_INPUT_TYPE_BEHAVIOUR) {
                case 'default':
                $this->input_type = self::DEFAULT_INPUT_TYPE;
                break;
                case 'error':
                throw new \Exception("wrong input_type specified", 1);
                break;
                default:
                throw new \Exception("illegal WRONG_INPUT_TYPE_BEHAVIOUR", 1);
                break;
            }
        }
        
        if (!empty($options['types']) && is_array($options['types']) ) {
            $this->types($options['types']);
        }
        if (!empty($options['actions']) && is_array($options['actions'])) {
            $this->actions($options['actions']);
        }

        if (isset($options['parse']) && ($options['parse'] === true)) {
            $this->parseInput($this->input_type);
        }
    }

    public function actions(array $actions): array
    {
        $this->ay_actions = array_merge($this->ay_actions, $actions);
        return $this->ay_actions;
    }

    public function types(array $types): array
    {
        $this->ay_types = array_merge($this->ay_types, $types);
        return $this->ay_types;
    }

    public function exportCollection(): array
    {
        $export = [];
        foreach ($this->collection->getCollection() as $key => $value) {
            $export[] = $value->export();
        }
        return $export;
    }

    public function getCollection(): TADCollection
    {
        return $this->collection;
    }

    public function getHh(): string
    {
        return $this->hh;
    }

    public function getPl(): array
    {
        return $this->pl;
    }

    public function getTk(): string
    {
        return $this->tk;
    }

    private function loadHh(): void
    {
        $this->hh = !empty($this->data_store['hh']) ? (string)trim($this->data_store['hh']) : '';
    }

    private function loadPl(): void
    {
        $this->pl = !empty($this->data_store['pl']) ? (array)$this->data_store['pl'] : [];
    }

    private function loadTk(): void
    {
        $this->tk = !empty($this->data_store['tk']) ? (string)trim($this->data_store['tk']) : '';
    }

    private function parseArray(array $inputdata, string $pl_type = self::PL_TYPE_IN_ARRAY): array
    {
        $data = [];
        if (isset($inputdata['tk'])) $data['tk'] = $inputdata['tk'];
        if (isset($inputdata['pl'])) {
            if ($pl_type == 'json') {
                $data['pl'] = $this->parseJson($inputdata['pl']);
            } else {
                $data['pl'] = $inputdata['pl'];
            }
            if (!is_array($data['pl'])) throw new \Exception("wrong format: pl did not result as array", 1);
        }
        if (isset($inputdata['hh'])) $data['hh'] = $inputdata['hh'];
        return $data;
    }

    public function parseInput(string $input_type = ''): void
    {
        $input_type = trim($input_type);
        if (empty($input_type)) $input_type = $this->input_type;
        if (!in_array($input_type,$this->input_types)) {
            switch (self::WRONG_INPUT_TYPE_BEHAVIOUR) {
                case 'default':
                $input_type = self::DEFAULT_INPUT_TYPE;
                break;
                case 'error':
                throw new \Exception("wrong input_type specified", 1);
                break;
                default:
                throw new \Exception("illegal WRONG_INPUT_TYPE_BEHAVIOUR", 1);
                break;
            }
        }
        $this->data_store = [];
        switch ($input_type) {
            case 'json_stream':
            $this->data_store = $this->parseJson($this->phpInputStream());
            break;
            case 'json':
            $this->data_store = $this->parseJson($this->inputdata_json);
            break;
            case 'post':
            $post_data = isset($_POST) ? $_POST : [];
            $this->data_store = $this->parseArray($post_data);
            break;
            case 'array':
            $this->data_store = $this->parseArray($this->inputdata_array);
            break;
            default:
            throw new \Exception("illegal behaviour", 1);
            break;
        }
        // Once fullfilled the data_store check all needed keys are present or throw error
        if (!isset($this->data_store['tk'])) {
            throw new \Exception("missing argument token: tk", 1);
        }
        if (!isset($this->data_store['pl'])) {
            throw new \Exception("missing argument payload: pl", 1);
        }
        if (!isset($this->data_store['hh'])) {
            throw new \Exception("missing argument hmachash: hh", 1);
        }
        
        /* TODO: check hmac hash at this point */
        
        $this->loadTk();
        $this->loadPl();
        $this->loadHh();
        
        $this->collection = new TADCollection();
        
        foreach ($this->pl as $pl_key => $pl_item) {
            $this->collection->setPush(new TAD($pl_item));
        }
    }

    private function parseJson(string $json): array
    {
        $data = json_decode($json, true);
        if ($data == NULL){throw new \Exception("JSON error: ".json_last_error_msg(),0);}
        if (!is_array($data)){throw new \Exception("JSON error: parsed JSON did not result as array",0);}
        return $data;
    }

    private function phpInputStream(): string
    {
        return file_get_contents('php://input');
    }

    private function setHh(string $hh): void
    {
        $this->hh = trim($hh);
    }

    public function setInputDataJson($json): void
    {
        $this->inputdata_json = $json;
    }

    private function setPl(array $pl): void
    {
        $this->pl = $pl;
    }

    private function setTk(string $tk): void
    {
        $this->tk = trim($tk);
    }

    /**
     * runWorker method:
     * receive a TADWorker instance by argument,
     * rotate every TAD of the collection,
     * after checking health, type and action inject information in TADWorker instance
     * set TAD response data "D" parsed by TADWorker or errors
     */
    public function runWorker(abTADWorker $worker)
    {
        foreach ($this->collection as $key => $tad) {
            $tad->setParsingStatus('parsing');
            if($tad->isHealth() === true) {
                if(isset($this->ay_types[$tad->requestedType()])){
                    if (empty($this->ay_actions)) {
                        try {
                            //  Here set worker properties related to single TAD
                            $worker->setAction($tad->requestedAction());
                            $worker->setData($tad->requestedData());
                            $worker->setType($this->ay_types[$tad->requestedType()]);
                            //  Fill TAD's "D" response data with data parsed by worker
                            $tad->parsedData($worker->parseData());
                        } catch (\Throwable $th) {
                            switch ($th->getCode()) {
                                case 1:
                                    $tad->setTWrong($th->getMessage());
                                    break;
                                case 2:
                                    $tad->setAWrong($th->getMessage());
                                    break;
                                default:
                                    print $th->getMessage();
                                    break;
                            }
                        }
                    } else {
                        if (isset($this->ay_actions[$tad->requestedAction()])) {
                            try {
                                //  Here set worker properties related to single TAD
                                $worker->setAction($this->ay_actions[$tad->requestedAction()]);
                                $worker->setData($tad->requestedData());
                                $worker->setType($this->ay_types[$tad->requestedType()]);
                                //  Fill TAD's "D" response data with data parsed by worker
                                $tad->parsedData($worker->parseData());
                            } catch (\Throwable $th) {
                                switch ($th->getCode()) {
                                    case 1:
                                        $tad->setTWrong($th->getMessage());
                                        break;
                                    case 2:
                                        $tad->setAWrong($th->getMessage());
                                        break;
                                    default:
                                        print $th->getMessage();
                                        break;
                                }
                            }
                        } else {
                            $tad->setAWrong();
                        }
                    }
                } else {
                    $tad->setTWrong();
                }
            } else {
                //	Not HEALTH, nothing at the moment
            }
            $tad->setParsingStatus('parsed');
        }
    }
}
