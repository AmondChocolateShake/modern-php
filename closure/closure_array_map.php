<?php


/* book example */
$numberPlusOne = array_map(function($number){
    return $number + 1;
},[1,2,3]);

print_r($numberPlusOne);
// Array
// (
//     [0] => 2
//     [1] => 3
//     [2] => 4
// )