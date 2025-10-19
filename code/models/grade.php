<?php
//framework taken from module1/code/6_RestAPI/api/models/Student.php


class grade {
    private $name;
    private $English;
    private $Math;

    private $Social_Studies;
    
    
    public function __construct() {

    }
    
 
    public function toArray() {
        $out = array();
        $out['name'] = $this->name;
        $out['English'] = $this->English;
        $out['Math'] = $this->Math;
        $out['Social_Studies'] = $this->Social_Studies;
        return $out;
    }
    

    
    public function getname() {
        return $this->name;
    }
    
    public function setname($name) {
        $this->name = trim($name);
    }
    
    public function getEnglish(): string {
        return $this->English;
    }
    
    public function setEnglish($English): void {
        $this->English = trim($English);
    }
    
    public function getMath():mixed {
        return $this->Math;
    }
    
    public function setMath($Math) {
        $this->Math = trim($Math);
    }

    public function getSocial_Studies():mixed {
        return $this->Social_Studies;
    }
    
    public function setSocial_Studies($Social_Studies) {
        $this->Social_Studies = trim($Social_Studies);
    }
    
}
