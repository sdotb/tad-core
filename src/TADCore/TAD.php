<?php
declare(strict_types=1);
namespace SdotB\TADCore;

/**
 * TAD Entity.
 * 
 * The TAD entity have all mothods to load, parse, validate, export data
 */
class TAD
{
    private $is_health = false;
    private $key_empty = [];
    private $key_missing = [];
    private $key_wrong = [];
    private $key_wrong_type = [];
    private $parsing_status = 'toparse';    //  Can be 'toparse','parsing','parsed'
    private $req_a;
    private $req_d;
    private $req_i;
    private $req_t;
    private $res_a;
    private $res_d;
    private $res_i;
    private $res_t;

    public function __construct(array $data = [])
    {
        $this->load($data);
    }

    public function load(array $data): void
    {
        if (isset($data['i'])) {
            $this->setI($data['i']);
        }
        if (isset($data['t'])) {
            $this->setT($data['t']);
        } else {
            $this->key_missing[] = 't';
            $this->res_t = "missing argument type";
        }
        if (isset($data['a'])) {
            $this->setA($data['a']);
        } else {
            $this->key_missing[] = 'a';
            $this->res_a = "missing argument action";
        }
        if (isset($data['d'])) {
            $this->setD($data['d']);
        }
        $this->healthCheck();
    }

    protected function healthCheck(): bool
    {
        $this->is_health = false;

        if ($this->key_missing == [] and $this->key_empty == [] and $this->key_wrong_type == [] and $this->key_wrong == []) {
            $this->is_health = true;
        }
        
        return $this->is_health;
    }

    public function isHealth(): bool
    {
        return $this->healthCheck();
    }

    public function export(): array
    {
        return $this->filter();
    }

    /**
     * Filter TAD export,
     * on "health" TAD export only "i" and "d" if present in $fields argument
     * otherwise "i" if evaluated and any wrong fields matching $fields argument
     */
    public function filter(array $fields = ['i','t','a','d']): array
    {
        $response = [];
        $this->isHealth();
        if ($this->is_health) {
            $keep = ['d','i'];
        } else {
            $keep = array_merge(['i'],$this->key_missing,$this->key_empty,$this->key_wrong_type,$this->key_wrong);
        }
        foreach ($fields as $field) {
            if (in_array($field,$keep)) {
                switch ($field) {
                    case 'i':
                        if (!empty($this->responseId())) $response['i'] = $this->responseId();
                        break;
                    case 't':
                        if (!empty($this->responseType())) $response['t'] = $this->responseType();
                        break;
                    case 'a':
                        if (!empty($this->responseAction())) $response['a'] = $this->responseAction();
                        break;
                    case 'd':
                        if (!empty($this->responseData())) $response['d'] = $this->responseData();
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        return $response;
    }

    public function setParsingStatus(string $status)
    {
        //  Can be 'toparse','parsing','parsed'
        //  TODO: define permitted status array
        $this->parsing_status = $status;
    }

    /**
     * Get the TAD
     * 
     * @return array TAD
     */
    public function response(): array
    {
        $tad = [];
        if (!empty($this->responseId())) $tad['i'] = $this->responseId();
        if (!empty($this->responseType())) $tad['t'] = $this->responseType();
        if (!empty($this->responseAction())) $tad['a'] = $this->responseAction();
        if (!empty($this->responseData())) $tad['d'] = $this->responseData();
        return $tad;
    }

    public function requestedAction(): ?string
    {
        return $this->req_a;
    }

    public function requestedData(): ?array
    {
        return $this->req_d;
    }

    public function requestedId(): ?string
    {
        return $this->req_i;
    }

    public function requestedType(): ?string
    {
        return $this->req_t;
    }

    public function responseAction(): ?string
    {
        return $this->res_a;
    }
    
    public function responseData(): ?array
    {
        return $this->res_d;
    }

    public function responseId(): ?string
    {
        return $this->res_i;
    }

    public function responseType(): ?string
    {
        return $this->res_t;
    }

    public function setA($a): void
    {
        try {
            $this->setAStrict($a);
        } catch (\TypeError $te) {
            $this->key_wrong_type[] = 'a';
            $this->res_a = $te->getMessage();
        } finally {
            $this->healthCheck();
        }
    }

    protected function setAStrict(string $a): void
    {
        $a = trim($a);
        $this->req_a = $a;
        if (empty($this->req_a)) {
            $this->key_empty[] = 'a';
            $this->res_a = "cannot be empty";
        }
    }

    public function setD($d): void
    {
        try {
            $this->setDStrict($d);
        } catch (\TypeError $te) {
            $this->key_wrong_type[] = 'd';
            $this->res_d = $te->getMessage();
        } finally {
            $this->healthCheck();
        }
    }
    
    protected function setDStrict(array $d): void
    {
        $this->req_d = $d;
    }

    public function setI($i): void
    {
        try {
            $this->setIStrict($i);
        } catch (\TypeError $te) {
            $this->key_wrong_type[] = 'i';
            $this->res_i = $te->getMessage();
        } finally {
            $this->healthCheck();
        }
    }

    protected function setIStrict(string $i): void
    {
        $i = trim($i);
        $this->req_i = $i;
        $this->res_i = $this->req_i;
    }

    public function setT($t): void
    {
        try {
            $this->setTStrict($t);
        } catch (\TypeError $te) {
            $this->key_wrong_type[] = 't';
            $this->res_t = $te->getMessage();
        } finally {
            $this->healthCheck();
        }
    }
        
    protected function setTStrict(string $t): void
    {
        $t = trim($t);
        $this->req_t = $t;
        if (empty($this->req_t)) {
            $this->key_empty[] = 't';
            $this->res_t = "cannot be empty";
        }
    }

    public function setAWrong(string $msg = 'wrong action or not permitted'): void
    {
        $this->key_wrong[] = 'a';
        $this->res_a = $msg;
        $this->healthCheck();
    }

    public function setTWrong(string $msg = 'wrong type or not permitted'): void
    {
        $this->key_wrong[] = 't';
        $this->res_t = $msg;
        $this->healthCheck();
    }

    public function parsedData(array $d): void
    {
        $this->res_d = $d;
    }

}
