<?php
namespace sdotbertoli\TAD;

use sdotbertoli\TAD\TADCollection;
use sdotbertoli\TAD\TAD;

/**
 * Classe TADManager, si occupa di parsare stringhe in ingresso e convertirle in una TADCollection
 * 
 * Descrizione *corsiva*
 * 
 */
class TADManager
{
    const DEFAULT_INPUT_TYPE = 'json';
    private $tk = '';
    private $pl = [];
    private $hh = '';
    private $ay_input = [];
    private $instance_default_input_type;
    private $collection;
    private $json_var = "";

    public function set_json_input_var($json) {
        $this->json_var = $json;
    }

    /**
     * new()
     * 
     * @param array $options = [] Define some instance option in runtime
     * 
     * @return void
     */
    function __construct(array $options = [])
    {
        $options['input_type'] = isset($options['input_type']) ? trim((string)$options['input_type']) : '';
        $this->instance_default_input_type = !empty($options['input_type']) ? $options['input_type'] : self::DEFAULT_INPUT_TYPE;

        if (isset($options['parse']) && ($options['parse'] === true)) {
            $this->parse_input($this->instance_default_input_type);
        }
    }

    function parse_input(string $type_input = ''): void
    {
        $type_input = trim($type_input);
        if (empty($type_input)) $type_input = $this->instance_default_input_type;
        $this->ay_input = [];
        switch ($type_input) {
            case 'json':
                $this->ay_input = $this->parse_json($this->parse_phpinput());
                break;
            case 'json_var':
                $this->ay_input = $this->parse_json($this->json_var);
                break;
            case 'post':
                $post_data = isset($_POST) ? $_POST : [];
                $this->ay_input = $this->parse_post($post_data);
                break;
            case 'post_var':
                $post_data = isset($_POST) ? $_POST : [];
                $this->ay_input = $this->parse_post($post_data);
                break;
            default:
                // code...
                break;
        }
        // Procedura: ottegno ay_input in base ai casi, verifico che ci siano tutte le "chiavi" necessarie o restituisco errore
        // Se tutto ok allora (in futuro controllo hmac hash prima di procedere) e popolo tk pl e hh
        if (!isset($this->ay_input['tk'])) {
            throw new \Exception("missing argument token: tk", 1);
        }
        if (!isset($this->ay_input['pl'])) {
            throw new \Exception("missing argument payload: pl", 1);
        }
        if (!isset($this->ay_input['hh'])) {
            throw new \Exception("missing argument hmachash: hh", 1);
        }
        $this->set_tk_from_ay_input();
        $this->set_pl_from_ay_input();
        $this->set_hh_from_ay_input();

        $this->collection = new TADCollection();

        foreach ($this->pl as $pl_key => $pl_item) {
            $this->collection->set_push(new TAD($pl_item));
        }
    }

    private function parse_phpinput(): string
    {
        return file_get_contents('php://input');
    }

    private function parse_json(string $json): array
    {
        $ay_input_data = json_decode($json, true);
        if ($ay_input_data == NULL){throw new \Exception("JSON error: ".json_last_error_msg(),0);}
        if (!is_array($ay_input_data)){throw new \Exception("JSON error: parsed JSON did not result in an array",0);}
        return $ay_input_data;
    }

    private function parse_post(array $post_data): array
    {
        $ay_post = [];
        if (isset($post_data['tk'])) $ay_post['tk'] = $post_data['tk'];
        if (isset($post_data['pl'])) $ay_post['pl'] = $this->parse_json($post_data['pl']);
        if (isset($post_data['hh'])) $ay_post['hh'] = $post_data['hh'];
        return $ay_post;
    }

    private function set_tk(string $tk): void
    {
        $this->tk = trim($tk);
    }

    private function set_tk_from_ay_input()
    {
        $this->tk = !empty($this->ay_input['tk']) ? (string)trim($this->ay_input['tk']) : '';
    }

    private function set_pl(array $pl): void
    {
        $this->pl = $pl;
    }

    private function set_pl_from_ay_input()
    {
        //if (!is_array($this->ay_input['pl'])) throw new \Exception("Wrong format: pl", 1);
        $this->pl = !empty($this->ay_input['pl']) ? (array)$this->ay_input['pl'] : [];
    }

    private function set_hh(string $hh): void
    {
        $this->hh = trim($hh);
    }

    private function set_hh_from_ay_input()
    {
        $this->hh = !empty($this->ay_input['hh']) ? (string)trim($this->ay_input['hh']) : '';
    }

    function get_tk(): string
    {
        return $this->tk;
    }

    function get_pl(): array
    {
        return $this->pl;
    }

    function get_hh(): string
    {
        return $this->hh;
    }

    function get_collection(): TADCollection
    {
        return $this->collection;
    }

    function export_collection(): array
    {
        $export = [];
        foreach ($this->collection->get_collection() as $key => $value) {
            $export[] = $value->filter();
        }
        return $export;
    }
}
