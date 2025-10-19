<?php
//framework taken from module1/code/6_RestAPI/api/models/Student.php


class art {
    private $name;
    private $size;
    private $artist;
    
 
    public function __construct() {

    }
    

    public function toArray() {
        $out = array();
        $out['name'] = $this->name;
        $out['size'] = $this->size;
        $out['artist'] = $this->artist;
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
    
    public function getartist():mixed {
        return $this->artist;
    }
    
    public function setartist($artist) {
        $this->artist = trim($artist);
    }
    
}
