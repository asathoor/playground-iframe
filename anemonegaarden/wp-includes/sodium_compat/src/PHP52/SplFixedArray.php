<?php
 if (class_exists('SplFixedArray')) { return; } class SplFixedArray implements Iterator, ArrayAccess, Countable { private $internalArray = array(); private $size = 0; public function __construct($size = 0) { $this->size = $size; $this->internalArray = array(); } #[\ReturnTypeWillChange]
 public function count() { return count($this->internalArray); } public function toArray() { ksort($this->internalArray); return (array) $this->internalArray; } public static function fromArray(array $array, $save_indexes = true) { $self = new SplFixedArray(count($array)); if($save_indexes) { foreach($array as $key => $value) { $self[(int) $key] = $value; } } else { $i = 0; foreach (array_values($array) as $value) { $self[$i] = $value; $i++; } } return $self; } #[\ReturnTypeWillChange]
 public function getSize() { return $this->size; } #[\ReturnTypeWillChange]
 public function setSize($size) { $this->size = $size; return true; } #[\ReturnTypeWillChange]
 public function offsetExists($index) { return array_key_exists((int) $index, $this->internalArray); } #[\ReturnTypeWillChange]
 public function offsetGet($index) { return $this->internalArray[(int) $index]; } #[\ReturnTypeWillChange]
 public function offsetSet($index, $newval) { $this->internalArray[(int) $index] = $newval; } #[\ReturnTypeWillChange]
 public function offsetUnset($index) { unset($this->internalArray[(int) $index]); } #[\ReturnTypeWillChange]
 public function rewind() { reset($this->internalArray); } #[\ReturnTypeWillChange]
 public function current() { return current($this->internalArray); } #[\ReturnTypeWillChange]
 public function key() { return key($this->internalArray); } #[\ReturnTypeWillChange]
 public function next() { next($this->internalArray); } #[\ReturnTypeWillChange]
 public function valid() { if (empty($this->internalArray)) { return false; } $result = next($this->internalArray) !== false; prev($this->internalArray); return $result; } public function __sleep() { return $this->internalArray; } public function __wakeup() { } public function __serialize() { return array_values($this->internalArray); } public function __unserialize(array $data) { $length = count($data); $values = array_values($data); for ($i = 0; $i < $length; ++$i) { $this->internalArray[$i] = $values[$i]; } $this->size = $length; } } 