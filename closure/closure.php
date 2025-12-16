<?php

// CLOSURE IS JUST 'OBJECT'



$closure = function($name){
    return sprintf('Hello %s', $name);
};

echo $closure('DongJu'); //Hello DongJu

var_dump($closure);
// Hello DongJuobject(Closure)#1 (4) {
//     ["name"]=>
//     string(80) "{closure: modern-php\closure\closure.php:7}"
//     ["file"]=>
//     string(68) "modern-php\closure\closure.php"
//     ["line"]=>
//     int(7)
//     ["parameter"]=>
//     array(1) {
//       ["$name"]=>
//       string(10) "<required>"
//     }
//   }