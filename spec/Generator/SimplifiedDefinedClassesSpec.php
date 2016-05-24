<?php

namespace spec\EcomDev\PHPSpec\MagentoDiAdapter\Generator;

use Magento\Framework\Code\Generator\DefinedClasses;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SimplifiedDefinedClassesSpec extends ObjectBehavior
{
    function it_is_extended_from_defined_classes()
    {
        $this->shouldHaveType(DefinedClasses::class);
    }
    
    function it_validates_existing_classes_correctly()
    {
        $this->isClassLoadableFromDisc('spec\EcomDev\PHPSpec\MagentoDiAdapter\Fixture\ValidInterface')
            ->shouldReturn(true);

        $this->isClassLoadableFromDisc('spec\EcomDev\PHPSpec\MagentoDiAdapter\Fixture\ValidClass')
            ->shouldReturn(true);

        $this->isClassLoadableFromDisc('spec\EcomDev\PHPSpec\MagentoDiAdapter\Fixture\NotExistingInterface')
            ->shouldReturn(false);

        $this->isClassLoadableFromDisc('spec\EcomDev\PHPSpec\MagentoDiAdapter\Fixture\NotExistingClass')
            ->shouldReturn(false);
    }
}
