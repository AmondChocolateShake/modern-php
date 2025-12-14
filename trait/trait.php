<?php

//Implement Trait
trait MyTrait{

    public function myMethod(){
        echo 'hello';
    }
}

class MyClass {
    use MyTrait;
}

class YourClass{
		use MyTrait;
}

$myclass = new MyClass;
$myclass->myMethod(); // 'hello'


$yourclass= new YourClass;
$yourclass->myMethod(); // 'hello'