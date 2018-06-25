<?php

function firstk($array){
    reset($array);
    return key($array);
};

function endk($array){
    end($array);
    return key($array);
};

function print_t( $array, $percent = false ){
    $line_t = "";
    $line_b = "";

    foreach($array as $key => $val){
        $line_t .= "<td>$key</td>";
        if ($percent){
            $val = round($val*100, 2).'%';
        }
        $line_b .= "<td>$val</td>";
    }
    echo"<table><tr>$line_t</tr><tr>$line_b</tr></table>";
}

function print_l( $data ) {
    print_r( $data );
    echo "\n";
}