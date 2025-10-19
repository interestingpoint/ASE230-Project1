<?php
//framework taken from module1/code/6_RestAPI/api/models/Student.php


class homework {
    private $name;
    private $questions;
    private $class;
    

    public function __construct() {

    }
    

    public function toArray() {
        $out = array();
        $out['name'] = $this->name;
        $out['questions'] = $this->questions;
        $out['class'] = $this->class;
        return $out;
    }
    
    
    public function getname() {
        return $this->name;
    }
    
    public function setname($name) {
        $this->name = trim($name);
    }
    
    public function getquestions(): string {
        return $this->questions;
    }
    
    public function setquestions($questions): void {
        $this->questions = (float)$questions;
    }
    
    public function getclass():mixed {
        return $this->class;
    }
    
    public function setclass($class) {
        $this->class = trim($class);
    }
    
}
