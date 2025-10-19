<?php
//framework taken from module1/code/6_RestAPI/api/models/Student.php


class candidate {
    private $day;
    private $time;
    private $event;
    
 
    public function __construct() {

    }
    
 
    public function toArray() {
        $out = array();
        $out['name'] = $this->day;
        $out['votes'] = $this->time;
        $out['position'] = $this->event;
        return $out;
    }
    

    
    public function getname() {
        return $this->day;
    }
    
    public function setname($name) {
        $this->day = trim($name);
    }
    
    public function getvotes(): string {
        return $this->time;
    }
    
    public function setvotes($votes): void {
        $this->time = (float)$votes;
    }
    
    public function getposition():mixed {
        return $this->event;
    }
    
    public function setposition($position) {
        $this->event = trim($position);
    }
    
}
