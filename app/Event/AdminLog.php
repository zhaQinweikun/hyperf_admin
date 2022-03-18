<?php


namespace App\Event;


class AdminLog
{
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
}
