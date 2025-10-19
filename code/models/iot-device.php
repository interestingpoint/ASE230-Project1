<?php
//framework taken from module1/code/6_RestAPI/api/models/Student.php


class Iot_device {
    private $type;
    private $description;
    private $online;
    
  
    public function __construct() {

    }
    

    public function toArray() {
        $out = array();
        $out['type'] = $this->type;
        $out['description'] = $this->description;
        $out['online'] = $this->online;
        return $out;
    }
    
    
    public function getType() {
        return $this->type;
    }
    
    public function setType($type) {
        $this->type = trim($type);
    }
    
    public function getDescription(): string {
        return $this->description;
    }
    
    public function setDescription($description): void {
        $this->description = trim($description);
    }
    
    public function getOnline():bool {
        return $this->online;
    }
    
    public function setOnline($online) {
        $this->online = (int)$online;
    }
    
}
