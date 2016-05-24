<?php

namespace spec\EcomDev\PHPSpec\MagentoDiAdapter\Fixture;

class Catcher
{
    public function invoke($args)
    {
        return $args;
    }
}
