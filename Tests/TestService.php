<?php

namespace Tests;

class TestService
{
    public function getParameter()
    {
        return 'value';
    }

    public function getParameterWithArguments($arg1, $arg2)
    {
        return $arg1.$arg2;
    }
}
