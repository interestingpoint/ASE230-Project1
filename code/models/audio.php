<?php
//framework taken from module1/code/6_RestAPI/api/models/Student.php


class audio {
    private $name;
    private $size;
    private $metadata;
    

    public function __construct() {

    }
    

    public function toArray() {
        $out = array();
        $out['name'] = $this->name;
        $out['size'] = $this->size;
        $out['metadata'] = $this->metadata;
        return $out;
    }
    

    
    public function getname() {
        return $this->name;
    }
    
    public function setname($name) {
        $this->name = trim($name);
    }
    
    public function getsize(): string {
        return $this->size;
    }
    
    public function setsize($size): void {
        $this->size = (float)$size;
    }
    
    public function getmetadata():mixed {
        return $this->metadata;
    }
    
    public function setmetadata($metadata) {
        $this->metadata = trim($metadata);
    }
    
}
