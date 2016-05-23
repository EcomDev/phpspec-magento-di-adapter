<?php

namespace spec\EcomDev\PHPSpec\MagentoDiAdapter\Runner;

use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\ObjectBehavior;
use PhpSpec\Runner\Maintainer\MaintainerInterface;
use Prophecy\Argument;

class CollaboratorMaintainerSpec extends ObjectBehavior
{
    function it_should_implement_maintainer_interface()
    {
        $this->shouldImplement(MaintainerInterface::class);
    }

    function it_should_support_any_example_node(ExampleNode $example)
    {
        $this->supports($example)->shouldReturn(true);
    }

    
}
