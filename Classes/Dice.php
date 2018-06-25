<?php

class Dice
{
    private $_size;

    public function __construct($size) {
        $this->_size = $size;
    }

    public static function Create ($size) {
        return new Dice($size);
    }

    public function getSize() :int {
        return $this->_size;
    }

    public function getSideStat() {
        $array = [];
        for( $i = 1 ; $i <= $this->_size ; $i++){
            $array[$i] = 1;
        }
        return $array;
    }

    public function getChanceToBeat($goal, $reverse = false) {
        return Dice::ChanceToBeat($this->_size, $goal, $reverse);
    }

    public static function ChanceToBeat ($size, $goal, $reverse = false) {
        if(( $goal < 0 && !$reverse ) || ( $goal > $size && $reverse ))
            return 0;

        if(( $goal < 0 && $reverse ) || ( $goal > $size && !$reverse ))
            return 1;

        if($reverse)
            return $goal / $size;
        else
            return (1 - ($goal / $size));
    }

}