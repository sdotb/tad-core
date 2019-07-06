<?php
namespace TAD;

/**
 * Single TAD unit class
 */
class TAD
{
    private $i = '';
    private $t = '';
    private $a = '';
    private $d = [];
    private $key_missing = [];
    private $msg_missing = '';
    private $key_empty = [];
    private $msg_empty = '';
    public $key_wrong = [];
    private $msg_wrong = '';
    private $health = true;
    private $parsing_status = 'toparse';    //  Can be 'toparse','parsing','parsed'

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

        $this->set_i((string)$data['i']);
        $this->set_t((string)$data['t']);
        $this->set_a((string)$data['a']);
        $this->set_d((array)$data['d']);

        $this->health_check();

    }

    /**
     * Fill TAD, not in use at the moment, filled in construct
     */
    private function set($data): bool
    {
        if (empty($data)) return false;
        $this->set_i($data['i']);
        $this->set_t($data['t']);
        $this->set_a($data['a']);
        if (!is_array($data['d'])) $data['d'] = (array)$data['d'];
        $this->set_d($data['d']);
        return true;
    }

    public function health_check(): bool
    {
        $this->health = true;
        if ($this->key_missing != []) {
            $this->msg_missing = "missing arguments: ".implode(',',$this->key_missing);
            $this->health = false;
        } else {
            $this->msg_missing = '';
        }
        if ($this->key_empty != []) {
            $this->msg_empty = "empty arguments: ".implode(',',$this->key_empty);
            $this->health = false;
        } else {
            $this->msg_empty = '';
        }
        if ($this->key_wrong != []) {
            $this->msg_wrong = "wrong arguments: ".implode(',',$this->key_wrong);
            $this->health = false;
        } else {
            $this->msg_wrong = '';
        }
        return $this->health;
    }

    public function is_health(): bool
    {
        return $this->health_check();
    }

    public function filter(array $fields = ['t','a','d']): array
    {
        // TODO: Sporchissimo da rivedere
        /**
         * Metodo per filtrare esportazione TAD,
         * se TAD "sano" esporta solo "i" e "d"
         * altrimenti esporta solo i campi che necessitano di correzione
         */
        if (!$this->health) {
            $keep = array_merge($this->key_missing,$this->key_empty,$this->key_wrong);
        } else {
            $keep = ['d'];
        }
        $response['i'] = $this->get_i();
        foreach ($fields as $field) {
            if (in_array($field,$keep)) {
                switch ($field) {
                    case 't':
                        $response['t'] = $this->get_t();
                        break;
                    case 'a':
                        $response['a'] = $this->get_a();
                        break;
                    case 'd':
                        $response['d'] = $this->get_d();
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        return $response;
    }

    public function set_parsing_status(string $status)
    {
        //  Can be 'toparse','parsing','parsed'
        //  TODO: definire array stati permessi e settare solo se status permesso
        $this->parsing_status = $status;
    }

    /**
     * Restituisce il TAD
     * 
     * @return array TAD
     */
    public function get(): array
    {
        $tad = [];
        $tad['i'] = $this->get_i();
        $tad['t'] = $this->get_t();
        $tad['a'] = $this->get_a();
        $tad['d'] = $this->get_d();
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
        $this->set_i('');
        $this->set_t('');
        $this->set_a('');
        $this->set_d([]);
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
        $this->set_i('');
        $this->set_t('');
        $this->set_a('');
        $this->set_d([]);
        return;
    }

    //	TODO: Implementare nel metodo set_ specifico un flag per settare il messaggio come errore, missing, empty etc etc
    //	ad esempio $this->set_a('messaggio di errore',['type'=>'wrong'])
    //	In questo modo posso gestire una tipologia di contenuto del campo, ad esempio anche se è una request o response,
    //	parsing, parsed etc etc
    public function set_i(string $i): void
    {
        $i = trim($i);
        $this->i = $i;
        if (empty($i)) {
            $this->i = "cannot be empty";
            $this->key_empty[] = 'i';
        }
        return;
    }

    public function set_t(string $t): void
    {
        $t = trim($t);
        $this->t = $t;
        if (empty($t)) {
            $this->t = "cannot be empty";
            $this->key_empty[] = 't';
        }
        return;
    }

    public function set_a(string $a): void
    {
        $a = trim($a);
        $this->a = $a;
        if (empty($a)) {
            $this->a = "cannot be empty";
            $this->key_empty[] = 'a';
        }
        return;
    }

    public function set_d(array $d): void
    {
        $this->d = $d;
        return;
    }

    public function set_d_from_old_mood(array $mood): void
    {
        $this->d = $mood['d'];
        return;
    }

    public function get_i(): string
    {
        return $this->i;
    }

    public function get_t(): string
    {
        return $this->t;
    }

    public function get_a(): string
    {
        return $this->a;
    }

    public function get_d(): array
    {
        return $this->d;
    }
}
