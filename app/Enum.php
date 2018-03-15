<?php

namespace App;

abstract class Enum {
    static function getKeys(){
        $class = new \ReflectionClass(get_called_class());
        return array_keys($class->getConstants());
    }
}
