<?php
namespace sdotbertoli\TAD;

use sdotbertoli\Utils\Utils;
use sdotbertoli\TAD\TAD;

/**
 * Classe TADCollection
 */
class TADCollection implements \Iterator, \ArrayAccess, \Countable
{
    protected $first = null;    // WARNING! Keep this always first, supportReset
    private $position = 0;      // Iterator
    private $tads = [];         // Elements container
    private $id = '';

    function __construct($id = '')
    {
        if (empty($id)) $id = Utils::mtRandStr(10);
        $this->set_id($id);
        $this->position = 0;    // Init Iterator
    }

    private function set_id(string $id): void
    {
        $this->id = $id;
    }

    public function get_id(): string
    {
        return $this->id;
    }

    public function get_size(): int
    {
        return count($this->tads);
    }

    public function set_collection(array $tads): int
    {
        foreach ($tads as $tad) {
            $this->set_push(new TAD($tad));
        }
        return $this->get_size();
    }

    public function get_collection(): array
    {
        return $this->tads;
    }

    public function set_push(TAD $tad): int
    {
        return array_push($this->tads,$tad);
    }

    public function set_unshift(TAD $tad): int
    {
        return array_unshift($this->tads,$tad);
    }

    public function get_pop(): TAD
    {
        return array_pop($this->tads);
    }

    public function get_shift(): TAD
    {
        return array_shift($this->tads);
    }

    public function get_first(): TAD
    {
        return reset($this->tads);
    }

    public function get_last(): TAD
    {
        return end($this->tads);
    }

    /**
     * ArrayAccess Methods
     */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->tads[] = $value;
        } else {
            $this->tads[$offset] = $value;
        }
        $this->supportReset();
    }

    public function offsetExists($offset) {
        return isset($this->tads[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->tads[$offset]);
        $this->supportReset();
    }

    public function offsetGet($offset) {
        return isset($this->tads[$offset]) ? $this->tads[$offset] : null;
    }

    private function supportReset() {
        $this->first = reset($this->tads); //Support reset().
    }

    /**
     * Iterator Methods
     */
    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->tads[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset($this->tads[$this->position]);
    }

    /**
     * Countable Methods
     */
    public function count() 
    { 
        return count($this->tads); 
    } 
}
