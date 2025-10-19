<?php

//framework taken from module1/code/6_RestAPI/api/models/Student.php
class announcement {
    private $announcement;
    private $location;
    private $date_announced;
   
    public function __construct() {

    }
    

    public function toArray() {
        $out = array();
        $out['announcement'] = $this->announcement;
        $out['location'] = $this->location;
        $out['date_announced'] = $this->date_announced;
        return $out;
    }
    

    
    public function getannouncement() {
        return $this->announcement;
    }
    
    public function setannouncement($announcement) {
        $this->announcement = trim($announcement);
    }
    
    public function getlocation(): string {
        return $this->location;
    }
    
    public function setlocation($location): void {
        $this->location = trim($location);
    }
    
    public function getdate_announced():mixed {
        return $this->date_announced;
    }
    
    public function setdate_announced() {
        $this->date_announced = date('Y-m-d H:i:s');
    }
    
}
