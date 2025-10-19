<?php
//framework taken from module1/code/6_RestAPI/api/models/Student.php


class Stock_ticker {
    private $symbol;
    private $full_name;
    private $market_value;
    private $is_up;
    
  
    public function __construct() {

    }
    
  
    public function toArray() {
        $out = array();
        $out['symbol'] = $this->symbol;
        $out['full_name'] = $this->full_name;
        $out['market_value'] = $this->market_value;
        $out['is_up'] = $this->is_up;
        return $out;
    }
    
    
    public function getSymbol() {
        return $this->symbol;
    }
    
    public function setSymbol($symbol) {
        $this->symbol = trim($symbol);
    }
    
    public function getFull_name(): string {
        return $this->full_name;
    }
    
    public function setFull_name($full_name): void {
        $this->full_name = trim($full_name);
    }
    
    public function getMarket_value():float {
        return $this->market_value;
    }
    
    public function setMarket_value($market_value) {
        $this->market_value = (float)$market_value;
    }

    public function getIs_up():bool {
        return $this->is_up;
    }
    
    public function setIs_up($is_up) {
        $this->is_up = (int)$is_up;
    }
    
}
