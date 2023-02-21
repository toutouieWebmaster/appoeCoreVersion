<?php

namespace App;
class Media extends File
{

    function __construct()
    {
        parent::__construct();
        $this->type = 'MEDIA';
    }
}