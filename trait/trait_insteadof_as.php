<?php


trait TraitA{
    public function sayHello(){
        echo "hello\n";
    }
}


trait TraitB{
    public function sayHello(){
        echo "bye\n";
    }
}


class ConflictClass{
    use TraitA, TraitB{
        TraitB::sayHello insteadOf TraitA;
        TraitA::sayHello as sayHelloA;
        TraitB::sayHello as sayHelloB;
    }
}


$class = new ConflictClass;
$class->sayHello(); //bye
$class->sayHelloA(); //hello
$class->sayHelloB(); //bye