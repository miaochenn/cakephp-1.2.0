<?php

class D {
    public function test()
    {
        $miao = self::getAge();
        $name = 'miao';

        return $this->$name;
    }

    public static function getAge()
    {
        return 123;
    }
}

echo (new D())->test();