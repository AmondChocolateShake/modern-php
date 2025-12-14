<?php


trait TraitA{
    public function sayHello(){
        echo 'hello';
    }
}

trait TraitB{
    public function sayHello(){
        echo 'bye';
    }
}


class ConflictClass{
    use TraitA;
    use TraitB;
}


$class = new ConflictClass;
$class->sayHello(); // Fatal error: Trait method TraitB::sayHello has not been applied as ConflictClass::sayHello