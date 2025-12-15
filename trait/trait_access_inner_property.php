<?php

trait ProfileTrait{
    public function getProfileInfo(){
        $info = "Name : ".$this->name."\n";

        $info .= "Age : ".$this->getAge()."\n";
        return $info;
    }
}

class Man{

    use ProfileTrait;
    protected $name = 'dongju';
    protected $age = 26;


    public function getAge(){
        return $this->age;
    }
}

$man = new Man;

$result = $man->getProfileInfo();

echo $result;