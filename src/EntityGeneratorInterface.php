<?php

namespace EcomDev\PHPSpec\MagentoDiAdapter;

interface EntityGeneratorInterface
{
    public function supports($className);

    public function generate($className);
}
