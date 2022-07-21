<?php

namespace App\Helpers;

class ModeHelper
{
    private $mode;
    public function __construct()
    {
        $this->mode = 'test';
        // $this->mode = 'prod';
    }

    public function getMode()
    {
        return $this->mode;
    }
}
