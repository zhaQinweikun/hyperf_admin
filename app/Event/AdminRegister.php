<?php


namespace App\Event;


class AdminRegister
{
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
}
