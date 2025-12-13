<?php

// Belonged to namespace App\Class
namespace App\Class;

//Should be loaded on memory to use global namespace class.
require '../../global-namespace.php';

Class MyClass{

    public function hello(){
        echo 'bye';
    }

}


//reference class in sub namespace 'App\Class'
$myclass = new Myclass;
$myclass->hello();

//reference class in global namespace.
$myclass = new \Myclass;
$myclass->hello();

