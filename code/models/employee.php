<?php
//framework taken from module1/code/6_RestAPI/api/models/Student.php


class employee {
    private $name;
    private $tenure;
    private $department;
    

    public function __construct() {

    }
    

    public function toArray() {
        $out = array();
        $out['name'] = $this->name;
        $out['tenure'] = $this->tenure;
        $out['department'] = $this->department;
        return $out;
    }
    

    
    public function getname() {
        return $this->name;
    }
    
    public function setname($name) {
        $this->name = trim($name);
    }
    
    public function gettenure(): string {
        return $this->tenure;
    }
    
    public function setenure($tenure): void {
        $this->tenure = (float)$tenure;
    }
    
    public function getdepartment():mixed {
        return $this->department;
    }
    
    public function setdepartment($department) {
        $this->department = trim($department);
    }
    
}
