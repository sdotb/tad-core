<?php
namespace SdotB\TADCore;

use SdotB\TADCore\TAD;
use SdotB\Utils\Utils;

/**
 * Classe TADCollection
 */
class TADCollection implements \Iterator, \ArrayAccess, \Countable
{
    protected $first = null;    // WARNING! Keep this always first, supportReset
    private $id = '';
    private $position = 0;      // Iterator
    private $tads = [];         // Elements container

    function __construct($id = '')
    {
        if (empty($id)) $id = Utils::randStr(10);
        $this->setId($id);
        $this->position = 0;    // Init Iterator
    }

    private function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSize(): int
    {
        return count($this->tads);
    }

    public function setCollection(array $tads): int
    {
        foreach ($tads as $tad) {
            $this->setPush(new TAD($tad));
        }
        return $this->getSize();
    }

    public function getCollection(): array
    {
        return $this->tads;
    }

    public function setPush(TAD $tad): int
    {
        return array_push($this->tads,$tad);
    }

    public function setUnshift(TAD $tad): int
    {
        return array_unshift($this->tads,$tad);
    }

    public function getPop(): TAD
    {
        return array_pop($this->tads);
    }

    public function getShift(): TAD
    {
        return array_shift($this->tads);
    }

    public function getFirst(): TAD
    {
        return reset($this->tads);
    }

    public function getLast(): TAD
    {
        return end($this->tads);
    }

    /**
     * ArrayAccess Methods
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->tads[] = $value;
        } else {
            $this->tads[$offset] = $value;
        }
        $this->supportReset();
    }

    public function offsetExists($offset): bool
    {
        return isset($this->tads[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->tads[$offset]);
        $this->supportReset();
    }

    public function offsetGet($offset): ?TAD
    {
        return isset($this->tads[$offset]) ? $this->tads[$offset] : null;
    }

    private function supportReset(): void
    {
        $this->first = reset($this->tads); //Support reset().
    }

    /**
     * Iterator Methods
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): TAD
    {
        return $this->tads[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->tads[$this->position]);
    }

    /**
     * Countable Methods
     */
    public function count(): int
    { 
        return count($this->tads); 
    } 
}
