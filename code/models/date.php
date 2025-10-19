<?php
//framework taken from module1/code/6_RestAPI/api/models/Student.php


class date {
    private $day;
    private $time;
    private $event;
    
 
    public function __construct() {

    }
    
 
    public function toArray() {
        $out = array();
        $out['day'] = $this->day;
        $out['time'] = $this->time;
        $out['event'] = $this->event;
        return $out;
    }
    

    
    public function getday() {
        return $this->day;
    }
    
    public function setday($day) {
        $this->day = trim($day);
    }
    
    public function gettime(): string {
        return $this->time;
    }
    
    public function settime($time): void {
        $this->time = trim($time);
    }
    
    public function getevent():mixed {
        return $this->event;
    }
    
    public function setevent($event) {
        $this->event = trim($event);
    }
    
}
