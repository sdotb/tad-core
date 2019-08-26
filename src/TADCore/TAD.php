<?php
namespace TADCore;

/**
 * Single TAD unit class
 */
class TAD
{
    private $a = '';
    private $d = [];
    private $is_health = true;
    private $key_empty = [];
    private $key_missing = [];
    private $key_wrong = [];
    private $i = '';
    private $msg_empty = '';
    private $msg_missing = '';
    private $msg_wrong = '';
    private $parsing_status = 'toparse';    //  Can be 'toparse','parsing','parsed'
    private $t = '';

    public function __construct(array $data = [])
    {
        if (!isset($data['i'])) {
            $this->key_missing[] = 'i';
            $data['i'] = "missing argument id";
        }
        if (!isset($data['t'])) {
            $this->key_missing[] = 't';
            $data['t'] = "missing argument type";
        }
        if (!isset($data['a'])) {
            $this->key_missing[] = 'a';
            $data['a'] = "missing argument action";
        }
        if (!isset($data['d'])) {
            $this->key_missing[] = 'd';
            $data['d'] = "missing argument data";
        }

        $this->setI((string)$data['i']);
        $this->setT((string)$data['t']);
        $this->setA((string)$data['a']);
        $this->setD((array)$data['d']);

        $this->healthCheck();

    }

    public function healthCheck(): bool
    {
        $this->is_health = true;
        if ($this->key_missing != []) {
            $this->msg_missing = "missing arguments: ".implode(',',$this->key_missing);
            $this->is_health = false;
        } else {
            $this->msg_missing = '';
        }
        if ($this->key_empty != []) {
            $this->msg_empty = "empty arguments: ".implode(',',$this->key_empty);
            $this->is_health = false;
        } else {
            $this->msg_empty = '';
        }
        if ($this->key_wrong != []) {
            $this->msg_wrong = "wrong arguments: ".implode(',',$this->key_wrong);
            $this->is_health = false;
        } else {
            $this->msg_wrong = '';
        }
        return $this->is_health;
    }

    public function isHealth(): bool
    {
        return $this->healthCheck();
    }

    public function filter(array $fields = ['i','t','a','d']): array
    {
        /**
         * Filter TAD export,
         * on "health" TAD export only "i" and "d"
         * otherwise "i" and wrong fields
         */
        if (!$this->is_health) {
            $keep = array_merge(['i'],$this->key_missing,$this->key_empty,$this->key_wrong);
        } else {
            $keep = ['d','i'];
        }
        foreach ($fields as $field) {
            if (in_array($field,$keep)) {
                switch ($field) {
                    case 'i':
                        $response['i'] = $this->getI();
                        break;
                    case 't':
                        $response['t'] = $this->getT();
                        break;
                    case 'a':
                        $response['a'] = $this->getA();
                        break;
                    case 'd':
                        $response['d'] = $this->getD();
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
    public function get(): array
    {
        $tad = [];
        $tad['i'] = $this->getI();
        $tad['t'] = $this->getT();
        $tad['a'] = $this->getA();
        $tad['d'] = $this->getD();
        return $tad;
    }

    /**
     * Svuota campi del TAD
     * 
     * @param array $fields TODO: definire che campi svuotare, lascia invariato il resto
     * @return void
     */
    public function empty(array $fields = []): void
    {
        if (!empty($fields)) {
            # code...
        }
        $this->setI('');
        $this->setT('');
        $this->setA('');
        $this->setD([]);
        return;
    }

    /**
     * Mantiene campi del TAD
     * 
     * @param array $fields TODO: definire che campi mantenere, svuota il resto
     */
    public function keep(array $fields = []): void
    {
        if (!empty($fields)) {
            # code...
        }
        $this->setI('');
        $this->setT('');
        $this->setA('');
        $this->setD([]);
        return;
    }

    //	TODO: Implementare nel metodo set_ specifico un flag per settare il messaggio come errore, missing, empty etc etc
    //	ad esempio $this->setA('messaggio di errore',['type'=>'wrong'])
    //	In questo modo posso gestire una tipologia di contenuto del campo, ad esempio anche se è una request o response,
    //	parsing, parsed etc etc
    public function setI(string $i): void
    {
        $i = trim($i);
        $this->i = $i;
        if (empty($i)) {
            $this->i = "cannot be empty";
            $this->key_empty[] = 'i';
        }
        return;
    }

    public function setT(string $t): void
    {
        $t = trim($t);
        $this->t = $t;
        if (empty($t)) {
            $this->t = "cannot be empty";
            $this->key_empty[] = 't';
        }
        return;
    }

    public function setA(string $a): void
    {
        $a = trim($a);
        $this->a = $a;
        if (empty($a)) {
            $this->a = "cannot be empty";
            $this->key_empty[] = 'a';
        }
        return;
    }

    public function setD(array $d): void
    {
        $this->d = $d;
        return;
    }

    public function setDFromMood(array $mood): void
    {
        $this->d = $mood['d'];
        return;
    }

    public function getI(): string
    {
        return $this->i;
    }

    public function getT(): string
    {
        return $this->t;
    }

    public function getA(): string
    {
        return $this->a;
    }

    public function getD(): array
    {
        return $this->d;
    }
}
